<?php

declare(strict_types=1);

// settings for development

return [
    'settings' => [
        'displayErrorDetails' => true,

        'doctrine' => [
            'dev_mode' => true,
            'cache_dir' => APP_ROOT . '/var/doctrine',
            'metadata_dirs' => [APP_ROOT . '/src/Models/Entity'],
            'connection' => [
                'driver' => 'pdo_mysql',
                'host' => '127.0.0.1',
                'port' => 3306,
                'dbname' => 'indicaer_des',
                'user' => 'indicaer_admin',
                'password' => 'pegasus@123',
                'charset' => 'UTF8'
            ]
        ]
    ]
];
