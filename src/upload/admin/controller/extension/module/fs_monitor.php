<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

if (!function_exists("array_key_last")) {
	function array_key_last($array) {
		if (!is_array($array) || empty($array)) {
			return NULL;
		}
		
		return array_keys($array)[count($array)-1];
	}
}

class ControllerExtensionModuleFsMonitor extends Controller {

	private	$_route 			= 'extension/module/fs_monitor';
	private	$_model 			= 'model_extension_module_fs_monitor';
	private $_version 			= '1.2.3.1';
	private	$_dashboard_route	= 'extension/dashboard/fs_monitor';
	private	$_extensions_route 	= 'extension/extension';

	private $error = [];

	/**
	 * install
	 * @return void
	 **/
	public function install() {
		$this->load->model($this->_route);
		$this->{$this->_model}->install(true);
	}

	/**
	 * uninstall
	 * @return void
	 **/
	public function uninstall() {
		$this->load->model($this->_route);
		$this->{$this->_model}->uninstall();
	}

	/**
	 * main section
	 * @return void
	 **/
	public function getScans() {
		$this->load->model($this->_route);
		$data = $this->language->load($this->_route);

		$this->humanizer = new Security\humanizer($this->registry);

		if (isset($this->request->post['page'])) {
			$page = $this->request->post['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->post['limit'])) {
			$limit = $this->request->post['limit'];
		} else {
			$limit = $this->config->get('config_limit_admin');
		}

		$filter_data = [
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		];

		$scans = $this->{$this->_model}->getScans($filter_data);

		if (!$scans) {
			$this->createScan($this->language->get('text_initial_scan'));
		
			$scans = $this->{$this->_model}->getScans();
			$data['success'] = $this->language->get('text_success_scan_initial');
		}

		// osworx locale fix
		$this->load->model('localisation/language');

		$current_language_code = $this->config->get('config_admin_language');
		$languages	= $this->model_localisation_language->getLanguages(true);

		foreach ($languages as $language_data) {
			if ($language_data['code'] == $current_language_code) {
				setlocale(LC_TIME, explode(',', $language_data['locale']));
			}
		}
		
		$data['groups'] = $data['scans'] = [];

		foreach ($scans as $scan) {
			$date_key = $this->language->get('text_scans_on') . ' ' . date('l jS \of F Y', strtotime($scan['date_added']));

			$scan['scan_size_abs_humanized'] 	= $this->humanizer->humanBytes($scan['scan_size_abs']);
			$scan['scan_size_rel_humanized'] 	= $this->humanizer->humanBytes($scan['scan_size_rel']);

			$scan['date_added_ago'] 			= $this->humanizer->humanDatePrecise($scan['date_added'], 'F j H:i:s');

			$scanKey = $scan['scan_id'];

			$data['scans'][$scanKey] = $scan;
			$group_key = $this->searchForKeyValue('name', $date_key, $data['groups']);
			if ($group_key === false) {
				$data['groups'][] = [
					'name'	=> $date_key,
					'children' => [],
				];
				$group_key = array_key_last($data['groups']);
				$data['groups'][$group_key]['children'][] = $scanKey;
			} else {
				$data['groups'][$group_key]['children'][] = $scanKey;
			}
		}

		$data['pagination'] = [
			'total' => $this->{$this->_model}->getTotalScans(),
			'page' 	=> $page,
			'limit' => $limit,
		];

		$result = [
			'groups' 	 => $data['groups'], 
			'scans' 	 => $data['scans'],
			'pagination' => $data['pagination']
		];

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
	}

	/**
	 * view scan function
	 * @return void
	 **/
	public function getScan() {
		$this->load->model($this->_route);
		$data = $this->language->load($this->_route);

		$this->humanizer = new Security\humanizer($this->registry);
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['scan_id'])) {
			$scan_id = (int) $this->request->post['scan_id'];

			$data['scan'] = $this->{$this->_model}->getScan($scan_id, true);
			
			if ($data['scan']) {
				$scan_data = $data['scan']['scan_data'];
				unset($data['scan']['scan_data']);
				$scan_result = [];
				if ($scan_data['scanned']) {
					foreach ($scan_data['scanned'] as $file_name => $file_data) {
						$scan_result['scanned'][$file_name] = $this->formatFile($file_name, $file_data);
					}
				}
				if ($scan_data['new']) {
					foreach ($scan_data['new'] as $file_name => $file_data) {
						$scan_result['new'][$file_name] = $this->formatFile($file_name, $file_data);
					}
				}
				if ($scan_data['changed']) {
					foreach ($scan_data['changed'] as $file_name => $file_data) {
						$postfix = '';
						if (isset($file_data['diff']['filesize'])) {
							if ($file_data['old']['filesize'] >= $file_data['new']['filesize']) {
								$postfix = ' (+' . $this->humanizer->humanBytes(abs($file_data['new']['filesize'] - $file_data['old']['filesize'])) . ')';
							} else {
								$postfix = ' (-' . $this->humanizer->humanBytes(abs($file_data['new']['filesize'] - $file_data['old']['filesize'])) . ')';
							}
						}
						$file_data['postfix'] = $postfix;
						$scan_result['changed'][$file_name] = $this->formatFile($file_name, $file_data);
					}
				}
				
				if ($scan_data['deleted']) {
					foreach ($scan_data['deleted'] as $file_name => $file_data) {
						$scan_result['deleted'][$file_name] = $this->formatFile($file_name, $file_data);
					}
				}
				$data['scan_id'] = $scan_id;
				$data['scan']['scan_data'] = $scan_result;
				$data['scan']['scan_size_abs_humanized'] = $this->humanizer->humanBytes($data['scan']['scan_size_abs']);
				$data['scan']['scan_size_rel_humanized'] = $this->humanizer->humanBytes($data['scan']['scan_size_rel']);
				$data['scan']['date_added_ago'] = $this->humanizer->humanDatePrecise($data['scan']['date_added'], 'F j H:i:s');
				$data['scan']['href'] = $this->url->link($this->_route . '/viewScan', 'scan_id=' . $data['scan']['scan_id'] . '&token=' . $this->session->data['token'], 'SSL');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($data['scan']));
			} else {
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode(['error' => $this->language->get('error_scan_doesnt_exists')]));				
			}
		}
	}
	
	public function addScan() {
		$this->load->model($this->_route);
		$this->language->load($this->_route);
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['scan_name']) && $this->user->hasPermission('modify', $this->_route)) {
			$scan_id = $this->createScan($this->request->post['scan_name']);
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($scan_id));
		} else {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode(['error' => $this->language->get('error_scan_doesnt_exists')]));
		}
	}

	public function deleteScans() {
		$this->load->model($this->_route);
		$this->language->load($this->_route);

		$this->fs_scans = new Security\fs_scans();

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['scans'] && $this->user->hasPermission('modify', $this->_route)) {
			foreach ($this->request->post['scans'] as $key => $value) {
				$this->{$this->_model}->deleteScan((int) $value);
			}
			$response['success'] = $this->language->get('text_success_scans_deleted');
		} else {
			$response['error'] = $this->language->get('error_permission');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));
	}

	public function renameScan() {
		$this->load->model($this->_route);
		$this->language->load($this->_route);

		if ($this->user->hasPermission('modify', $this->_route)) {
			if (isset($this->request->post['scan_name']) && !empty($this->request->post['scan_name'])) {
				$scan_name = $this->request->post['scan_name'];
				$this->{$this->_model}->rename((int)$this->request->post['scan_id'], $scan_name);

				$response['success'] = $this->language->get('text_success_renamed');
			} else {
				$response['error'] = $this->language->get('error_empty_name');
			}
		} else {
			$response['error'] = $this->language->get('error_permission');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}

	public function getSettings() {
		$response['settings'] = [
			'security_fs_admin_dir' 			=> $this->config->get('security_fs_admin_dir'),
			'security_fs_base_path' 			=> $this->config->get('security_fs_base_path'),
			'security_fs_extensions' 			=> $this->config->get('security_fs_extensions'),
			'security_fs_include' 				=> $this->config->get('security_fs_include'),
			'security_fs_exclude' 				=> $this->config->get('security_fs_exclude'),
			'security_fs_cron_access_key' 		=> $this->config->get('security_fs_cron_access_key'),
			'security_fs_cron_save' 			=> $this->config->get('security_fs_cron_save'),
			'security_fs_cron_notify' 			=> $this->config->get('security_fs_cron_notify'),

			'security_fs_notify_to' 			=> $this->config->get('security_fs_notify_to'),
			'security_fs_e_emails' 				=> $this->config->get('security_fs_e_emails'),
			'security_fs_w_phone_number' 		=> $this->config->get('security_fs_w_phone_number'),
			'security_fs_w_business_account_id' => $this->config->get('security_fs_w_business_account_id'),
			'security_fs_w_api_token' 			=> $this->config->get('security_fs_w_api_token'),
			'security_fs_t_api_token' 			=> $this->config->get('security_fs_t_api_token'),
			'security_fs_t_channel_id' 			=> $this->config->get('security_fs_t_channel_id'),
		];
		
		$response['entries'] = [
			'security_fs_cron_wget' => '/usr/local/bin/wget -q -O- \'' . str_replace($this->config->get('security_fs_admin_dir') . '/', '', HTTP_SERVER) . 'index.php?route=' . $this->_route . '&access_key=',
			'security_fs_cron_curl' => '/usr/local/bin/curl -s \'' . str_replace($this->config->get('security_fs_admin_dir') . '/', '', HTTP_SERVER) . 'index.php?route=' . $this->_route . '&access_key=',
			'security_fs_cron_cli'  => '/usr/local/bin/php -q \'' . str_replace($this->config->get('security_fs_admin_dir') . '/', '', DIR_APPLICATION) . 'index.php?route=' . $this->_route . '&access_key='
		];

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));
	}

	public function saveSettings() {
		$data = $this->language->load($this->_route);
		
		$this->load->model('setting/setting');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_setting_setting->editSetting('security_fs', $this->request->post);
			$response['success'] = $this->language->get('text_success_saved');
		} else {
			$response['error'] = $this->language->get('error_permission');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));
	}
	
	
	public function index() {
		$data = $this->language->load($this->_route);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('//cdn.jsdelivr.net/npm/sortable-tablesort@2.1.1/sortable.min.css');
		$this->document->addScript('//cdn.jsdelivr.net/npm/sortable-tablesort@2.1.1/sortable.min.js');

		$this->load->model($this->_route);
		$this->{$this->_model}->install(false);

		$breadcrumbs = [];

		$breadcrumbs[] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		];

		$breadcrumbs[] = [
				'text' => $this->language->get('text_modules'),
				'href' => $this->url->link($this->_extensions_route, 'token=' . $this->session->data['token'] . '&type=module', 'SSL')
			];
	
		$breadcrumbs[] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($this->_route, 'token=' . $this->session->data['token'], 'SSL')
		];
	
		$data['initial_data'] = json_encode([
			'api_entry' 	=> $this->_route,
			'token' 		=> $this->session->data['token'],
			'breadcrumbs' 	=> $breadcrumbs,
			'i18n' 			=> $data,
			'app_container'	=> '#fsm',
			'version'		=> $this->_version
		]);
	
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view($this->_route . '/main', $data));
	}
 

	/**
	 * view file section
	 * @return void
	 **/
	public function viewFile() {
		$this->load->language($this->_route);
		
		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->user->hasPermission('modify', $this->_route)) {

			if (isset($this->request->post['file_name'])) {
				$file_name = urldecode($this->request->post['file_name']);
				if (file_exists($file_name) && is_file($file_name)) {
					$response['content'] = file_get_contents($file_name);
				} else {
					$response['error'] = $this->language->get('file_doesnt_exists');
				}
			}

			switch (pathinfo($file_name, PATHINFO_EXTENSION)) {
				case 'php5':
				case 'php42':
				case 'php4':
				case 'php3':
				case 'php':
				case 'tpl':
				case 'phpt':
				case 'phps':
				case 'phtm':
				case 'phtml':
					$response['mode'] = 'php';
					break;
				case 'twig':
					$response['mode'] = 'twig';
					break;
				case 'css':
					$response['mode'] = 'css';
					break;
				case 'js':
					$response['mode'] = 'javascript';
					break;
				default:
					$response['mode'] = 'php';
					break;
			}

			$data['heading_title'] = $file_name;

		} else {
			$response['error'] = $this->language->get('error_permission');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));
	}

	/**
	 * widget section
	 * @return string controller output
	 **/
	public function widget() {
		$this->load->model($this->_route);

		$data = $this->language->load($this->_route);
		$data += $this->language->load($this->_dashboard_route);

		$this->humanizer = new Security\humanizer($this->registry);

		$data['token'] = $this->session->data['token'];

		$data['reload_widget'] = html_entity_decode($this->url->link($this->_route . '/reloadWidget', 'token=' . $this->session->data['token'], 'SSL'), ENT_QUOTES, 'UTF-8');
		$data['view_all'] = $this->url->link($this->_route, 'token=' . $this->session->data['token'], 'SSL');

		$scan = $this->{$this->_model}->getLastScan();

		if ($scan) {
			unset($data['scan_data']);

			$date_key = $this->language->get('text_scans_on') . date_format(date_create($scan['date_added']), $this->language->get('text_date_format_short'));

			$scan['scan_size_abs_humanized'] = $this->humanizer->humanBytes($scan['scan_size_abs']);
			$scan['scan_size_rel_humanized'] = $this->humanizer->humanBytes($scan['scan_size_rel']);

			$data['scan'] = $scan;
			$data['scan']['date_added_ago'] = $this->humanizer->humanDatePrecise($data['scan']['date_added'], 'H:i:s');
			$data['scan']['href'] = $this->url->link($this->_route . '/viewScan', 'scan_id=' . $data['scan']['scan_id'] . '&token=' . $this->session->data['token'], 'SSL');
			$data['scan']['date_key']       = $date_key;

			return $this->load->view($this->_route . '/widget', $data);
		} else {
			return;
		}
	}

	/**
	 * ajax reload widget
	 * @return string output of widget function
	 **/
	public function reloadWidget() {
		$this->language->load($this->_route);

		if ($this->validateScan()) {
			$this->createScan($this->language->get('text_dashboard_scan'));
			$this->response->setOutput($this->widget());
		}
	}

	/**
	 * delete scan
	 * @return void
	 **/
	public function delete() {
		$this->load->model($this->_route);
		$this->language->load($this->_route);

		$this->fs_scans = new Security\fs_scans();

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['scans'] && $this->user->hasPermission('modify', $this->_route)) {
			foreach ($this->request->post['scans'] as $key => $value) {
				$this->{$this->_model}->deleteScan((int) $value);
			}

			$response['success'] = $this->language->get('text_success_saved');
		} else {
			$response['error'] = $this->language->get('error_permission');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));
	}

	/**
	 * create scan
	 * @param 	string 	$name
	 * @return 	int 	scan_id
	 **/
	private function createScan($name) {
		$this->load->model($this->_route);

		$this->directory_scanner = new Security\directory_scanner();
		$this->fs_scans = new Security\fs_scans();
		
		// add include paths
		$include_paths   = array_map('trim', explode(PHP_EOL, $this->config->get('security_fs_include')));
		$include_paths[] = $this->config->get('security_fs_base_path');
		$this->directory_scanner->setIncludePaths($include_paths);
		
		// add exclude paths
		$exclude_paths = array_map('trim', explode(PHP_EOL, $this->config->get('security_fs_exclude')));
		$this->directory_scanner->setExcludePaths($exclude_paths);
		
		// add default replace path
		$this->directory_scanner->setReplacePath(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR);
		
		// add extensions
		$this->directory_scanner->setExtensions(array_map('trim', explode(PHP_EOL, $this->config->get('security_fs_extensions'))));

		$files = $this->directory_scanner->getFiles();
		$scan_size = $this->fs_scans->getScanSize($files);
		$scan_id = $this->{$this->_model}->addScan($name, $files, $scan_size);

		return $scan_id;
	}

	/**
	 * generate default settings for module
	 * @return void
	 **/
	public function generateDefaultSettings() {
		$this->load->model($this->_route);
		$this->language->load($this->_route);

		if ($this->user->hasPermission('modify', $this->_route)) {
			$this->{$this->_model}->install(true);

			$this->session->data['success'] = $this->language->get('text_success_saved');
			$this->response->redirect($this->url->link($this->_route . '/settings', 'token=' . $this->session->data['token'], true));
		} else {
			$this->session->data['error'] = $this->language->get('error_permission');
			$this->response->redirect($this->url->link($this->_route, 'token=' . $this->session->data['token'], 'SSL'));
		}
	}

	/**
	 * validate scan before create
	 * @return bool
	 **/
	private function validateScan() {
		if (!$this->user->hasPermission('modify', $this->_route)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * validate settings form data
	 * @return bool
	 **/
	private function validateSettings() {
		if (!$this->user->hasPermission('modify', $this->_route)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['security_fs_base_path'])) {
			$this->error['base_path'] = $this->language->get('error_base_path');
		}

		if (empty($this->request->post['security_fs_extensions'])) {
			$this->error['extensions'] = $this->language->get('error_extensions');
		}

		if (empty($this->request->post['security_fs_cron_access_key'])) {
			$this->error['access_key'] = $this->language->get('error_cron_access_key');
		}

		if (!$this->error) {
			return true;
		} else {
			if (!isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_form');
			}
			return false;
		}
	}

	/**
	 * format file_data for output
	 * @param 	string 	$file_name
	 * @param 	array 	$file_data
	 * @return 	array
	 **/
	private function formatFile($file_name, $file_data) {
		if (isset($file_data['diff'])) { //changed
			return [
				'filesize' => $this->humanizer->humanBytes($file_data['new']['filesize']) . $file_data['postfix'],
				'relpath' => str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name),
				'extension' => pathinfo($file_name, PATHINFO_EXTENSION),
				'filemtime' => date('Y.m.d H:i:s', $file_data['new']['filemtime']),
				'filectime' => date('Y.m.d H:i:s', $file_data['new']['filectime']),
				'fileperms' => substr(decoct($file_data['new']['fileperms']), -4),
				'crc' => $file_data['new']['crc'],
					'int_filesize' => $file_data['new']['filesize'],
					'int_filemtime' => $file_data['new']['filemtime'],
					'int_filectime' => $file_data['new']['filectime'],
				'diff' => $file_data['diff']
			];
		} else {
			return [
				'filesize' => $this->humanizer->humanBytes($file_data['filesize']),
				'relpath' => str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name),
				'extension' => pathinfo($file_name, PATHINFO_EXTENSION),
				'filemtime' => date('Y.m.d H:i:s', $file_data['filemtime']),
				'filectime' => date('Y.m.d H:i:s', $file_data['filectime']),
				'fileperms' => substr(decoct($file_data['fileperms']), -4),
				'crc' => $file_data['crc'],
					'int_filesize' => $file_data['filesize'],
					'int_filemtime' => $file_data['filemtime'],
					'int_filectime' => $file_data['filectime']
			];
		}
	}
	
	private static function searchForKeyValue($searchKey, $searchValue, $array) {
		foreach ($array as $key => $value) {
			if ($value[$searchKey] === $searchValue) {
				return $key;
			}
		}
		return false;
	}
}