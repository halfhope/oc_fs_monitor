<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Security;
class OpenCart_scanner {
	private $db;
	
	public function __construct($db) {
		$this->db = $db;
	}

	public function getMods() {
		$mods = [];
		$db_name = DB_DATABASE;
		
		$table = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . $db_name . "' AND TABLE_NAME = '" . DB_PREFIX . "modification'");
		if (!empty($table->row)) {

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification m ORDER BY m.modification_id ASC");
			
			if ($query->num_rows) {
				foreach ($query->rows as $key => $value) {
					$value['file'] = $value['name'];
					$mods[$value['modification_id']] = $value;
				}
			}
		}

		// This is purly for developers so they can run mods directly and have them run without upload after each change.
		$selection[] = glob(DIR_APPLICATION . '../vqmod/xml/*.xml');
		$selection[] = glob(DIR_APPLICATION . '../vqmod/xml/*.xml*');
		$selection[] = glob(DIR_SYSTEM . '*.xml');
		$selection[] = glob(DIR_SYSTEM . '*.xml*');
		foreach ($selection as $key => $files) {
			if ($files) {
				foreach ($files as $file) {
					if (filesize($file) > 0) {
						$fmid = hash('crc32b', pathinfo($file, PATHINFO_BASENAME));
						$mods[$fmid] = $this->parseFSMod($file);
					}
				}
			}
		}
		return $mods;
	}

	public function parseFSMod($filename) {
		$fmid = hash('crc32b', pathinfo($filename, PATHINFO_BASENAME));
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$xml = file_get_contents($filename);
		$dom->loadXml($xml);
		if ($dom->getElementsByTagName('name')->length) {
			$name = $dom->getElementsByTagName('name')->item(0)->textContent;
		} elseif ($dom->getElementsByTagName('id')->length) {
			$name = $dom->getElementsByTagName('id')->item(0)->textContent;
		} else {
			$name = 'Not found';
		}
		$author = $dom->getElementsByTagName('author')->item(0)->textContent;
		$version = $dom->getElementsByTagName('version')->length ? $dom->getElementsByTagName('version')->item(0)->textContent : '';
		$link = $dom->getElementsByTagName('link')->length ? urldecode($dom->getElementsByTagName('link')->item(0)->textContent) : '';
		$status = (int)(pathinfo($filename, PATHINFO_EXTENSION) == 'xml');

		return [
			'modification_id' => $fmid,
			'file' => $filename,
			'name' => $name,
			'code' => ($dom->getElementsByTagName('code')->length) ? $dom->getElementsByTagName('code')->item(0)->textContent : 'None',
			'author' => $author,
			'version' => $version,
			'link' => $link,
			'xml' => crc32($xml),
			'status' => $status,
			'date_added' => date('Y-m-d H:i:s', filectime($filename))
		];
	}

	public function getModifications() {
		return $this->getMods();
	}

	public function getTablesAndFields() {
		$result = [];
		$alltables = $this->db->query("SHOW TABLES");
		$db_name = DB_DATABASE;
		foreach($alltables->rows as $key => $table_data) {
			$table_name = array_values($table_data)[0];
			$fields = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . $db_name . "' AND TABLE_NAME = '" . $table_name . "'");
			foreach ($fields->rows as $field_data) {
				$result[$table_name][$field_data['COLUMN_NAME']] = $field_data;
			}
		}
		return $result;
	}

	public function getUsers() {
		$users = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user`");
		return $users->rows;
	}

	public function getModificationsDiff($one, $two) {
        
		return array_diff_assoc($one, $two);
	}

	public function getTablesAndFieldsDiff($one, $two) {
		return array_diff_assoc($one, $two);
	}

	public function getUsersDiff($one, $two) {
		return array_diff_assoc($one, $two);
	}
}