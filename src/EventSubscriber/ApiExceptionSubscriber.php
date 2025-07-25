<?php namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Exception\NotFoundException;
use App\Controller\ApiResponse;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $e = $event->getThrowable();

        $status = 400;
        if ($e instanceof NotFoundException) {
            $status = 404;
        } elseif ($e instanceof \PDOException) {
            $status = 500;
        } elseif ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
        }

        $data = ApiResponse::error(['error' => $e->getMessage()]);

        $event->setResponse(new JsonResponse($data, $status));
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }
}
