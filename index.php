<?php

declare(strict_types=1);

require 'vendor/autoload.php';

date_default_timezone_set('UTC');
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('error_log', '/dev/stdout'); // Docker wants logs written to stdout
ini_set('error_reporting', (string)E_ALL);
ini_set('log_errors', '1');

set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if ((error_reporting() & $severity) !== 0) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    return false;
}, E_ALL);

class Runner
{
    private GuzzleHttp\ClientInterface $client;
    private Psr\Http\Message\UriInterface $baseuri;
    private array $options;

    public function __construct()
    {
        $this->client = new GuzzleHttp\Client();

        $this->baseuri = new GuzzleHttp\Psr7\Uri('https://www.example.com');

        $this->options = [
            'timeout' => 25,
            'http_errors' => false,
        ];

    }

    function runTask()
    {
        $uri = $this->baseuri->withQuery(http_build_query([
            'foo' => (string)random_int(0, PHP_INT_MAX),
        ], '', '&', PHP_QUERY_RFC3986));

        // It's in the call stack from this line that the error occurs
        $_ = $this->client->send(new GuzzleHttp\Psr7\Request('GET', $uri), $this->options);
    }
}

$runner = new Runner();

$run = true;
$i = 0;
while ($run) {
    $runner->runTask();
    echo '.';
    sleep(1);
    if ($i++ >= 3) {
        $run = false;
    }
}
echo 'done';
