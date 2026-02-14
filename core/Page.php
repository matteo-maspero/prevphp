<?php
declare(strict_types=1);
namespace core;

use core\Router;
use core\Config;

class Page
{
	private Router $router;
	private Config $config;
	private string $path;

	public function __construct(Router $router, Config $config)
	{
		$this->router = $router;
		$this->config = $config;
		$this->path = $this->router->getPagePath();
	}

	public function render(): string
	{
		$layoutPath = $this->resolveLayoutPath();
		$contentPath = $this->path . DIRECTORY_SEPARATOR . 'content.php';
		
		// TODO: handle errors, etc...

		ob_start();
		include $contentPath;
		$content = ob_get_clean();

		ob_start();
		include $layoutPath;
		return ob_get_clean();
	}

	private function resolveLayoutPath(): string|false
	{
		$dir = $this->path;
		while (true) {
			$layoutPath = $dir . DIRECTORY_SEPARATOR . 'layout.php';
			if (file_exists($layoutPath)) {
				return $layoutPath;
			}

			if ($dir === $this->config->get('paths.app')) break;
			$dir = dirname($dir);
		}
		return false;
	}
}
