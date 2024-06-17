<?php

use App\Controllers\HomeController;
use App\Roles\RoleConstants;
use RWFramework\Framework\Http\Roles\RequiredRoles;
use RWFramework\Framework\Http\Roles\Role;
use RWFramework\Framework\Routing\Route;

return [
    new Route('GET', '/', [HomeController::class, 'index']),
    new Route('POST', '/register', [\App\Controllers\RegistrationController::class, 'register']),
    new Route('GET', '/register', [\App\Controllers\RegistrationController::class, 'index'],
        [
            RWFramework\Framework\Http\Middleware\Guest::class
        ]
    ),
    new Route('POST', '/login', [\App\Controllers\LoginController::class, 'login']),
    new Route('GET', '/logout', [\App\Controllers\LoginController::class, 'logout'],
        [
            RWFramework\Framework\Http\Middleware\Authenticate::class
        ]
    ),
    new Route('GET', '/login', [\App\Controllers\LoginController::class, 'index'],
        [
            RWFramework\Framework\Http\Middleware\Guest::class
        ]
    ),
    new Route('GET', '/dashboard', [\App\Controllers\DashboardController::class, 'index'], 
        [  
            RWFramework\Framework\Http\Middleware\Authenticate::class,
            RWFramework\Framework\Http\Middleware\Dummy::class
        ]
    ),
    new Route('GET', '/vluchten', [\App\Controllers\FlightController::class, 'index'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ]),
    new Route('GET', '/vluchten/{flightNumber:\d+}', [\App\Controllers\FlightController::class, 'flight'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ]),
    new Route('GET', '/vluchten/{flightNumber:\d+}/passagier/{userId:\d+}', [\App\Controllers\FlightController::class, 'flight'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ], new RequiredRoles([
        new Role(RoleConstants::EMPLOYEE)
    ])),
    new Route('GET', '/stoel_boeken/{flightNumber:\d+}', [\App\Controllers\PassengerController::class, 'seatSelection'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ]),
    new Route('GET', '/vluchten/nieuw', [\App\Controllers\FlightController::class, 'create'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ], new RequiredRoles([
        new Role(RoleConstants::EMPLOYEE)
    ])),
    new Route('POST', '/vluchten/nieuw', [\App\Controllers\FlightController::class, 'store'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ]),
    new Route('POST', '/boeken/{flightNumber:\d+}', [\App\Controllers\PassengerController::class, 'book'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ]),
    new Route('GET', '/boekingen/vluchten', [\App\Controllers\PassengerController::class, 'booked'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ]),
    new Route('GET', '/vluchten/{flightNumber:\d+}/passagier/{passengerNumber:\d+}/bagage', [\App\Controllers\PassengerController::class, 'luggage'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ]),
    new Route('POST', '/vluchten/{flightNumber:\d+}/passagier/{passengerNumber:\d+}/bagage', [\App\Controllers\PassengerController::class, 'storeLuggage'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ]),
    new Route('POST', '/vluchten/{flightNumber:\d+}/passagier/{passengerNumber:\d+}/bagage/{objectFollowNumber:\d+}', [\App\Controllers\PassengerController::class, 'deleteLuggage'], [
        RWFramework\Framework\Http\Middleware\Authenticate::class
    ]),
];