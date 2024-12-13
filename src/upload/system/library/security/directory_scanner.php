<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */
namespace Security;
final class Directory_scanner
{
    private static $extensions = array('php');
    private static $exclude_files = array();
    private static $exclude_paths = array();
    private static $include_paths = array();
    private static $files = array();
    private static $replace_path = '';

    function __construct($registry)
    {
        // Increase execution time
        set_time_limit(0);
        ini_set('max_execution_time', 0);
    }

    public function setExtensions($extensions)
    {
        self::$extensions = $extensions;
    }

    public function getExtensions()
    {
        return self::$extensions;
    }

    public function setIncludePaths($include_paths)
    {
        foreach ($include_paths as $key => $include_path) {
            if (!empty($include_path)) {
                self::$include_paths[] = self::normalizePath($include_path);
            }
        }
    }

    public function getIncludePaths()
    {
        return self::$include_paths;
    }

    public function setExcludePaths($exclude_paths)
    {
        foreach ($exclude_paths as $key => $exclude_path) {
            if (!empty($exclude_path)) {
                self::$exclude_paths[] = self::normalizePath($exclude_path);
            }
        }
    }
    public function getReplacePath()
    {
        return self::$replace_path;
    }

    public function setReplacePath($replace_path)
    {
        self::$replace_path = $replace_path;
    }

    public function getExcludePaths()
    {
        return self::$exclude_paths;
    }

    public function getFiles()
    {

        foreach (self::$include_paths as $key => $path) {
            $path = realpath($path);

            $directory = new \RecursiveDirectoryIterator($path);

            self::$files[$path] = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::LEAVES_ONLY, \RecursiveIteratorIterator::CATCH_GET_CHILD);

            $extensions = implode('|', self::$extensions);

            self::$files[$path] = new \RegexIterator(self::$files[$path], '/^.+\.(' . $extensions . ')$/i', \RecursiveRegexIterator::GET_MATCH);

        }

        $files = array();
        foreach (self::$files as $path => $file_list) {
            foreach ($file_list as $file_name => $file_data) {
                if (!$this->isExcluded($file_name)) {
                    $file_name         = realpath($file_name);
                    $files[$file_name] = self::getFileInfo($file_name);
                }

            }
        }

        return $files;
    }

    public function getFileInfo($file_name)
    {

        clearstatcache();

        return array(
            'crc' => crc32(file_get_contents($file_name)),
            'filemtime' => filemtime($file_name),
            'filectime' => filectime($file_name),
            'filesize' => filesize($file_name),
            'fileperms' => fileperms($file_name)
        );
    }

    private function isExcluded($path){
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

    private static function normalizePath($path, $encoding = "UTF-8")
    {
        // Attempt to avoid path encoding problems.
        $path  = iconv($encoding, "$encoding//IGNORE//TRANSLIT", $path);
        // Process the components
        $parts = explode('/', $path);
        $safe  = array();
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

?>