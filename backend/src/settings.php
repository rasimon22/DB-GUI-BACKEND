<?php
return [
    'settings' => [
        'displayErrorDetails' =>true,
        'addContentLengthHeader' => false,
        //Renderer Settings
        'renderer' => [
            'templagte_path' => __DIR__ . '/../templates/',
        ],
        //Monolog Settings
        'logger'=> [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        //connection settings
        "db" => [
            "host" => "localhost",
            "dbname" => "myplaylist",
            "user" => "root",
            "pass" => "abc123"
        ],
    ],
];
