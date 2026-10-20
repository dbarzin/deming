<?php

/*
 * Avec une configuration en cache, Laravel ignore les variables de phpunit.xml
 * (base SQLite en mémoire) : RefreshDatabase viderait alors la base réelle.
 * On refuse donc de lancer les tests tant que le cache n'est pas supprimé.
 */
$configCache = $_SERVER['APP_CONFIG_CACHE'] ?? $_ENV['APP_CONFIG_CACHE'] ?? __DIR__ . '/../bootstrap/cache/config.php';

if (file_exists($configCache)) {
    fwrite(STDERR, "\n  Configuration is cached ({$configCache}): tests would run against the real database.\n"
        . "  Run `php artisan config:clear` before running the tests.\n\n");
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';
