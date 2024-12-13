<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ControllerModuleFsMonitor extends Controller {

	private $error = [];
	
	public 	$_version 			= '1.2';
	private	$_module_route 		= 'module/fs_monitor';
	private	$_dashboard_route	= 'dashboard/fs_monitor';
	private	$_extensions_route 	= 'extension/module';
	private	$_model 			= 'model_module_fs_monitor';

	/**
	 * install
	 * @return void
	 **/
	public function install() {
		$this->load->model($this->_module_route);
		$this->{$this->_model}->install(true);
	}

	/**
	 * uninstall
	 * @return void
	 **/
	public function uninstall() {
		$this->load->model($this->_module_route);
		$this->{$this->_model}->uninstall();
	}

	/**
	 * main section
	 * @return void
	 **/
	public function index() {
		$this->load->model($this->_module_route);
		$data = $this->language->load($this->_module_route);

		$this->humanizer = new Security\humanizer($this->registry);

		$this->document->setTitle($this->language->get('heading_title'));
		$data['panel_title']   = $this->language->get('text_fs_monitor');
		
		$this->{$this->_model}->install(false);

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateScan()) {
			if (!isset($this->request->post['scan_name']) || empty($this->request->post['scan_name'])) {
				$this->session->data['error'] = $this->language->get('error_empty_name');
				$this->response->redirect($this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL'));
			}

			$scan_id = $this->createScan($this->request->post['scan_name']);
			
			$this->session->data['success'] = $this->language->get('text_success_scan_created');
			$this->response->redirect($this->url->link($this->_module_route, 'scan_id=' . (int) $scan_id . '&token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_limit_admin');
		}

		$filter_data = [
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		];

		$pagination = new Pagination();
		$pagination->total = $this->{$this->_model}->getTotalScans();
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link($this->_module_route, 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

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

		foreach ($scans as $key => $scan) {
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
				$date_key = $this->language->get('text_scans_on') . ' ' . iconv('windows-1251', 'utf-8', strftime('%A %d. %B %Y', strtotime($scan['date_added'])));
			} else {
				$date_key = $this->language->get('text_scans_on') . ' ' . strftime('%A %d. %B %Y', strtotime($scan['date_added']));
			}

			$scan['scan_size_abs_humanized'] = $this->humanizer->humanBytes($scan['scan_size_abs']);
			$scan['scan_size_rel_humanized'] = $this->humanizer->humanBytes($scan['scan_size_rel']);

			$data['scans'][$date_key][$key]                   = $scan;
			$data['scans'][$date_key][$key]['date_added_ago'] = $this->humanizer->humanDatePrecise($data['scans'][$date_key][$key]['date_added'], 'H:i:s');
			$data['scans'][$date_key][$key]['href']           = $this->url->link($this->_module_route . '/viewScan', 'scan_id=' . $data['scans'][$date_key][$key]['scan_id'] . '&token=' . $this->session->data['token'], 'SSL');
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		];
	
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_modules'),
			'href' => $this->url->link($this->_extensions_route, 'token=' . $this->session->data['token'], 'SSL')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL')
		];

		$data['action_scan']     = $this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL');
		$data['action_init']     = $this->url->link($this->_module_route . '/init', 'token=' . $this->session->data['token'], 'SSL');
		$data['action_settings'] = $this->url->link($this->_module_route . '/settings', 'token=' . $this->session->data['token'], 'SSL');
		$data['action_delete']   = $this->url->link($this->_module_route . '/delete', 'token=' . $this->session->data['token'], 'SSL');
		$data['action_cancel']   = $this->url->link($this->_extensions_route, 'token=' . $this->session->data['token'], 'SSL');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if (version_compare('2.2', VERSION) >= 0) {
			$this->response->setOutput($this->load->view($this->_module_route . '/main.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view($this->_module_route . '/main', $data));
		}
	}

	/**
	 * view scan function
	 * @return void
	 **/
	public function viewScan() {
		$this->load->model($this->_module_route);
		$data = $this->language->load($this->_module_route);

		$this->humanizer = new Security\humanizer($this->registry);
		
		$this->document->setTitle($this->language->get('heading_title'));
		$data['panel_title']     = $this->language->get('text_view');

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->get['scan_id'])) {
			$data['scan'] = $this->{$this->_model}->getScan((int) $this->request->get['scan_id'], true);
		} else {
			$this->response->redirect($this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL'));
		}

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

		$data['scan']['scan_data'] = $scan_result;
		$data['scan']['scan_size_abs_humanized'] = $this->humanizer->humanBytes($data['scan']['scan_size_abs']);
		$data['scan']['scan_size_rel_humanized'] = $this->humanizer->humanBytes($data['scan']['scan_size_rel']);
		$data['scan']['date_added_ago'] = $this->humanizer->humanDatePrecise($data['scan']['date_added'], 'F j H:i:s');
		$data['scan']['href'] = $this->url->link($this->_module_route . '/viewScan', 'scan_id=' . $data['scan']['scan_id'] . '&token=' . $this->session->data['token'], 'SSL');

		$data['token'] = $this->session->data['token'];

		$data['breadcrumbs'] = [];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_modules'),
			'href' => $this->url->link($this->_extensions_route, 'token=' . $this->session->data['token'], 'SSL')
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL')
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_view'),
			'href' => $this->url->link($this->_module_route . '/viewScan', 'scan_id=' . $data['scan']['scan_id'] . '&token=' . $this->session->data['token'], 'SSL')
		];

		$data['action_cancel']   = $this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL');
		$data['action_settings'] = $this->url->link($this->_module_route . '/settings', 'token=' . $this->session->data['token'], 'SSL');
		$data['action_file']     = $this->url->link($this->_module_route . '/viewFile', 'token=' . $this->session->data['token'], 'SSL');
		$data['action_rename']   = $this->url->link($this->_module_route . '/rename', 'scan_id=' . (int) $data['scan']['scan_id'] . '&token=' . $this->session->data['token'], 'SSL');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if (version_compare('2.2', VERSION) >= 0) {
			$this->response->setOutput($this->load->view($this->_module_route . '/view_scan.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view($this->_module_route . '/view_scan', $data));
		}
	}

	/**
	 * settings section
	 * @return void
	 **/
	public function settings() {
		$data = $this->language->load($this->_module_route);

		$this->document->setTitle($this->language->get('heading_title') . ' - ' . $this->language->get('text_settings'));
		$data['panel_title']   = $this->language->get('text_settings');
		
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateSettings()) {
			$this->model_setting_setting->editSetting('security_fs', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success_saved');
			$this->response->redirect($this->url->link($this->_module_route . '/settings', 'token=' . $this->session->data['token'], true));
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['base_path'])) {
			$data['error_base_path'] = $this->error['base_path'];
		} else {
			$data['error_base_path'] = '';
		}

		if (isset($this->error['extensions'])) {
			$data['error_extensions'] = $this->error['extensions'];
		} else {
			$data['error_extensions'] = '';
		}

		if (isset($this->error['access_key'])) {
			$data['error_access_key'] = $this->error['access_key'];
		} else {
			$data['error_access_key'] = '';
		}

		if (isset($this->request->post['security_fs_admin_dir'])) {
			$data['security_fs_admin_dir'] = $this->request->post['security_fs_admin_dir'];
		} else {
			$data['security_fs_admin_dir'] = $this->config->get('security_fs_admin_dir');
		}

		if (isset($this->request->post['security_fs_base_path'])) {
			$data['security_fs_base_path'] = $this->request->post['security_fs_base_path'];
		} else {
			$data['security_fs_base_path'] = $this->config->get('security_fs_base_path');
		}

		if (isset($this->request->post['security_fs_extensions'])) {
			$data['security_fs_extensions'] = $this->request->post['security_fs_extensions'];
		} else {
			$data['security_fs_extensions'] = $this->config->get('security_fs_extensions');
		}

		if (isset($this->request->post['security_fs_include'])) {
			$data['security_fs_include'] = $this->request->post['security_fs_include'];
		} else {
			$data['security_fs_include'] = $this->config->get('security_fs_include');
		}

		if (isset($this->request->post['security_fs_exclude'])) {
			$data['security_fs_exclude'] = $this->request->post['security_fs_exclude'];
		} else {
			$data['security_fs_exclude'] = $this->config->get('security_fs_exclude');
		}

		if (isset($this->request->post['security_fs_cron_access_key'])) {
			$data['security_fs_cron_access_key'] = $this->request->post['security_fs_cron_access_key'];
		} else {
			$data['security_fs_cron_access_key'] = $this->config->get('security_fs_cron_access_key');
		}

		if (isset($this->request->post['security_fs_cron_save'])) {
			$data['security_fs_cron_save'] = $this->request->post['security_fs_cron_save'];
		} else {
			$data['security_fs_cron_save'] = $this->config->get('security_fs_cron_save');
		}

		if (isset($this->request->post['security_fs_cron_notify'])) {
			$data['security_fs_cron_notify'] = $this->request->post['security_fs_cron_notify'];
		} else {
			$data['security_fs_cron_notify'] = $this->config->get('security_fs_cron_notify');
		}

		$data['security_fs_cron_wget'] = '/usr/local/bin/wget -q -O- ' . str_replace($this->config->get('security_fs_admin_dir') . '/', '', HTTP_SERVER) . 'index.php?route=' . $this->_module_route . '&access_key=';
		$data['security_fs_cron_curl'] = '/usr/local/bin/curl -s ' . str_replace($this->config->get('security_fs_admin_dir') . '/', '', HTTP_SERVER) . 'index.php?route=' . $this->_module_route . '&access_key=';
		$data['security_fs_cron_cli']  = '/usr/local/bin/php -q ' . str_replace($this->config->get('security_fs_admin_dir') . '/', '', DIR_APPLICATION) . 'index.php?route=' . $this->_module_route . '&access_key=';

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_modules'),
			'href' => $this->url->link($this->_extensions_route, 'token=' . $this->session->data['token'], 'SSL')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL')
		];
		
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_settings'),
			'href' => $this->url->link($this->_module_route . '/settings', 'token=' . $this->session->data['token'], 'SSL')
		];

		$data['action_cancel']   = $this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL');
		$data['action_generate'] = $this->url->link($this->_module_route . '/generateDefaultSettings', 'token=' . $this->session->data['token'], 'SSL');
		$data['action_save']     = $this->url->link($this->_module_route . '/settings', 'token=' . $this->session->data['token'], 'SSL');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if (version_compare('2.2', VERSION) >= 0) {
			$this->response->setOutput($this->load->view($this->_module_route . '/settings.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view($this->_module_route . '/settings', $data));
		}
	}

	/**
	 * view file section
	 * @return void
	 **/
	public function viewFile() {
		$this->load->language($this->_module_route);
		
		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->user->hasPermission('modify', $this->_module_route)) {

			if (isset($this->request->get['file_name'])) {
				$file_name = urldecode($this->request->get['file_name']);
				if (file_exists($file_name) && is_file($file_name)) {
					$data['content'] = file_get_contents($file_name);
				}
			}

			if (empty($data['content'])) {
				$this->session->data['error'] = $this->language->get('error_permission');
				$this->response->redirect($this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL'));
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
					$data['mode'] = 'php';
					break;
				case 'twig':
					$data['mode'] = 'twig';
					break;
				case 'css':
					$data['mode'] = 'css';
					break;
				case 'js':
					$data['mode'] = 'javascript';
					break;
				default:
					$data['mode'] = 'php';
					break;
			}

			$data['heading_title'] = $file_name;

			if (version_compare('2.2', VERSION) >= 0) {
				$this->response->setOutput($this->load->view($this->_module_route . '/view_file.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view($this->_module_route . '/view_file', $data));
			}

		} else {
			$this->session->data['error'] = $this->language->get('error_permission');

			$this->response->redirect($this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL'));
		}
	}

	/**
	 * widget section
	 * @return string controller output
	 **/
	public function widget() {
		$this->load->model($this->_module_route);

		$data = $this->language->load($this->_module_route);
		$data += $this->language->load($this->_dashboard_route);

		$this->humanizer = new Security\humanizer($this->registry);

		$data['token'] = $this->session->data['token'];

		$data['reload_widget'] = html_entity_decode($this->url->link($this->_module_route . '/reloadWidget', 'token=' . $this->session->data['token'], 'SSL'), ENT_QUOTES, 'UTF-8');
		$data['view_all'] = $this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL');

		$scan = $this->{$this->_model}->getLastScan();

		if ($scan) {
			unset($data['scan_data']);

			$date_key = $this->language->get('text_scans_on') . date_format(date_create($scan['date_added']), $this->language->get('text_date_format_short'));

			$scan['scan_size_abs_humanized'] = $this->humanizer->humanBytes($scan['scan_size_abs']);
			$scan['scan_size_rel_humanized'] = $this->humanizer->humanBytes($scan['scan_size_rel']);

			$data['scan'] = $scan;
			$data['scan']['date_added_ago'] = $this->humanizer->humanDatePrecise($data['scan']['date_added'], 'H:i:s');
			$data['scan']['href'] = $this->url->link($this->_module_route . '/viewScan', 'scan_id=' . $data['scan']['scan_id'] . '&token=' . $this->session->data['token'], 'SSL');
			$data['scan']['date_key']       = $date_key;

			if (version_compare('2.2', VERSION) >= 0) {
				return $this->load->view($this->_module_route . '/widget.tpl', $data);
			} else {
				return $this->load->view($this->_module_route . '/widget', $data);
			}
		}else{
			return;
		}
	}

	/**
	 * ajax reload widget
	 * @return string output of widget function
	 **/
	public function reloadWidget() {
		$this->language->load($this->_module_route);

		if ($this->validateScan()) {
			$this->createScan($this->language->get('text_dashboard_scan'));
			$this->response->setOutput($this->widget());
		}
	}

	/**
	 * rename scan
	 * @return void
	 **/
	public function rename() {
		$this->load->model($this->_module_route);
		$this->language->load($this->_module_route);

		if ($this->user->hasPermission('modify', $this->_module_route)) {
			if (isset($this->request->post['scan_name']) && !empty($this->request->post['scan_name'])) {
				$scan_name = $this->request->post['scan_name'];
				$this->{$this->_model}->rename((int)$this->request->get['scan_id'], $scan_name);

				$this->session->data['success'] = $this->language->get('text_success_renamed');
				$this->response->redirect($this->url->link($this->_module_route . '/viewScan',  'scan_id=' . (int)$this->request->get['scan_id'] . '&token=' . $this->session->data['token'], true));
			} else {
				$this->session->data['error'] = $this->language->get('error_empty_name');
				$this->response->redirect($this->url->link($this->_module_route . '/viewScan',  'scan_id=' . (int)$this->request->get['scan_id'] . '&token=' . $this->session->data['token'], true));
			}

		} else {
			$this->session->data['error'] = $this->language->get('error_permission');
			$this->response->redirect($this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL'));
		}
	}

	/**
	 * delete scan
	 * @return void
	 **/
	public function delete() {
		$this->load->model($this->_module_route);
		$this->language->load($this->_module_route);

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['scans'] && $this->user->hasPermission('modify', $this->_module_route)) {
			foreach ($this->request->post['scans'] as $key => $value) {
				$this->{$this->_model}->deleteScan((int) $value);
			}

			$this->session->data['success'] = $this->language->get('text_success_scans_deleted');
			$this->response->redirect($this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL'));
		} else {
			$this->session->data['error'] = $this->language->get('error_permission');
			$this->response->redirect($this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL'));
		}

	}

	/**
	 * create scan
	 * @param 	string 	$name
	 * @return 	int 	scan_id
	 **/
	private function createScan($name) {
		$this->load->model($this->_module_route);

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
		$this->load->model($this->_module_route);
		$this->language->load($this->_module_route);

		if ($this->user->hasPermission('modify', $this->_module_route)) {
			$this->{$this->_model}->install(true);

			$this->session->data['success'] = $this->language->get('text_success_saved');
			$this->response->redirect($this->url->link($this->_module_route . '/settings', 'token=' . $this->session->data['token'], true));
		} else {
			$this->session->data['error'] = $this->language->get('error_permission');
			$this->response->redirect($this->url->link($this->_module_route, 'token=' . $this->session->data['token'], 'SSL'));
		}
	}

	/**
	 * validate scan before create
	 * @return bool
	 **/
	private function validateScan() {
		if (!$this->user->hasPermission('modify', $this->_module_route)) {
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
		if (!$this->user->hasPermission('modify', $this->_module_route)) {
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
				'filemtime' => date('d.m.Y H:i:s',$file_data['new']['filemtime']),
				'filectime' => date('d.m.Y H:i:s',$file_data['new']['filectime']),
				'fileperms' => substr(decoct($file_data['new']['fileperms']), -4),
				'crc' => $file_data['new']['crc'],
				'diff' => $file_data['diff']
			];
		} else {
			return [
				'filesize' => $this->humanizer->humanBytes($file_data['filesize']),
				'relpath' => str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name),
				'extension' => pathinfo($file_name, PATHINFO_EXTENSION),
				'filemtime' => date('d.m.Y H:i:s',$file_data['filemtime']),
				'filectime' => date('d.m.Y H:i:s',$file_data['filectime']),
				'fileperms' => substr(decoct($file_data['fileperms']), -4),
				'crc' => $file_data['crc']
			];
		}
	}
}