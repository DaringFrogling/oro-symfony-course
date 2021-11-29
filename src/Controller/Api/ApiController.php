<?php

namespace App\Controller\Api;

use App\Dto\NotifyRequestDto;
use App\Services\SendNotificationService;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\{Annotation\Route, Generator\UrlGeneratorInterface};
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApiController
{
    /**
     * Returns all resources of application.
     *
     * @Route("/api", methods="GET", name="list_of_resources")
     *
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return JsonResponse
     */
    public function actionIndex(
        UrlGeneratorInterface $urlGenerator,
    ): JsonResponse {
        $data = $this->getDataFromFile();

        return new JsonResponse($this->generateUrls($data, $urlGenerator));
    }

    /**
     * Returns information about client and writes it to log file.
     *
     * @Route("/api/my-info", methods="GET", name="client_info")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function actionClientInfo(Request $request): JsonResponse
    {
        $clientInfo = [
            'ip' => $request->getClientIps()[0],
            'language' => $request->getPreferredLanguage(),
            'browser' => $request->headers->get('User-Agent')
        ];
        $encoded = json_encode($clientInfo);

        file_put_contents('logs.txt', $encoded . PHP_EOL, FILE_APPEND | LOCK_EX);

        return new JsonResponse($clientInfo);
    }

    /**
     * Creates notification though specified channel.
     *
     * @Route("/api/notify", methods="POST", name="notify")
     *
     * @param Request $request
     * @param SendNotificationService $sendNotificationService
     * @return JsonResponse
     */
    public function actionNotify(
        Request $request,
        SendNotificationService $sendNotificationService,
    ): JsonResponse {
        $data = $request->request->all();
        $this->validateWithThrowsException($data);
        $sendNotificationService->notify($this->createNotifyDto($data));

        return new JsonResponse();
    }

    /**
     * Returns all resources of application.
     *
     * @Route("/api/{product}", methods="GET", name="product_id")
     *
     * @param string $product
     * @param UrlGeneratorInterface $urlGenerator
     * @param TranslatorInterface $translator
     *
     * @return JsonResponse
     */
    public function actionProduct(
        string $product,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
    ): JsonResponse {
        $data = $this->getDataFromFile();
        $urls = $this->generateUrls($data, $urlGenerator);
        $neededProducts = array_filter($urls, fn ($item) => str_contains($item, $product));

        if (empty($neededProducts)) {
            throw new NotFoundHttpException();
        }

        $neededProduct = $data[key($neededProducts)];
        $translatedKeys = $this->translate($this->getTranslateIds(), $translator);

        return new JsonResponse(array_combine($translatedKeys, $neededProduct));
    }

    /**
     * Gets data from file without header line.
     *
     * @return array
     */
    private function getDataFromFile(): array
    {
        $products = array_map('str_getcsv', file('./products.csv'));
        array_shift($products);

        return $products;
    }

    /**
     * Returns formatted list of resources.
     *
     * @param array $data
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return array
     */
    private function generateUrls(array $data, UrlGeneratorInterface $urlGenerator): array
    {
        return array_map(
            fn($item) => $urlGenerator->generate(
                'product_id',
                [
                    'product' => $this->formatUri($item['0'])
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $data
        );
    }

    /**
     * Formats product URI.
     *
     * @param string $product
     *
     * @return string
     */
    private function formatUri(string $product): string
    {
        $erasePosition = mb_strpos($product, '_');
        $productName = mb_substr($product, 0, $erasePosition);
        $productIdSubStr = str_split(mb_substr($product, $erasePosition));
        $productId = [];

        foreach ($productIdSubStr as $symbol) {
            if ((int)$symbol >= 1) {
                $productId[] = $symbol;
            } elseif (!empty($productId)) {
                $productId[] = $symbol;
            }
        }

        return $productName . implode($productId);
    }

    /**
     * Translates keys of the Product object.
     *
     * @param array $translateIdentifiers
     * @param $translator
     *
     * @return array
     */
    private function translate(array $translateIdentifiers, $translator) : array
    {
        return array_map(fn(string $identifier) => $translator->trans($identifier), $translateIdentifiers);
    }

    /**
     * Gets identifiers for the translatable keys.
     *
     * @return string[]
     */
    private function getTranslateIds() : array
    {
        return  ['product.article', 'product.name', 'product.description'];
    }

    /**
     * Validates request data.
     *
     * @param $data
     * @throws UnprocessableEntityHttpException
     */
    private function validateWithThrowsException($data)
    {
        if (
            !array_key_exists('receiver', $data)
            || !array_key_exists('message', $data)
            || !array_key_exists('channel', $data)
        ) {
            throw new UnprocessableEntityHttpException('Request data is not valid');
        }
    }

    /**
     * Creates NotifyRequestDto.
     *
     * @param array $data
     * @return NotifyRequestDto
     */
    private function createNotifyDto(array $data): NotifyRequestDto
    {
        return new NotifyRequestDto(
            $data['receiver'],
            $data['message'],
            $data['channel'],
        );
    }

}
