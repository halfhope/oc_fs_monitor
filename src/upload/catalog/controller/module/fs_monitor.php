<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ControllerExtensionModuleFsMonitor extends ControllerModuleFsMonitor {}

class ControllerModuleFsMonitor extends Controller
{
	public 	$_version 				= '1.1.2';
	private $_module_route 			= 'module/fs_monitor';
	private $_model 				= 'model_module_fs_monitor';
	
	/**
	 * constructor
	 * @param 	object $registry
	 * @return 	void
	 **/
	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->load->language($this->_module_route);
		$this->load->model($this->_module_route);

		$this->humanizer = new Security\humanizer($registry);
		$this->directory_scanner = new Security\directory_scanner($registry);
		$this->fs_scans = new Security\fs_scans($registry);

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
	}

	/**
	 * run cron scan
	 * @return void
	 **/
	public function index()
	{
		// check access_key
		if (isset($this->request->get['access_key']) && $this->request->get['access_key'] == $this->config->get('security_fs_cron_access_key')) {

			$last_scan = $this->{$this->_model}->getLastScan();
			if (!$last_scan) {
				$this->addScan($this->language->get('text_initial_scan'));
			}

			$files = $this->directory_scanner->getFiles();
			$scan_size = $this->fs_scans->getScanSize($files);

			// Compare scans
			$current_scan = array(
				'scan_id' => 0,
				'scan_size' => (int) $scan_size,
				'user_name' => $this->language->get('text_cron_scan_user'),
				'name' => $this->language->get('text_cron_scan_name'),
				'date_added' => date('Y-m-d H:i:s'),
				'scan_data' => array(
					'scanned' => $files
				)
			);

			$scansDiff = $this->fs_scans->getScansDiff(array(
				$current_scan,
				$last_scan
			));

			$scan = $scansDiff[0];
			// End compare scans

			// notify administrator
			if ($scan['new_count'] || $scan['changed_count'] || $scan['deleted_count']) {

				// add scan
				if ($this->config->get('security_fs_cron_save')) {
					$scan_name = $this->language->get('text_cron_scan_name');
					$user_name = $this->language->get('text_cron_scan_user');
					$scan_id = $this->{$this->_model}->addScan($scan_name, $files, $scan_size, $user_name);
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
					$datetime_format = ($this->language->get('datetime_format') == 'datetime_format') ? $this->language->get('date_format_short') : $this->language->get('datetime_format');
					$message = sprintf($this->language->get('text_mail_header'), date($datetime_format, time())) . PHP_EOL . $message;
					if ($this->config->get('security_fs_cron_notify')) {

						if ($this->config->get('security_fs_cron_save')) {
							$link = HTTP_SERVER . $this->config->get('security_fs_admin_dir') .'/index.php?route=' . $this->_module_route . '/viewScan&scan_id=' . $scan['scan_id'];
							$message .= $this->language->get('text_mail_link') . '<a href="' . $link . '">' . $link . '</a>';
						}

						$mail                = new Mail();
						$mail->protocol      = $this->config->get('config_mail_protocol');
						$mail->parameter     = $this->config->get('config_mail_parameter');
						$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
						$mail->smtp_username = $this->config->get('config_mail_smtp_username');
						$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
						$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
						$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

						$mail->setTo($this->config->get('config_email'));
						$mail->setFrom($this->config->get('config_email'));
						$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
						$mail->setSubject(html_entity_decode($this->language->get('text_mail_subject'), ENT_QUOTES, 'UTF-8'));
						$mail->setHtml(nl2br($message));
						$mail->setText(strip_tags($message));
						$mail->send();
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
	private function addScan($name)
	{
		$files = $this->directory_scanner->getFiles();

		$scan_size = $this->fs_scans->getScanSize($files);
		
		$user_name = $this->language->get('text_cron_scan_user');
		
		$scan_id = $this->{$this->_model}->addScan($name, $files, $scan_size, $user_name);

		return $scan_id;
	}

}