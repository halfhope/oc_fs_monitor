<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Extension\FS_Monitor\System\Library\Security;
class Directory_scanner {

	private $extensions     = [];
	private $exclude_paths 	= [];
	private $include_paths 	= [];
	private $replace_path 	= '';

	public function setExtensions(array $extensions): void {
		$this->extensions = $extensions;
	}

	public function getExtensions(): array {
		return $this->extensions;
	}

	public function setIncludePaths(array $include_paths): void {
		foreach ($include_paths as $key => $include_path) {
			if (!empty($include_path)) {
				$this->include_paths[] = self::normalizePath($include_path);
			}
		}
	}

	public function getIncludePaths(): array {
		return $this->include_paths;
	}

	public function setExcludePaths(array $exclude_paths): void {
		foreach ($exclude_paths as $exclude_path) {
			if (!empty($exclude_path)) {
				$this->exclude_paths[] = self::normalizePath($exclude_path);
			}
		}
	}

	public function getReplacePath(): string {
		return $this->replace_path;
	}

	public function setReplacePath(string $replace_path): void {
		$this->replace_path = $replace_path;
	}

	public function getExcludePaths(): array {
		return $this->exclude_paths;
	}

	public function getFiles(): array {

		// Increase execution time
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		
		$files = [];

		foreach ($this->include_paths as $path) {
			$directory = new \RecursiveDirectoryIterator($path);
			$files[$path] = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::LEAVES_ONLY, \RecursiveIteratorIterator::CATCH_GET_CHILD);

			$extensions = implode('|', $this->extensions);
			$files[$path] = new \RegexIterator($files[$path], '/^.+\.(' . $extensions . ')$/i', \RecursiveRegexIterator::GET_MATCH);
		}
		
		clearstatcache();

		$result = [];
		foreach ($files as $path => $file_list) {
			foreach ($file_list as $file_name => $file_data) {
				if (!$this->isExcluded($file_name)) {
					$file_name         = $file_name;
					$result[$file_name] = self::getFileInfo($file_name);
				}
			}
		}

		return $result;
	}

	private function isExcluded(string $path): bool {
		$absolute = $path;
		$relative = str_replace($this->getReplacePath(), '', $path);

		foreach ($this->getExcludePaths() as $pattern) {
			if (strncmp($relative, $pattern, strlen($pattern)) == 0 || strncmp($absolute, $pattern, strlen($pattern)) == 0) {
				return true;
			}
			if (fnmatch($pattern, $relative, FNM_PATHNAME | FNM_NOESCAPE) || fnmatch($pattern, $absolute, FNM_PATHNAME | FNM_NOESCAPE)) {
				return true;
			}
		}

		return false;
	}

	public static function getFileInfo(string $file_name): array {
		// osworx becuase under WIN filemtime may display wrong time
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
			$mtime = stat($file_name)['mtime'];
		} else {
			$mtime = filemtime($file_name);
		}

		return [
			'crc' 		=> crc32(file_get_contents($file_name)),
			'filemtime' => $mtime,
			'filectime' => filectime($file_name),
			'filesize' 	=> filesize($file_name),
			'fileperms' => fileperms($file_name)
		];
	}

	private static function normalizePath(string $path, string $encoding = "UTF-8"): string {
		// Attempt to avoid path encoding problems.
		$path  = iconv($encoding, "$encoding//IGNORE//TRANSLIT", $path);
		// Process the components
		$parts = explode('/', $path);
		$safe  = [];
		foreach ($parts as $idx => $part) {
			if (($idx > 0 && trim($part) == "") || $part == '.') {
				continue;
			} elseif ('..' == $part) {
				array_pop($safe);
				continue;
			} else {
				$safe[] = $part;
			}
		}
		// Return the "clean" path
		$path = implode(DIRECTORY_SEPARATOR, $safe);
		return $path;
	}
}