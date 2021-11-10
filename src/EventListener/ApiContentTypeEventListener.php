<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class ApiContentTypeEventListener
{
    /**
     * Validates request content type.
     *
     * @param RequestEvent $event
     *
     * @throws UnsupportedMediaTypeHttpException
     */
    public function __invoke(RequestEvent $event) : void
    {
        $request = $event->getRequest();

        if (!str_contains($request->getPathInfo(), 'api')) {
            return;
        }

        if ($request->getContentType() !== 'json') {
            throw new UnsupportedMediaTypeHttpException();
        }
    }
}