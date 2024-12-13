<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ModelModuleFSMonitor extends Model {

	public $_version = '1.2';

	private function pack_data($object) {
		return base64_encode(gzdeflate(json_encode($object)));
	}

	private function unpack_data($object) {
		return json_decode(gzinflate(base64_decode($object)), true);
	}
	
	private function editSetting($key, $value, $code, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `key` = '".$this->db->escape($key)."'");
		return $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '".(int)$store_id."', `group` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
	}

	public function getTotalScans(){
		$query = $this->db->query("SELECT scan_id FROM `" . DB_PREFIX . "security_filesystem_monitor_generated`");
		return $query->num_rows;
	}

	public function addScan($name, $files, $scan_size, $user = false) {

		$user_name = ($user) ? $user : $this->user->getUserName();
		$packed = $this->pack_data(['scanned' => $files]);

		$this->db->query("INSERT INTO `" . DB_PREFIX . "security_filesystem_monitor_data` (scan_data) VALUES ('" . $this->db->escape($packed) . "');");

		$scan_id = (int) $this->db->getLastId();

		$this->db->query("INSERT INTO `" . DB_PREFIX . "security_filesystem_monitor_generated` (scan_id, scan_size, user_name, name, auto, date_added) VALUES (" . (int) $scan_id . "," . (int) $scan_size . ",'" . $this->db->escape($user_name) . "','" . $this->db->escape($name) . "', 0, '" . date('Y-m-d H:i:s') . "');");

		$last_scan = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_data` AS sfmd LEFT JOIN `" . DB_PREFIX . "security_filesystem_monitor_generated` sfmg ON sfmg.scan_id = sfmd.scan_id WHERE sfmd.scan_id < " . (int) $scan_id . " ORDER BY sfmd.scan_id DESC LIMIT 0, 1");

		$scan = [
			'scan_id' => $scan_id,
			'scan_data' => [
				'scanned' => $files,
			],
			'scan_size' => $scan_size,
		];

		if ($last_scan->num_rows == 1) {
			$last_scan->row['scan_data'] = $this->unpack_data($last_scan->row['scan_data']);
			$to_update = [$scan, $last_scan->row];
		}else{
			$to_update = [$scan];
		}

		$scans = $this->fs_scans->getScansDiff($to_update);

		$this->updateScansData([$scans[0]]);

		return $scan_id;
	}

	public function getScan($scan_id, $full = false) {
		if($full){
			$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_generated` AS sfmg LEFT JOIN `" . DB_PREFIX . "security_filesystem_monitor_data` sfmd ON sfmd.scan_id = sfmg.scan_id WHERE sfmg.scan_id = " . (int) $scan_id);
			$result->row['scan_data'] = $this->unpack_data($result->row['scan_data']);
		}else{
			$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_generated` AS sfmg WHERE scan_id = " . (int) $scan_id);
		}
		return $result->row;
	}

	public function deleteScan($scan_id) {

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

	public function getScans($data = array()) {
		
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

	public function getLastScan() {
		$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_generated` AS sfmg ORDER BY sfmg.scan_id DESC LIMIT 0,1");
		return $result->row;
	}

	public function rename($scan_id, $scan_name) {
		$query = $this->db->query("UPDATE `" . DB_PREFIX . "security_filesystem_monitor_generated` SET `name` = '" . $this->db->escape($scan_name) . "' WHERE `scan_id` = " . (int)$scan_id);
		return $query;
	}

	public function updateScansData($scans) {
		foreach ($scans as $key => $scan) {
			$this->db->query("UPDATE `" . DB_PREFIX . "security_filesystem_monitor_data` SET scan_data = '" . $this->pack_data($scan['scan_data']) . "' WHERE scan_id = " . (int)($scan['scan_id']));
			$this->db->query("UPDATE `" . DB_PREFIX . "security_filesystem_monitor_generated` SET scan_size_abs = " . (int) $scan['scan_size'] . ", scan_size_rel = " . (int) (($scan['size_up']) ? $scan['scan_size_compared'] : -$scan['scan_size_compared']) . ", new_count = " . (int) $scan['new_count'] . ", changed_count = " . (int) $scan['changed_count'] . ", deleted_count = " . (int) $scan['deleted_count'] . ", scanned_count = " . (int) $scan['scanned_count'] . " WHERE scan_id = " . (int)($scan['scan_id']));
		}
	}

	public function install($replace = false) {

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "security_filesystem_monitor_data`
		(
			`scan_id` INT(11) NOT NULL AUTO_INCREMENT,
			`scan_data` MEDIUMTEXT COLLATE utf8_general_ci NOT NULL,
			PRIMARY KEY (`scan_id`)
		)
		ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "security_filesystem_monitor_generated`
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
		ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		
		$defaults = [
			'security_fs_admin_dir' => basename(DIR_APPLICATION),
			'security_fs_base_path' => realpath(DIR_APPLICATION . '..'),
			'security_fs_extensions' => str_replace('|', PHP_EOL, 'php5|php42|php4|php3|php|tpl|twig|phpt|phps|phtm|phtml|phar|asp|aspx|sh|bash|zsh|csh|tsch|pl|py|pyc|jsp|cgi|cfm|css|js'),
			'security_fs_cron_access_key' => md5(mt_rand()),
			'security_fs_cron_save' => 1,
			'security_fs_cron_notify' => 1,
			'security_fs_exclude' => str_replace('|', PHP_EOL, 'vqmod/vqcache/')
		];

		foreach ($defaults as $key => $value) {
			$setting = $this->config->get($key);
			if (empty($setting) || $replace) {
				$this->editSetting($key, $value, 'security_fs');				
			}
		}
	}

}