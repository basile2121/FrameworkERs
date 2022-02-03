<?php
require_once __DIR__ . '/../vendor/autoload.php';


use App\Config\Connection;
use Symfony\Component\Dotenv\Dotenv;

// Declaration des varibales superglobale dans .env et .env.local
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

// BDD
$connection = new Connection();
$connection->init();
var_dump($connection->getPdoConnection());
