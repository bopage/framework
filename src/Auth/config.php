<?php

use App\Auth\AuthTwigExtension;
use App\Auth\DatabaseAuth;
use App\Auth\ForbiddenMiddleware;
use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\User;
use App\Auth\UserTable;
use Framework\Auth;

use function DI\add;
use function DI\autowire;
use function DI\get;

return [
    'auth.login' => '/login',
    'auth.entity' => User::class,
    Auth::class => get(DatabaseAuth::class),
    'twig.extensions' => add([
        get(AuthTwigExtension::class)
    ]),
    UserTable::class => autowire()->constructorParameter('entity', get('auth.entity')),
    ForbiddenMiddleware::class => autowire()->constructorParameter('loginPath', get('auth.login')),
    PasswordResetMailer::class => autowire()->constructorParameter('from', get('mail.from'))
];
