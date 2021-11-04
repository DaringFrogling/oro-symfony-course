<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Exception\{NotFoundHttpException, UnsupportedMediaTypeHttpException};
use Symfony\Component\Routing\Annotation\Route;

class ApiController
{
    /**
     * Returns information about client and writes it to log file.
     *
     * @Route("/api/my-info", methods="GET", name="client_info")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function actionClientInfo(Request $request): Response
    {
        if (!$request->headers->get('Content-type: application/json')) {
            throw new UnsupportedMediaTypeHttpException();
        }

        $clientInfo = [
            'ip' => $request->getClientIps()[0],
            'language' => $request->getLanguages(),
            'browser' => ($request->headers->get('User-Agent'))
        ];
        $responseData = json_encode($clientInfo);

        file_put_contents('logs.txt', $responseData . PHP_EOL, FILE_APPEND | LOCK_EX);

        return new Response(
            $responseData,
            Response::HTTP_OK,
            [
                'Content-type' => 'application/json'
            ]
        );
    }

    /**
     * Returns all resources of application.
     *
     * @Route("/api", methods="GET", name="list_of_resources")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function actionIndex(Request $request): Response
    {
        if (!$request->headers->get('Content-type: application/json')) {
            throw new UnsupportedMediaTypeHttpException();
        }

        $data = $this->getDataFromFile();
        $responseData = $this->prepareData($request, $data);

        return new Response(
            json_encode($responseData),
            Response::HTTP_OK,
            [
                'Content-type' => 'application/json'
            ]
        );
    }

    /**
     * Returns all resources of application.
     *
     * @Route("/api/{product}", methods="GET", name="product_id")
     *
     * @param Request $request
     * @param string $product
     *
     * @return Response
     */
    public function actionProduct(Request $request, string $product): Response
    {
        if (!$request->headers->get('Content-type: application/json')) {
            throw new UnsupportedMediaTypeHttpException();
        }

        $data = $this->getDataFromFile();
        $uris = $this->prepareData($request, $data);
        $neededProducts = array_filter($uris, function ($item) use ($product) {
            return str_contains($item, $product);
        });

        if (empty($neededProducts)) {
            throw new NotFoundHttpException();
        }

        $neededProduct = $data[key($neededProducts)];

        return new Response(
            json_encode($neededProduct),
            Response::HTTP_OK,
            [
                'Content-type' => 'application/json'
            ]
        );
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
     * @param Request $request
     * @param array $data
     *
     * @return array
     */
    private function prepareData(Request $request, array $data): array
    {
        $responseData = [];

        foreach ($data as $product) {
            $productUri = $this->formatUri($product[0]);
            $responseData[] = $request->getSchemeAndHttpHost() . "/{$productUri}";
        }

        return $responseData;
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
            } elseif (is_int($symbol) && !empty($productId)) {
                $productId[] = $symbol;
            }
        }

        return $productName . implode($productId);
    }
}
