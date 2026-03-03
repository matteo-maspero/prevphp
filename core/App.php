<?php
namespace core;

class App {
	private string $uri;
	private Router $router;

	public function __construct() {
		$this->uri = rtrim($_SERVER['REQUEST_URI'], '/');
		$this->router = new Router();
	}

	public function start() {

	}
}

$app = new App();
$app->start();
