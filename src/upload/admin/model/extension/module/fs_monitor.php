<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ModelExtensionModuleFSMonitor extends Model
{

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->checkAndInstall();

    }

    private function pack_data($object){
        return base64_encode(gzdeflate(json_encode($object)));
    }

    private function unpack_data($object){
        return json_decode(gzinflate(base64_decode($object)), true);
    }

    private function compatibleEditSetting($key, $value, $code, $store_id = 0){
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = '".$this->db->escape($key)."'");
        if (version_compare('2', VERSION) >= 0) {
            $code_column_name = 'group';
        } else {
            $code_column_name = 'code';
        }
        return $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '".(int)$store_id."', `" . $code_column_name . "` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
    }

    public function getTotalScans(){
        $query = $this->db->query("SELECT scan_id FROM `" . DB_PREFIX . "security_filesystem_monitor_generated`");

        return $query->num_rows;
    }

    public function addScan($name, $files, $scan_size, $user = false)
    {

        $user_name = ($user) ? $user : $this->user->getUserName();

        $this->db->query("INSERT INTO `" . DB_PREFIX . "security_filesystem_monitor_data` (scan_data) VALUES ('" . $this->db->escape($this->pack_data(array('scanned' => $files))) . "');");

        $scan_id = (int) $this->db->getLastId();

        $this->db->query("INSERT INTO `" . DB_PREFIX . "security_filesystem_monitor_generated` (scan_id, scan_size, user_name, name, auto, date_added) VALUES (" . (int) $scan_id . "," . (int) $scan_size . ",'" . $this->db->escape($user_name) . "','" . $this->db->escape($name) . "', 0, '" . date('Y-m-d H:i:s') . "');");

        $last_scan = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_data` AS sfmd LEFT JOIN `" . DB_PREFIX . "security_filesystem_monitor_generated` sfmg ON sfmg.scan_id = sfmd.scan_id WHERE sfmd.scan_id < " . (int) $scan_id . " ORDER BY sfmd.scan_id DESC LIMIT 0, 1");

        $scan = array(
            'scan_id' => $scan_id,
            'scan_data' => array(
                'scanned' => $files,
            ),
            'scan_size' => $scan_size,
        );

        if ($last_scan->num_rows == 1) {
            $last_scan->row['scan_data'] = $this->unpack_data($last_scan->row['scan_data']);
            $to_update = array($scan, $last_scan->row);
        }else{
            $to_update = array($scan);
        }

        $scans = $this->fs_scans->getScansDiff($to_update);

        $this->updateScansData(array($scans[0]));

        return $scan_id;
    }

    public function getLastScan()
    {
        $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_generated` AS sfmg LEFT JOIN `" . DB_PREFIX . "security_filesystem_monitor_data` sfmd ON sfmd.scan_id = sfmg.scan_id ORDER BY sfmd.scan_id DESC LIMIT 0,1");
        $result->row['scan_data'] = $this->unpack_data($result->row['scan_data']);
        return $result->row;
    }

    public function getScan($scan_id, $full = false)
    {

        if($full){
            $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_generated` AS sfmg LEFT JOIN `" . DB_PREFIX . "security_filesystem_monitor_data` sfmd ON sfmd.scan_id = sfmg.scan_id WHERE sfmg.scan_id = " . (int) $scan_id);
            $result->row['scan_data'] = $this->unpack_data($result->row['scan_data']);
        }else{
            $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_generated` AS sfmg WHERE scan_id = " . (int) $scan_id);
        }

        return $result->row;
    }

    public function deleteScan($scan_id)
    {

        $this->db->query("DELETE FROM `" . DB_PREFIX . "security_filesystem_monitor_generated` WHERE `scan_id` = " . (int) $scan_id);
        $this->db->query("DELETE FROM `" . DB_PREFIX . "security_filesystem_monitor_data` WHERE `scan_id` = " . (int) $scan_id);

        $next_scan = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_data` AS sfmg WHERE scan_id > " . (int) $scan_id . " ORDER BY scan_id ASC LIMIT 0, 1 ");

        if ($next_scan->num_rows == 1) {

            $last_scan = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_data` AS sfmg WHERE scan_id < " . (int) $scan_id . " ORDER BY scan_id DESC LIMIT 0, 1 ");

            if ($last_scan->num_rows == 1) {
                $last_scan = $this->getScan($last_scan->row['scan_id'], true);
                $to_update = array($this->getScan($next_scan->row['scan_id'], true), $last_scan);
            } else {
                $to_update = array($this->getScan($next_scan->row['scan_id'], true));
            }

            $scans = $this->fs_scans->getScansDiff($to_update);

            $this->updateScansData(array($scans[0]));
        }

    }

    public function getScans($data = array())
    {
        // $data = array(
        //     'start'           => ($page - 1) * $this->config->get('config_admin_limit'),
        //     'limit'           => $this->config->get('config_admin_limit')
        // );

        $sql = "SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_generated` ORDER BY scan_id DESC";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $scans = $this->db->query($sql);

        return $scans->rows;
    }

    public function rename($scan_id, $scan_name)
    {
        $query = $this->db->query("UPDATE `" . DB_PREFIX . "security_filesystem_monitor_generated` SET `name` = '" . $this->db->escape($scan_name) . "' WHERE `scan_id` = " . (int)$scan_id);

        return $query;
    }

    public function updateScansData($scans)
    {
        foreach ($scans as $key => $scan) {
            $this->db->query("UPDATE `" . DB_PREFIX . "security_filesystem_monitor_data` SET scan_data = '" . $this->pack_data($scan['scan_data']) . "' WHERE scan_id = " . (int)($scan['scan_id']));
            $this->db->query("UPDATE `" . DB_PREFIX . "security_filesystem_monitor_generated` SET scan_size_abs = " . (int) $scan['scan_size'] . ", scan_size_rel = " . (int) (($scan['size_up']) ? $scan['scan_size_compared'] : -$scan['scan_size_compared']) . ", new_count = " . (int) $scan['new_count'] . ", changed_count = " . (int) $scan['changed_count'] . ", deleted_count = " . (int) $scan['deleted_count'] . ", scanned_count = " . (int) $scan['scanned_count'] . " WHERE scan_id = " . (int)($scan['scan_id']));
        }

    }

    public function checkAndInstall($replace = false)
    {

        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "security_filesystem_monitor_data`
        (
            `scan_id` INT(11) NOT NULL AUTO_INCREMENT,
            `scan_data` MEDIUMTEXT COLLATE utf8_general_ci NOT NULL,
            PRIMARY KEY (`scan_id`)
        )
        ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
        ");

        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "security_filesystem_monitor_generated`
        (
            `scan_id` INT(11) NOT NULL,
            `scan_size` INT(11) NOT NULL,
            `user_name` VARCHAR(255) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `scan_size_abs` VARCHAR(20) NOT NULL,
            `scan_size_rel` VARCHAR(20) NOT NULL,
            `scanned_count` INT(11) NOT NULL,
            `new_count` INT(11) NOT NULL,
            `changed_count` INT(11) NOT NULL,
            `deleted_count` INT(11) NOT NULL,
            `auto` tinyint NOT NULL,
            `date_added` DATETIME,
            PRIMARY KEY (`scan_id`)
        )
        ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
        ");

        // $this->db->query("ALTER TABLE `" . DB_PREFIX . "security_filesystem_monitor_generated` ADD INDEX `date_added` (`date_added`);");

        $security_fs_admin_dir = $this->config->get('security_fs_admin_dir');
        if (empty($security_fs_admin_dir) || $replace) {
            $security_fs_admin_dir = basename(DIR_APPLICATION);
            $this->compatibleEditSetting('security_fs_admin_dir', $security_fs_admin_dir, 'security_fs');
        }

        $security_fs_base_path = $this->config->get('security_fs_base_path');
        if (empty($security_fs_base_path) || $replace) {
            $security_fs_base_path = realpath(DIR_APPLICATION . '..');
            $this->compatibleEditSetting('security_fs_base_path', $security_fs_base_path, 'security_fs');
        }

        $security_fs_extensions = $this->config->get('security_fs_extensions');
        if (empty($security_fs_extensions) || $replace) {
            $security_fs_extensions = str_replace('|', PHP_EOL, 'php5|php42|php4|php3|php|tpl|twig|phpt|phps|phtm|phtml|phar|asp|aspx|sh|bash|zsh|csh|tsch|pl|py|pyc|jsp|cgi|cfm|css|js');
            $this->compatibleEditSetting('security_fs_extensions', $security_fs_extensions, 'security_fs');
        }

        $security_fs_cron_access_key = $this->config->get('security_fs_cron_access_key');
        if (empty($security_fs_cron_access_key) || $replace) {
            $this->compatibleEditSetting('security_fs_cron_access_key', md5(mt_rand()), 'security_fs');
        }

        $security_fs_cron_save = $this->config->get('security_fs_cron_save');
        if (is_null($security_fs_cron_save) || $replace) {
            $this->compatibleEditSetting('security_fs_cron_save', 1, 'security_fs');
        }

        $security_fs_cron_notify = $this->config->get('security_fs_cron_notify');
        if (is_null($security_fs_cron_notify) || $replace) {
            $this->compatibleEditSetting('security_fs_cron_notify', 1, 'security_fs');
        }

        $tables = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "security_filesystem_monitor'");

        if ($tables->num_rows) {
            // Create backup
            $output = '';

            foreach ($tables->rows as $table) {
                if (DB_PREFIX) {
                    if (strpos($table, DB_PREFIX) === false) {
                        $status = false;
                    } else {
                        $status = true;
                    }
                } else {
                    $status = true;
                }

                if ($status) {
                    $output .= 'TRUNCATE TABLE `' . $table . '`;' . "\n\n";

                    $query = $this->db->query("SELECT * FROM `" . $table . "`");

                    foreach ($query->rows as $result) {
                        $fields = '';

                        foreach (array_keys($result) as $value) {
                            $fields .= '`' . $value . '`, ';
                        }

                        $values = '';

                        foreach (array_values($result) as $value) {
                            $value = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $value);
                            $value = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $value);
                            $value = str_replace('\\', '\\\\',  $value);
                            $value = str_replace('\'', '\\\'',  $value);
                            $value = str_replace('\\\n', '\n',  $value);
                            $value = str_replace('\\\r', '\r',  $value);
                            $value = str_replace('\\\t', '\t',  $value);

                            $values .= '\'' . $value . '\', ';
                        }

                        $output .= 'INSERT INTO `' . $table . '` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
                    }

                    $output .= "\n\n";
                }
            }

            @file_put_contents(DIR_LOGS . 'fs_monitor_backup.sql', $output);
            // end

            $scans = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor` ORDER BY scan_id ASC");

            foreach ($scans->rows as $key => $scan) {

                $scan['scan_data'] = $scans->rows[$key]['scan_data'] = $scans->rows[$key]['scan_data'] = $this->unpack_data($scan['scan_data']);

                $scan['auto'] = $scans->rows[$key]['auto'] = ($scan['name'] == $this->language->get('text_cron_scan_name')) ? true : false;

                $scan_data = array(
                    'scanned'   => $scan['scan_data']['scanned'],
                    'new'       => $scan['scan_data']['new'],
                    'changed'   => $scan['scan_data']['changed'],
                    'deleted'   => $scan['scan_data']['deleted'],
                );

                $this->db->query("INSERT INTO `" . DB_PREFIX . "security_filesystem_monitor_data` (scan_data) VALUES ('" . $this->db->escape($this->pack_data($scan_data)) . "');");

                $new_scan_id = $this->db->getLastId();

                $this->db->query("INSERT INTO `" . DB_PREFIX . "security_filesystem_monitor_generated` (scan_id, scan_size, user_name, name, auto, date_added) VALUES (" . (int) $new_scan_id . "," . (int) $scan['scan_size'] . ",'" . $this->db->escape($scan['user_name']) . "','" . $this->db->escape($scan['name']) . "', " . (int)$scan['auto'] . ", '" . $this->db->escape($scan['date_added']) . "');");

            }

            $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "security_filesystem_monitor`");

            foreach ($scans->rows as $key => $scan) {
                $this->db->query("UPDATE `" . DB_PREFIX . "security_filesystem_monitor_generated` SET scan_size_abs = " . (int) $scan['scan_data']['scan_size'] . ", scan_size_rel = " . (int) (($scan['scan_data']['size_up']) ? $scan['scan_data']['scan_size_compared'] : -$scan['scan_data']['scan_size_compared']) . ", new_count = " . (int) $scan['scan_data']['new_count'] . ", changed_count = " . (int) $scan['scan_data']['changed_count'] . ", deleted_count = " . (int) $scan['scan_data']['deleted_count'] . ", scanned_count = " . (int) $scan['scan_data']['scanned_count'] . " WHERE scan_id = " . (int)($scan['scan_id']));
            }

            $scans = $this->getScans();

            foreach ($scans as $key => $scan) {
                $scans[$key] = $this->getScan($scan['scan_id'], true);
            }

            $diff = $this->fs_scans->getScansDiff($scans);

            $this->updateScansData($diff);

        }

    }

}

?>