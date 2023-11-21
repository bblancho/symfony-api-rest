<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExceptionSuscriberSubscriber implements EventSubscriberInterface
{
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable() ;

        // On vérifie que $exception est bien une Instance de HttpException et que l'url commence bien par /api/
        if  ( $exception instanceof HttpException ) {
            
            $data = [
                'error'      => $exception->getMessage() ,
                'statusCode' => $exception->getStatusCode()
            ];

            $event->setResponse( new JsonResponse($data) );

        } else {

            $data = [
                'error'      => $exception->getMessage() ,
                'statusCode' => 500
            ];

            $event->setResponse( new JsonResponse($data) );
        }

    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }
}
