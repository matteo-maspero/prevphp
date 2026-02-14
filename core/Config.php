<?php
declare(strict_types=1);
namespace core;

const DEFAULT_CONFIG_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.json';

class Config
{
	private array $config;

	public function __construct(string $path = DEFAULT_CONFIG_PATH)
	{
		$this->config = json_decode(file_get_contents($path), true) ?? [];
		$this->config['paths']['app'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app';
		$this->config['paths']['core'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'core';
		$this->config['paths']['public'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public';
	}

	public function get(string $key): mixed
	{
		$steps = explode('.', $key);
		$value = $this->config;

		foreach ($steps as $step) {
			if (!isset($value[$step])) {
				return null;
			}
			$value = $value[$step];
		}
		
		return $value;
	}
}
