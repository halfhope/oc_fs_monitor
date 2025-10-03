<?php
/**
 * @author ChatGPT 4
 */

namespace Security;
class Tree_storage {
	private $rootDir;
 
	public function __construct($rootDir) {
		if (!is_dir($rootDir) || !is_writable($rootDir)) {
			throw new \Exception("Invalid root directory or missing write permissions.");
		}
		$this->rootDir = rtrim($rootDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}
 
	private function getFilePath($key) {
		// Validate the key
		if (!preg_match('/^[a-f0-9]{40}$/', $key)) {
			throw new \Exception("Invalid key. Expected a 40-character SHA-1 hash.");
		}

		// Build the path using the first 38 characters (excluding the last two)
		$path = $this->rootDir;
		$path .= substr($key, 0, 2) . DIRECTORY_SEPARATOR;
		$path .= substr($key, 2, 38) . '.dat';

		return $path;
	}

	public function set($key, $content) {
		$filePath = $this->getFilePath($key);

		// Create necessary directories if they do not exist
		$directory = dirname($filePath);
		if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
			throw new RuntimeException("Failed to create directory: $directory");
		}

		// Write content to file
		if (file_put_contents($filePath, gzencode($content)) === false) {
			throw new RuntimeException("Failed to write file: $filePath");
		}
	}

	public function get($key) {
		$filePath = $this->getFilePath($key);

		// Check if file exists
		if (!file_exists($filePath)) {
			return null;
		}

		// Read file content
		$content = gzdecode(file_get_contents($filePath));
		if ($content === false) {
			throw new RuntimeException("Failed to read file: $filePath");
		}

		return $content;
	}

	public function is_exists($key) {
		$filePath = $this->getFilePath($key);
		return file_exists($filePath);
	}

	public function remove($key) {
		$filePath = $this->getFilePath($key);

		// Delete file if it exists
		if (file_exists($filePath) && !unlink($filePath)) {
			throw new RuntimeException("Failed to delete file: $filePath");
		}
	}

	public function flush() {
		// Recursively delete all files and folders in the root directory
		$this->deleteDirectory($this->rootDir);
	}

	private function deleteDirectory($dir) {
		if (!is_dir($dir)) return;

		$files = scandir($dir);
		if ($files === false) {
			throw new RuntimeException("Failed to read directory: $dir");
		}

		foreach ($files as $file) {
			if ($file === '.' || $file === '..') continue;

			$filePath = $dir . DIRECTORY_SEPARATOR . $file;
			if (is_dir($filePath)) {
				$this->deleteDirectory($filePath);
			} else if (!unlink($filePath)) {
				throw new RuntimeException("Failed to delete file: $filePath");
			}
		}

		if (!rmdir($dir)) {
			throw new RuntimeException("Failed to delete directory: $dir");
		}
	}
}
