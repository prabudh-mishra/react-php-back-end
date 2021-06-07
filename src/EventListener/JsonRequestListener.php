<?php


namespace App\EventListener;


use Symfony\Component\HttpKernel\Event\RequestEvent;

class JsonRequestListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getContentType() === 'json') {
            $jsonData = @json_decode($request->getContent(), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->request->replace($jsonData);
            }
        }
    }
}
