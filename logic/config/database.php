<?php


const DB_CONFIG_VALUE = [

    'default' => 'mysql',

    'connections' => [
        'mysql' => [
            'host' => 'localhost',
            'dbname' => 'uploads',
            'charset' => 'utf8',
            'username' => 'root',
            'password' => ''
        ]
    ],

];

const DB_CONNECTION_VALUE = [
    'dsn' => 'mysql:host=localhost; dbname=uploads; charset=utf8',
    'username' => 'root',
    'password' => ''
];


return DB_CONNECTION_VALUE;


?>