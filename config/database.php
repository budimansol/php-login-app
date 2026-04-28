<?php

function getDatabaseConfig(): array {
    return [
        "database" => [
            "test" =>[
                "url" => "mysql:host=localhost:3306;dbname=php_login_test",
                "username" => "root",
                "password" => ""
            ],
            "prod" => [
                "url" => "mysql:host=localhost:3306;dbname=php_login",
                "username" => "root",
                "password" => ""
            ]
        ]
    ];
}

?>