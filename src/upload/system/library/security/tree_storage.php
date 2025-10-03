<?php
/**
 * @author ChatGPT 4
 */

namespace Security;
class Tree_storage {
	private $rootDir;
 
	public function __construct($rootDir) {
		if (!is_dir($rootDir) || !is_writable($rootDir)) {
			throw new \Exception("Неверный корневой каталог или отсутствуют права на запись.");
		}
		$this->rootDir = rtrim($rootDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}
 
	private function getFilePath($key) {
		// Проверяем валидность ключа
		if (!preg_match('/^[a-f0-9]{40}$/', $key)) {
			throw new \Exception("Неверный ключ. Ожидается 40-символьный SHA-1 хеш.");
		}

		// Формируем путь по первым 38 символам (кроме двух последних)
		$path = $this->rootDir;
		$path .= substr($key, 0, 2) . DIRECTORY_SEPARATOR;
		$path .= substr($key, 2, 38) . '.dat';

		return $path;
	}

	public function set($key, $content) {
		$filePath = $this->getFilePath($key);

		// Создаем необходимые папки, если они отсутствуют
		$directory = dirname($filePath);
		if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
			throw new RuntimeException("Не удалось создать директорию: $directory");
		}

		// Записываем содержимое в файл
		if (file_put_contents($filePath, gzencode($content)) === false) {
			throw new RuntimeException("Не удалось записать файл: $filePath");
		}
	}

	public function get($key) {
		$filePath = $this->getFilePath($key);

		// Проверяем, существует ли файл
		if (!file_exists($filePath)) {
			return null;
		}

		// Читаем содержимое файла
		$content = gzdecode(file_get_contents($filePath));
		if ($content === false) {
			throw new RuntimeException("Не удалось прочитать файл: $filePath");
		}

		return $content;
	}

	public function is_exists($key) {
		$filePath = $this->getFilePath($key);
		return file_exists($filePath);
	}

	public function remove($key) {
		$filePath = $this->getFilePath($key);

		// Удаляем файл, если он существует
		if (file_exists($filePath) && !unlink($filePath)) {
			throw new RuntimeException("Не удалось удалить файл: $filePath");
		}
	}

	public function flush() {
		// Рекурсивное удаление всех файлов и папок в корневом каталоге
		$this->deleteDirectory($this->rootDir);
	}

	private function deleteDirectory($dir) {
		if (!is_dir($dir)) return;

		$files = scandir($dir);
		if ($files === false) {
			throw new RuntimeException("Не удалось прочитать директорию: $dir");
		}

		foreach ($files as $file) {
			if ($file === '.' || $file === '..') continue;

			$filePath = $dir . DIRECTORY_SEPARATOR . $file;
			if (is_dir($filePath)) {
				$this->deleteDirectory($filePath);
			} else if (!unlink($filePath)) {
				throw new RuntimeException("Не удалось удалить файл: $filePath");
			}
		}

		if (!rmdir($dir)) {
			throw new RuntimeException("Не удалось удалить директорию: $dir");
		}
	}
}
 