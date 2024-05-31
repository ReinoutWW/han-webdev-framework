<?php 

namespace RWFramework\Framework\Template;

use ReflectionClass;
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

        $twigEnvoriment->addFunction(
            new TwigFunction(
                'isInList', 
                [$this, 'isInList']
            )
        );

        return $twigEnvoriment;
    }

    public function getSession(): SessionInterface {
        return $this->session;
    }

    public function isInList($value, $list): bool {
        // Check if the list is an array
        if (!is_array($list)) {
            return false;
        }
    
        // Iterate through each object in the array
        foreach ($list as $item) {
            // Check if the item is an object
            if (is_object($item)) {
                // Use reflection to access all properties
                $reflect = new ReflectionClass($item);
                $properties = $reflect->getProperties();
                $props = 0;
    
                // Loop through each property
                foreach ($properties as $property) {
                    $property->setAccessible(true); // Make the property accessible
                    $propertyValue = $property->getValue($item);
                    if(is_string($propertyValue)) {
                        $propertyValue = str_replace(' ', '', $propertyValue);
                    }

                    if ($propertyValue == $value) {
                        return true;
                    }

                    $props++;
                }
            }
        }
    
        // Return false if value is not found in any object
        return false;
    }
    
    
    
}