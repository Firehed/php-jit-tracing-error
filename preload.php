<?php

require_once 'vendor/autoload.php';

class_exists(GuzzleHttp\ClientInterface::class);
class_exists(GuzzleHttp\Client::class);
class_exists(GuzzleHttp\Psr7\Uri::class);
opcache_compile_file(__DIR__ . '/index.php');
