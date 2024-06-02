<?php

namespace RWFramework\Framework\Controller;

use Psr\Container\ContainerInterface;
use RWFramework\Framework\Http\Request;
use RWFramework\Framework\Http\Response;

abstract class AbstractController {
    protected ?ContainerInterface $container = null;
    protected Request $request;
    
    public function setContainer(ContainerInterface $container): void {
        $this->container = $container;
    }

    public function setRequest(Request $request): void {
        $this->request = $request;
    }

    public function render(string $template, array $parameters = [], Response $response = null): Response {
        $parameters['pathInfo'] = $this->request->getPathInfo();
        $parameters['currentPath'] = $this->request->getPathInfo();

        $content = $this->container->get('twig')->render($template, $parameters);

        $response ??= new Response();

        $response->setContent($content);
        
        return $response;
    }
}