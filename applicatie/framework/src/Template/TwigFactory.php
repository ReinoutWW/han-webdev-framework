<?php 

namespace RWFramework\Framework\Template;

use RWFramework\Framework\Session\SessionInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

/**
 * This will create twig envoriment, all prepared on how we want to use it
 * Additional functionality: Add session() function to twig
 */
class TwigFactory {
    public function __construct(
        private SessionInterface $session,
        private string $templatesPath
    ) {}

    public function create(): Environment {
        // Instantiate the FileLoader with templates path
        $loader = new FilesystemLoader($this->templatesPath);

        // Instantiate the Environment with the FileLoader
        $twigEnvoriment = new Environment($loader, [
            'deug' => true,
            'cache'=> false,
        ]);

        $twigEnvoriment->addExtension(new DebugExtension());

        // Add new twig session() function to environment
        //[$this, 'getSession'] = The first parameter is the object, the second is the method on that object (callable)
        $twigEnvoriment->addFunction(
            new TwigFunction(
                'session', 
                [$this, 'getSession']
            )
        );

        return $twigEnvoriment;
    }

    public function getSession(): SessionInterface {
        return $this->session;
    }
}