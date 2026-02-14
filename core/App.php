<?php
declare(strict_types=1);
namespace core;

use core\Config;
use core\Router;

class App
{
	private Config $config;
	private Router $router;

	public function __construct()
	{
		$this->config = new Config();
		$this->router = new Router($this->config, $_SERVER['REQUEST_URI']);
	}

	public function start(): void
	{
		$this->router->dispatch();

		$page = new Page($this->router, $this->config);

		echo $page->render();
	}
}
