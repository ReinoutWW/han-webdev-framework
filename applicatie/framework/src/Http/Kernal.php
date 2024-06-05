<?php
// The kernal will handle a request and return a response

namespace RWFramework\Framework\Http;

use Psr\Container\ContainerInterface;
use RWFramework\Framework\Authentication\SessionAuthentication;
use RWFramework\Framework\EventDispatcher\EventDispatcher;
use RWFramework\Framework\Http\Event\ResponseEvent;
use RWFramework\Framework\Http\Middleware\RequestHandlerInterface;
use RWFramework\Framework\Routing\RouterInterface;

class Kernal {
    private string $appEnv;

    public function __construct(
        private ContainerInterface $container,
        private RequestHandlerInterface $requestHandler,
        private EventDispatcher $eventDispatcher
    ) { // PHP 8 will automatically create the property and assign the value
        $this->appEnv = $container->get('APP_ENV');
    }

    public function handle(Request $request): Response 
    {
        try {
            $response = $this->requestHandler->handle($request);
        }
        catch(\Exception $exception) {
            $response = $this->createExceptionResponse($exception);
        }

        $this->eventDispatcher->dispatch(new ResponseEvent($request, $response));

        return $response;
    }

    /**
     * @throws \Exception $exception
     * This will handle the exception and return a response, instaed of creating exception handling *directly* in the frontdoor controller
     */
    private function createExceptionResponse(\Exception $exception): Response {
        if(in_array($this->appEnv, ['dev', 'test'])) {
            throw $exception;
        }

        if($exception instanceof HttpException) {
            return new Response($exception->getMessage(), $exception->getStatusCode());
        }

        return new Response('RWFramework 1.0.0 | We kunnen dit verzoek niet verwerken. Probeer het later opnieuw, of bel de helpdesk. (Teams :P)', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function terminate(Request $request, Response $response): void {
        $request->getSession()?->clearFlash(); // The question mark is a null check, just like in C#
        
        // Remove auth_key from session
        //$request->getSession()?->remove(SessionAuthentication::AUTH_ID_KEY);
    }
}