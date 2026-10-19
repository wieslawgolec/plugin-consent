<?php

declare(strict_types=1);

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'MauticPlugin\\MauticConsentBundle\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
        $path     = dirname(__DIR__) . '/' . $relative . '.php';
        if (is_file($path)) {
            require_once $path;
        }
    });
}

// Minimal stubs so unit tests run without full Mautic/Symfony bootstrap.
require_once __DIR__ . '/Stub/FormEntity.php';
require_once __DIR__ . '/Stub/CommonRepository.php';
