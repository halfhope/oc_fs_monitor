<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Catalog\Controller\Extension\FSMonitor\Module;
use \Opencart\Extension\FS_Monitor\System\Library\Security as Security;
use \Opencart\Extension\FS_Monitor\System\Library\Security\Notify as Notify;
class FSMonitor extends \Opencart\System\Engine\Controller {

	private $_route 			= 'extension/fs_monitor/module/fs_monitor';
	private $_model 			= 'model_extension_fs_monitor_module_fs_monitor';

	/**
	 * run cron scan
	 * @return void
	 **/
	public function index(): void {
		$this->load->model($this->_route);
		$this->load->language($this->_route);
		
		$this->humanizer = new Security\humanizer($this->registry);
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

		// check access_key
		if (isset($this->request->get['access_key']) && $this->request->get['access_key'] == $this->config->get('security_fs_cron_access_key')) {

			$last_scan = $this->{$this->_model}->getLastScan();
			if (!$last_scan) {
				$this->addScan($this->language->get('text_initial_scan'));
			}

			$files = $this->directory_scanner->getFiles();
			$scan_size = $this->fs_scans->getScanSize($files);

			// Compare scans
			$current_scan = [
				'scan_id' => 0,
				'scan_size' => (int) $scan_size,
				'user_name' => $this->language->get('text_cron_scan_user'),
				'name' => $this->language->get('text_cron_scan_name'),
				'date_added' => date('Y-m-d H:i:s'),
				'scan_data' => [
					'scanned' => $files
				]
			];

			$scansDiff = $this->fs_scans->getScansDiff([
				$current_scan,
				$last_scan
			]);

			$scan = $scansDiff[0];
			// End compare scans

			// notify administrator
			if ($scan['new_count'] || $scan['changed_count'] || $scan['deleted_count']) {
				// add scan
				if ($this->config->get('security_fs_cron_save')) {
					$scan_name = $this->language->get('text_cron_scan_name');
					$user_name = $this->language->get('text_cron_scan_user');
					$this->{$this->_model}->addScan($scan_name, $files, $scan_size, $user_name);
					$scan = $this->{$this->_model}->getLastScan();
				}

				$message = '';

				if ($scan['new_count']) {
					$message .= sprintf('%d ' . $this->language->get('text_mail_new_files') . PHP_EOL, $scan['new_count']);
				}

				if ($scan['changed_count']) {
					$message .= sprintf('%d ' . $this->language->get('text_mail_changed_files') . PHP_EOL, $scan['changed_count']);
				}

				if ($scan['deleted_count']) {
					$message .= sprintf('%d ' . $this->language->get('text_mail_deleted_files') . PHP_EOL, $scan['deleted_count']);
				}

				if (!empty($message)) {
					$security_fs_notify_to = $this->config->get('security_fs_notify_to');

					$datetime_format = ($this->language->get('datetime_format') == 'datetime_format') ? $this->language->get('date_format_short') : $this->language->get('datetime_format');
					$message = sprintf($this->language->get('text_mail_header'), date($datetime_format, time())) . PHP_EOL . PHP_EOL . $message;
					
					if ($this->config->get('security_fs_cron_notify')) {

						if ($this->config->get('security_fs_cron_save')) {
							$link = HTTP_SERVER . $this->config->get('security_fs_admin_dir') .'/index.php?route=' . $this->_route . '/viewScan&scan_id=' . $scan['scan_id'];

							if ($security_fs_notify_to !== 'mail') {
								$message .= PHP_EOL . $this->language->get('text_mail_link') . $link;
							} else {
								$message .= PHP_EOL . $this->language->get('text_mail_link') . '<a href="' . $link . '">' . $link . '</a>';
							}
						}
					
						switch ($this->config->get('security_fs_notify_to')) {
							case 'email':
								$this->notify_email = new Notify\email();
								
								$this->notify_email->send([
									'config_mail_parameter' 	=> $this->config->get('config_mail_parameter'),
									'config_mail_smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
									'config_mail_smtp_username' => $this->config->get('config_mail_smtp_username'),
									'config_mail_smtp_password' => $this->config->get('config_mail_smtp_password'),
									'config_mail_smtp_port' 	=> $this->config->get('config_mail_smtp_port'),
									'config_mail_smtp_timeout' 	=> $this->config->get('config_mail_smtp_timeout'),
									'config_mail_engine' 		=> $this->config->get('config_mail_engine'),
									'security_fs_e_emails' 		=> $this->config->get('security_fs_e_emails'),
									'config_email' 				=> $this->config->get('config_email'),
									'config_name' 				=> $this->config->get('config_name'),
									'text_mail_subject' 		=> $this->language->get('text_mail_subject'),
									'message' 					=> $message,
								]);

								break;
							case 'whatsapp':

								$this->notify_whatsapp = new Notify\whatsApp();

								$this->notify_whatsapp->send([
									'security_fs_w_phone_number' 		=> $this->config->get('security_fs_w_phone_number'),
									'security_fs_w_business_account_id' => $this->config->get('security_fs_w_business_account_id'),
									'security_fs_w_api_token' 			=> $this->config->get('security_fs_w_api_token'),
									'message' 							=> $message
								]);

								break;
							case 'telegram':

								$this->notify_telegram = new Notify\telegram();

								$this->notify_telegram->send([
									'security_fs_t_api_token'	=> $this->config->get('security_fs_t_api_token'),
									'security_fs_t_channel_id'	=> $this->config->get('security_fs_t_channel_id'),
									'message' 					=> $message
								]);

								break;
						}

					}
				}
			}
		} else {
			$this->response->redirect($this->url->link('common/home'));
		}
	}

	/**
	 * create initial scan
	 * @param 	string 	$name
	 * @return 	int 	scan_id
	 **/
	private function addScan(string $name): int {
		$files = $this->directory_scanner->getFiles();
		$scan_size = $this->fs_scans->getScanSize($files);
		$user_name = $this->language->get('text_cron_scan_user');
		$scan_id = $this->{$this->_model}->addScan($name, $files, $scan_size, $user_name);
		return $scan_id;
	}

}