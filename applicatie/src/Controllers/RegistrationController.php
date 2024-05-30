<?php

namespace App\Controllers;

use App\Form\User\RegistrationForm;
use App\Repository\UserMapper;
use RWFramework\Framework\Authentication\SessionAuthentication;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Http\Response;

class RegistrationController extends AbstractController
{
    public function __construct(
        private UserMapper $userMapper,
        private SessionAuthentication $sessionAuthentication
    ){}

    public function index(): Response
    {
        return $this->render('register.html.twig');
    }

    public function register(): RedirectResponse
    {
        $form = new RegistrationForm($this->userMapper);

        $form->setFields(
            $this->request->input('username'),
            $this->request->input('password')
        );

        if($form->hasValidationErrors()) {
            foreach($form->getValidationErrors() as $error) {
                $this->request->getSession()->setFlash('error', $error);
            }

            return new RedirectResponse('/register');
        }

        // Register user
        $user = $form->save();

        // Log success 
        $this->request->getSession()->setFlash('success', 'You have been registered successfully!');

        // Log the user in
        $this->sessionAuthentication->login($user);

        return new RedirectResponse('/dashboard');
    }
}