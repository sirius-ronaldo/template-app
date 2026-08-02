<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

abstract class HttpTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        IntegrationDatabase::reset();
        $_ENV['DB_SCHEMA'] = IntegrationDatabase::SCHEMA;
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_COOKIE = [];
        $_SERVER = [];
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    /**
     * @param array<string, string> $data
     * @param array<string, string> $cookies
     * @param array<string, string> $headers
     * @param array<string, array<string, mixed>> $files
     * @return array{status:int,headers:list<string>,body:string}
     */
    protected function request(
        string $method,
        string $uri,
        array $data = [],
        array $cookies = [],
        array $headers = [],
        array $files = []
    ): array {
        header_remove();
        http_response_code(200);

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $_GET = [];
        $_POST = strtoupper($method) === 'POST' ? $data : [];
        $_FILES = strtoupper($method) === 'POST' ? $files : [];
        $_COOKIE = $cookies;
        $_SERVER = [
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $uri,
            'HTTP_HOST' => 'localhost',
        ];

        foreach ($headers as $name => $value) {
            $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $name))] = $value;
        }

        $query = parse_url($uri, PHP_URL_QUERY);
        if (is_string($query)) {
            parse_str($query, $_GET);
        }

        ob_start();
        include dirname(__DIR__, 2) . '/public/index.php';
        $body = (string) ob_get_clean();

        $response = [
            'status' => http_response_code(),
            'headers' => headers_list(),
            'body' => $body,
        ];

        restore_exception_handler();
        header_remove();

        return $response;
    }
}
