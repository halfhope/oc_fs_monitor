<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */
namespace Security;
final class FS_scans
{

    function __construct($registry)
    {

    }

    public function getScansDiff($scans)
    {

        $result = array();

        foreach ($scans as $key => $scan) {

            $scan_data = $scan['scan_data'];
            unset($scan);
            $scan['scanned'] = $scan_data['scanned'];

            if (isset($result[$key - 1]) && !empty($result[$key - 1])) {
                $diff                          = $this->getFilesDiff($scans[$key - 1]['scan_data']['scanned'], $scan['scanned']);
                $result[$key - 1]              = $scans[$key - 1];
                $result[$key - 1]['scan_data'] = array_merge($result[$key - 1]['scan_data'], $diff);

                $diff                      = $this->getFilesDiff($scan['scanned'], array());
                $result[$key]              = $scans[$key];
                $result[$key]['scan_data'] = array_merge($result[$key]['scan_data'], $diff);
            } else {
                $diff                      = $this->getFilesDiff($scan['scanned'], array());
                $result[$key]              = $scans[$key];
                $result[$key]['scan_data'] = array_merge($result[$key]['scan_data'], $diff);
            }

        }

        foreach ($result as $key => $scan) {

            $scan_size = $scan['scan_size'];

            if (isset($result[$key + 1]) && !empty($result[$key + 1])) {
                $size_up                   = (((int) $result[$key + 1]['scan_size'] - $scan_size) <= 0);
                $result[$key] = array_merge($result[$key], array(
                    'size_up' => $size_up,
                    'scan_size_compared' => abs($size_up ? (int) $result[$key + 1]['scan_size'] - $scan_size : $scan_size - (int) $result[$key + 1]['scan_size']),
                    'scan_size' => $scan_size,
                    'new_count' => count($scan['scan_data']['new']),
                    'changed_count' => count($scan['scan_data']['changed']),
                    'deleted_count' => count($scan['scan_data']['deleted']),
                    'scanned_count' => count($scan['scan_data']['scanned'])
                ));
            } else {
                $result[$key] = array_merge($result[$key], array(
                    'size_up' => true,
                    'scan_size_compared' => abs($scan_size),
                    'scan_size' => $scan_size,
                    'new_count' => count($scan['scan_data']['new']),
                    'changed_count' => count($scan['scan_data']['changed']),
                    'deleted_count' => count($scan['scan_data']['deleted']),
                    'scanned_count' => count($scan['scan_data']['scanned'])
                ));
            }
        }

        return $result;
    }

    private static function getFilesDiff($files_old, $files_new)
    {
                    try{

        $new       = array();
        $changed   = array();
        $deleted   = array();
        $crc32_key = array();

        if ($files_old) {
            foreach ($files_old as $file_name => $file_data) {
                $crc32_key[$file_data['crc']] = $file_data;
                if (isset($files_new[$file_name])) {
                        $diff = array_diff_assoc($files_new[$file_name], $file_data);

                    if ($diff) {
                        $changed[$file_name] = array(
                            'old' => $file_data,
                            'diff' => $diff,
                            'new' => $files_new[$file_name]
                        );
                        unset($files_new[$file_name]);
                    } else {
                        unset($files_new[$file_name]);
                    }
                } else {
                    $new[$file_name] = $file_data;
                }
            }
        }
                    } catch (Exception $e){
                        var_dump($files_new[$file_name], $e);
                    }

        $deleted = $files_new;

        return array(
            'new' => $new,
            'changed' => $changed,
            'deleted' => $deleted
        );
    }

    public static function getScanSize($files)
    {
        $scan_size = 0;

        foreach ($files as $file_name => $file_data) {
            $scan_size += $file_data['filesize'];
        }

        return $scan_size;
    }
}

?>