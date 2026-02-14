<?php
declare(strict_types=1);
namespace core;

use core\Config;

class Router
{
	private Config $config;
	private string $urlPath;
	private array $params;
	private string $pagePath;

	public function __construct(Config $config, string $requestUrl)
	{
		$this->config = $config;
		$this->urlPath = $this->parseUrlPath($requestUrl);
		$this->params = $this->parseParams($requestUrl);
		$this->dispatch();
	}

	public function dispatch(): void
	{
		if ($this->recursePagePath($this->config->get('paths.app'), $this->urlPath)) return;
		$this->redirect('');
	}

	public function redirect(string $path): void
	{
		if ($path === $this->urlPath) return;
		header('Location: ' . $this->config->get('baseUrl') . '/' . $path);
		exit;
	}

	public function refresh(): void
	{
		header('Location: ' . $this->config->get('baseUrl') . '/' . $this->urlPath);
		exit;
	}

	private function recursePagePath(string $pagePath, string $urlPath): bool
	{
		// If URL is empty, we've found the page path
		if ($urlPath === '') {
			$this->pagePath = $pagePath;
			return true;
		}
		
		list($urlPathSegment, $remainingUrlPath) = explode('/', $urlPath, 2) + ['', ''];
		$exactMatchPath = $pagePath . DIRECTORY_SEPARATOR . $urlPathSegment;

		// Try exact directory match first
		if (is_dir($exactMatchPath)) {
			return $this->recursePagePath($exactMatchPath, $remainingUrlPath);
		}

		// Fall back to dynamic route parameters
		$children = scandir($pagePath);
		foreach ($children as $child) {
			if ($child === '.' || $child === '..') continue;
			
			$childPath = $pagePath . DIRECTORY_SEPARATOR . $child;

			if (!is_dir($childPath)) continue;
			if (!str_starts_with($child, '(')) continue;
			if (!str_ends_with($child, ')')) continue;

			if ($this->recursePagePath($childPath, $urlPath)) {
				return true;
			}
		}

		return false;
	}

	private function parseUrlPath(string $requestUrl): string
	{
		$path = parse_url($requestUrl, PHP_URL_PATH);
		$path = strtolower($path);
		return trim($path, '/');
	}

	private function parseParams(string $requestUrl): array
	{
		$queryString = parse_url($requestUrl, PHP_URL_QUERY);
		if ($queryString === null) {
			return [];
		}
		parse_str($queryString, $params);
		return $params;
	}

	/**
	 * Getters for page and layout paths
	 */

	public function getPagePath(): string
	{
		return $this->pagePath;
	}
}
