<?php
namespace Src;

class Router {
    private $method;
    private $uri;
    private $body;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->body = json_decode(file_get_contents('php://input'), true) ?: [];
    }

    public function method() { return $this->method; }
    public function uri() { return $this->uri; }
    public function body() { return $this->body; }
}
?>
