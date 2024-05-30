<?php

namespace RWFramework\Framework\Http\Middleware;

use RWFramework\Framework\Authentication\SessionAuthentication;
use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Session\Session;
use RWFramework\Framework\Session\SessionInterface;

class Guest implements MiddlewareInterface
{
    public function __construct(private SessionInterface $session)
    {
    }    

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->session->start();  

        // If authenticated, redirect to the home page
        if($this->session->isAuthenticated()) {
            $this->session->setFlash(Session::NOTIFICATION_INFO, 'You are aleady authenticated.');
            return new RedirectResponse('/dashboard');
        }

        return $handler->handle($request);
    }
}