<?php
declare(strict_types=1);

spl_autoload_register(function ($class) {
	$baseDir = __DIR__;
	$file = $baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	if (!file_exists($file)) {
		throw new RuntimeException("Class $class not found.");
	}
	
	require $file;
});