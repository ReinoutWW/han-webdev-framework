<?php

namespace App\Controllers;

use RWFramework\Framework\Authentication\SessionAuthentication;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Session\Session;

class LoginController extends AbstractController {
    public function __construct(
        private SessionAuthentication $authComponent
    ){}

    public function index(): Response {
        return $this->render('login.html.twig');
    }

    public function login(): Response {
        // Attempt to authenticate the user using a secure component (bool)
        // Create a session for the user
        $userIsAuthenticated = $this->authComponent->authenticate(
            $this->request->input('username'),
            $this->request->input('password')
        );

        // If not succesfull, redirect to the login page
        if(!$userIsAuthenticated) {
            $this->request->getSession()->setFlash(Session::NOTIFICATION_ERROR, 'Het wachtwoord of de gebruikersnaam is onjuist. Probeer het opnieuw.');
            return new RedirectResponse('/login');
        }

        // If succesfull, retreive the user
        $user = $this->authComponent->user();

        $this->request->getSession()->setFlash(Session::NOTIFICATION_SUCCESS, 'Je bent weer ingelogd, welkom terug, ' . $user->getUsername());

        // Redirect to the intended page
        return new RedirectResponse('/dashboard');
    }

    public function logout(): Response {
        // Log the user out
        $this->authComponent->logout();

        // Set a flash message
        $this->request->getSession()->setFlash(Session::NOTIFICATION_SUCCESS, 'Je bent succesvol uitgelogd.');

        // Redirect to the login page
        return new RedirectResponse('/login');
    }
}