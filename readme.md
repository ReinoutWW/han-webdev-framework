# RWFramework 1.0.0  - PHP 8

## The only framework you will ever need
The RWFramework will provide the fundamentals for a working API or Web application, and will include the following:
- Route handling
- Kernal class
- Request handlers
- Controllers support

## Contribution

### Adding packages guidelines
When adding 3rd party packages, always check the following:
- How well is it maintained?
- When was the last commit?
- How many contributers are there?
- Are open issues being addressed?

Always be aware that the packages are being updated to new versions so it'll always be compatible with the latest PHP version.

## Installation
You will find a boilerplate in the source.

### Folder structure
- Root
    - framework : RWFramework logic
    - public
        - index.php : The base script that will handle requests
    - routes : Includes the configured routes
    - src : The controllers

## Containers and reflections
Instances of objects van be obtained via the $container. Once registered in the config/services.php, the $container will deliver instances of the classes. When the class is not yet added to the $container, it will try to resolve the classes itself using reflections and recursion. Every objects' constructor will be scanned, and the dependencies will be instantiated. 

Once completed, the object will be returned.

## Environments and error handling
For error handling, the framework will have different environments available. In production, you might not want all errors to be visible with all the beautofill local paths and variables. But for debugging, this information is crusial. 

In the Kernal class, the field $appEnv will be set to a configuration. The available options are:
- 'dev' or 'test' for full exceptions
- 'prod' for production and exception hiding

The configuration can be set in the framework/.env file, for example: APP_ENV=dev.

## Templating engine
This framework is not a MVC framework. However, there is one key component we can learn from the MVC architecture. It is the seperation of concerns in the business logic / presentation code. Ofcourse, this is something we want here too.

To avoid messy PHP tags in the front-end, we'll be using a templating engine. One of the benefits is that we won't have to add messy PHP tags like this: `<?php echo $name ?>`, and instead we use `{{ $name }}`. One other benefit is a security aspect, and it will escape content to protect against cross-site scripting attacks. 

To build our own templating engine would be impossible with the time available, so we will use the Twig template engine. Other frameworks like Laravel and Symfony use other 3rd party templating engines to run the framework.

## Database abstraction layer
One of the requirements for this project is the SQL-injection safety, and one other personal learning goal I have for this project is using a database abstraction layer. This means that the code is written independant of the relational database.

We will be using Doctrine DBAL, which is one of those database abstraction layers. It's designed to be fast and efficient, and we will use prepared statements to prevent SQL injection attacks. 

### Custom console commands
Like other mainstream frameworks, we want a solution for handling specific framework tasks. Other frameworks have custom set of commands available. This makes it possible to, for example, run database migrations. 

We will make our own console command line program, which will simply be a php file expecting incomming commands. It will make use of our 'heart', called the services.php. This contains the container (nice sentence), and will make our live easy collecting instances of objects.

Location:
/bin/console

#### Commands
Help:
- docker compose exec web_server php bin/console database:migrations:migrate 
- docker compose exec web_server php bin/console database:migrations:rollback 
