<?php

namespace App\Controllers;

use App\Form\User\RegistrationForm;
use RWFramework\Framework\Controller\AbstractController;
use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Session\Session;

class RegistrationController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('register.html.twig');
    }

    public function register(): RedirectResponse
    {
        $form = new RegistrationForm();

        $form->setFields(
            $this->request->input('username'),
            $this->request->input('password')
        );

        if($form->hasValidationErrors()) {
            foreach($form->getValidationErrors() as $error) {
                $this->request->getSession()->setFlash('error', $error);
            }
        }

        return new RedirectResponse('/register');
    }
}