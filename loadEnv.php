<?php

$env_file = dirname(__FILE__) . '/.env';
$dotenv = new \josegonzalez\Dotenv\Loader([$env_file]);
$dotenv->parse()
    ->putenv(true)
    ->toEnv(true)
    ->toServer(true);
