<?php

namespace RWFramework\Framework\Authentication;

use RWFramework\Framework\Session\Session;
use RWFramework\Framework\Session\SessionInterface;

class SessionAuthentication implements SessionAuthInterface {
    private AuthUserInterface $user;

    public function __construct(
        private AuthRepositoryInterface $authRepository,
        private SessionInterface $session
    ){
    }
    
    public function authenticate(string $username, string $password): bool
    {
        // query db for user using username
        $user = $this->authRepository->findByUsername($username);

        if (!$user) {
            return false;
        }

        // Does the hashed user pw match the hash of the attempted password
        if (!password_verify($password, $user->getPassword())) {
            return false;
        }

        // if yes, log the user in
        $this->login($user);

        // return true
        return true;
    }

    public function login(AuthUserInterface $user): void
    {
        // Start a session
        $this->session->start();

        // Set the user in the session (login)
        $this->session->set(Session::AUTH_ID_KEY, $user->getAuthId());

        // Set user information in the session
        $this->session->set(Session::USER_KEY, $user);

        // Set the user here
        $this->user = $user;
    }

    public function logout(): void
    {
        $this->session->remove(Session::AUTH_ID_KEY);
    }

    public function user(): ?AuthUserInterface
    {
        return $this->user;
    }
}