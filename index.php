<?php

	try
	{
		set_time_limit(0);
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
		ob_implicit_flush(TRUE);

		if (!isset($argv[1]))
			die('Please enter absolute path');

		$directory = new RecursiveDirectoryIterator($argv[1]);
		$iterator = new RecursiveIteratorIterator($directory);
		$regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

		foreach ($regex as $file_path)
		{
			$file_content = file_get_contents($file_path[0]);
			$result = preg_match_all('/(Class|class) ([a-zA-Z0-9]*)(\s*|){/ium', $file_content, $matches);

			if (empty($result))
				continue;

			if (empty($matches[2]))
				continue;

			$old_constructor = 'function ' . $matches[2][0];
			$new_constructor = 'function __construct';

			$file_content = str_replace($old_constructor, $new_constructor, $file_content);
			file_put_contents($file_path[0], $file_content);

			echo 'File ' . $file_path[0] . ' changed.' . PHP_EOL;
		}
	}
	catch (Exception $e)
	{
		echo $e->getMessage();
	}

// END