<?php

namespace RWFramework\Framework\Http\Middleware;

use RWFramework\Framework\Authentication\SessionAuthentication;
use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Session\Session;
use RWFramework\Framework\Session\SessionInterface;

class Authenticate implements MiddlewareInterface {
    public function __construct(
        private SessionInterface $session
    ){
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response {
        $this->session->start();  

        if(!$this->session->has(SessionAuthentication::AUTH_ID_KEY)) {
            $this->session->setFlash(Session::NOTIFICATION_ERROR, 'Not authenticated, please sign in.');
            return new RedirectResponse('/login');
        }

        return $handler->handle($request);
    }
}