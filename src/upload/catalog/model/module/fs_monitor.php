<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ModelModuleFSMonitor extends Model {

	private function pack_data($object) {
		return base64_encode(gzdeflate(json_encode($object)));
	}

	private function unpack_data($data) {
		return json_decode(gzinflate(base64_decode($data)), true);
	}

	public function addScan($name, $files, $scan_size, $user = false) {
		$user_name = ($user) ? $user : $this->user->getUserName();

		$packed = $this->pack_data(['scanned' => $files]);

		$this->db->query("INSERT INTO `" . DB_PREFIX . "security_filesystem_monitor_data` (scan_data) VALUES ('" . $this->db->escape($packed) . "');");

		$scan_id = (int) $this->db->getLastId();

		$this->db->query("INSERT INTO `" . DB_PREFIX . "security_filesystem_monitor_generated` (scan_id, scan_size, user_name, name, auto, date_added) VALUES (" . (int) $scan_id . "," . (int) $scan_size . ",'" . $this->db->escape($user_name) . "','" . $this->db->escape($name) . "', 1, '" . date('Y-m-d H:i:s') . "');");

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
		} else {
			$to_update = [$scan];
		}

		$scans = $this->fs_scans->getScansDiff($to_update);

		$this->updateScansData([$scans[0]]);

		return $scan_id;
	}

	public function getLastScan() {
		$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "security_filesystem_monitor_generated` AS sfmg LEFT JOIN `" . DB_PREFIX . "security_filesystem_monitor_data` sfmd ON sfmd.scan_id = sfmg.scan_id ORDER BY sfmd.scan_id DESC LIMIT 0,1");
		$result->row['scan_data'] = $this->unpack_data($result->row['scan_data']);
		return $result->row;
	}

	public function updateScansData($scans) {
		foreach ($scans as $key => $scan) {
			$this->db->query("UPDATE `" . DB_PREFIX . "security_filesystem_monitor_data` SET scan_data = '" . $this->pack_data($scan['scan_data']) . "' WHERE scan_id = " . (int)($scan['scan_id']));
			$this->db->query("UPDATE `" . DB_PREFIX . "security_filesystem_monitor_generated` SET scan_size_abs = " . (int) $scan['scan_size'] . ", scan_size_rel = " . (int) (($scan['size_up']) ? $scan['scan_size_compared'] : -$scan['scan_size_compared']) . ", new_count = " . (int) $scan['new_count'] . ", changed_count = " . (int) $scan['changed_count'] . ", deleted_count = " . (int) $scan['deleted_count'] . ", scanned_count = " . (int) $scan['scanned_count'] . " WHERE scan_id = " . (int)($scan['scan_id']));
		}
	}
}