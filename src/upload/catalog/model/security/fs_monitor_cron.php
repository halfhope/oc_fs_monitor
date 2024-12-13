<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */
class ModelSecurityFSMonitorCron extends Model
{

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->checkAndInstall();

    }

    public function addScan($name, $user_name, $files, $scan_size)
    {

        $this->db->query("INSERT INTO `" . DB_PREFIX . "security_filesystem_monitor` (scan_size, user_name, name, date_added, scan_data) VALUES (" . (int) $scan_size . ",'" . $this->db->escape($user_name) . "','" . $this->db->escape($name) . "', NOW(), '" . $this->db->escape(json_encode(array(
            'scanned' => $files
        ))) . "');");

        return $this->db->getLastId();
    }

    public function getLastScan()
    {
        $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor` ORDER BY scan_id DESC LIMIT 0, 1");

        $result->row['scan_data'] = json_decode($result->row['scan_data'], true);

        return $result->row;
    }


    public function getScanNotification()
    {

        return $this->config->get('security_fs_notification');

    }

    public function setScanNotification($text)
    {

        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'security_fs', `key` = 'security_fs_notification', `value` = '" . $this->db->escape($text) . "'");

    }

    public function removeScanNotification()
    {

        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'security_fs_notification'");

    }

    public function getScans()
    {

        $scans = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor` ORDER BY scan_id DESC");

        foreach ($scans->rows as $key => $scan) {
            $scans->rows[$key]['scan_data'] = json_decode($scan['scan_data'], true);
        }

        return $scans->rows;
    }

    public function updateScansData($scans)
    {

        foreach ($scans as $scan) {
            $this->db->query("UPDATE `" . DB_PREFIX . "security_filesystem_monitor` SET `scan_data` = '" . $this->db->escape(json_encode($scan['scan_data'])) . "' WHERE `scan_id` = " . (int) $scan['scan_id']);
        }

    }

    public function checkAndInstall()
    {

        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "security_filesystem_monitor`
        (
            `scan_id` INT(11) NOT NULL AUTO_INCREMENT,
            `scan_size` INT(11) NOT NULL,
            `user_name` VARCHAR(255) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `date_added` DATETIME,
            `scan_data` MEDIUMTEXT COLLATE utf8_general_ci NOT NULL,
            PRIMARY KEY (`scan_id`)
        )
        ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
        ");

        $security_fs_base_path = $this->config->get('security_fs_base_path');
        if (empty($security_fs_base_path)) {
            $security_fs_base_path = realpath(DIR_APPLICATION . '..');
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'security_fs', `key` = 'security_fs_base_path', `value` = '" . $this->db->escape($security_fs_base_path) . "'");
        }

        $security_fs_extensions = $this->config->get('security_fs_extensions');
        if (empty($security_fs_extensions)) {
            $security_fs_extensions = str_replace('|', PHP_EOL, 'php5|php42|php4|php3|php|tpl|phpt|phps|phtm|phtml|phar|asp|aspx|sh|bash|zsh|csh|tsch|pl|py|pyc|jsp|cgi|cfm|css|js');
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'security_fs', `key` = 'security_fs_extensions', `value` = '" . $this->db->escape($security_fs_extensions) . "'");
        }

        $security_fs_cron_access_key = $this->config->get('security_fs_cron_access_key');
        if (empty($security_fs_cron_access_key)) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'security_fs', `key` = 'security_fs_cron_access_key', `value` = '" . $this->db->escape(md5(mt_rand())) . "'");
        }

        $security_fs_cron_save = $this->config->get('security_fs_cron_save');
        if (empty($security_fs_cron_save)) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'security_fs_cron_save'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'security_fs', `key` = 'security_fs_cron_save', `value` = '0'");
        }

        $security_fs_cron_notify = $this->config->get('security_fs_cron_notify');
        if (empty($security_fs_cron_notify)) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = 'security_fs_cron_notify'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '0', `code` = 'security_fs', `key` = 'security_fs_cron_notify', `value` = '1'");
        }

    }

}
?>