<?php 

namespace RWFramework\Framework\Http\Middleware;

use RWFramework\Framework\Http\RedirectResponse;
use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;
use RWFramework\Framework\Http\Roles\RoleManagerInterface;
use RWFramework\Framework\Http\Roles\RoleUserInterface;
use RWFramework\Framework\Session\Session;
use RWFramework\Framework\Session\SessionInterface;

class RoleValidation implements MiddlewareInterface {
    public function __construct(
        private SessionInterface $session,
        private RoleManagerInterface $roleManager,
    ){ }

    public function process(Request $request, RequestHandlerInterface $handler): Response {
        $user = $this->session->getUser();

        // If the RoleUserInterface is not used
        if(!$user && !$user instanceof RoleUserInterface) {
            return $handler->handle($request);
        } 

        if($user instanceof RoleUserInterface) {
            // Validatie if the user has the required roles
            $hasRequiredRoles = $this->roleManager->hasRequiredRoles($user);

            if(!$hasRequiredRoles) {
                $this->session->setFlash('error', 'Je hebt niet de juiste rechten om deze pagina te bezoeken.');
                return new RedirectResponse($this->session->getLastVisitedRoute());
            }
        }

        return $handler->handle($request);
    }
}