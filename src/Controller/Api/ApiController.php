<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpKernel\Exception\{NotFoundHttpException, UnsupportedMediaTypeHttpException};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApiController
{
    /**
     * Returns all resources of application.
     *
     * @Route("/api", methods="GET", name="list_of_resources")
     *
     * @param Request $request
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return JsonResponse
     */
    public function actionIndex(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
    ): JsonResponse {
        $this->validateRequestContentType($request);
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
        $this->validateRequestContentType($request);
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
     * Returns all resources of application.
     *
     * @Route("/api/{product}", methods="GET", name="product_id")
     *
     * @param Request $request
     * @param UrlGeneratorInterface $urlGenerator
     * @param string $product
     *
     * @return JsonResponse
     */
    public function actionProduct(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        string $product
    ): JsonResponse {
        $this->validateRequestContentType($request);
        $data = $this->getDataFromFile();
        $urls = $this->generateUrls($data, $urlGenerator);
        $neededProducts = array_filter($urls, fn ($item) => str_contains($item, $product));

        if (empty($neededProducts)) {
            throw new NotFoundHttpException();
        }

        $neededProduct = $data[key($neededProducts)];

        return new JsonResponse($neededProduct);
    }

    /**
     * Validates request content type.
     *
     * @param Request $request
     *
     * @throws UnsupportedMediaTypeHttpException
     */
    private function validateRequestContentType(Request $request): void
    {
        if ($request->getContentType() !== 'json') {
            throw new UnsupportedMediaTypeHttpException();
        }
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
}
