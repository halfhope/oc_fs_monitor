<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */
class ControllerSecurityFsMonitorCron extends Controller
{
    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language('security/fs_monitor_cron');

        if (version_compare('2.1', substr(VERSION, 0, 3)) == 0) {

            $this->humanizer         = new Security\humanizer($registry);
            $this->directory_scanner = new Security\directory_scanner($registry);
            $this->fs_scans          = new Security\fs_scans($registry);

        } else {

            $this->load->library('security/humanizer');
            $this->load->library('security/directory_scanner');
            $this->load->library('security/fs_scans');

        }

        $this->load->model('security/fs_monitor_cron');

        // add include paths
        $include_paths   = array_map('trim', explode(PHP_EOL, $this->config->get('security_fs_include')));
        $include_paths[] = $this->config->get('security_fs_base_path');
        $this->directory_scanner->setIncludePaths($include_paths);

        // add exclude paths
        $exclude_paths = array_map('trim', explode(PHP_EOL, $this->config->get('security_fs_exclude')));
        $this->directory_scanner->setExcludePaths($exclude_paths);

        // add extensions
        $this->directory_scanner->setExtensions(array_map('trim', explode(PHP_EOL, $this->config->get('security_fs_extensions'))));

    }

    public function index()
    {
        // check access_key
        if (isset($this->request->get['access_key']) && $this->request->get['access_key'] == $this->config->get('security_fs_cron_access_key')) {

            $scans = $this->model_security_fs_monitor_cron->getScans();

            if (!$scans) {

                $this->addScan($this->language->get('text_initial_scan'));

                $this->recalculateScansData();

            }

            $last_scan = $this->model_security_fs_monitor_cron->getLastScan();

            $files = $this->directory_scanner->getFiles();

            $scan_size = $this->fs_scans->getScanSize($files);

                // Compare scans
                $current_scan = array(
                    'scan_id' => 0,
                    'scan_size' => (int) $scan_size,
                    'user_name' => $this->language->get('text_cron_scan_user'),
                    'name' => $this->language->get('text_cron_scan_name'),
                    'date_added' => time(),
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
            if ($scan['scan_data']['new_count'] || $scan['scan_data']['changed_count'] || $scan['scan_data']['deleted_count']) {

                // add scan
                if ($this->config->get('security_fs_cron_save')) {

                    $scan_id = $this->model_security_fs_monitor_cron->addScan($this->language->get('text_cron_scan_name'), $this->language->get('text_cron_scan_user'), $files, $scan_size);

                    $this->recalculateScansData();

                    $scan = $this->model_security_fs_monitor_cron->getLastScan();

                }

                $message = '';

                if ($scan['scan_data']['new_count']) {
                    $message .= sprintf('%d ' . $this->language->get('text_mail_new_files') . PHP_EOL, $scan['scan_data']['new_count']);
                }

                if ($scan['scan_data']['changed_count']) {
                    $message .= sprintf('%d ' . $this->language->get('text_mail_changed_files') . PHP_EOL, $scan['scan_data']['changed_count']);
                }

                if ($scan['scan_data']['deleted_count']) {
                    $message .= sprintf('%d ' . $this->language->get('text_mail_deleted_files') . PHP_EOL, $scan['scan_data']['deleted_count']);
                }

                if (!empty($message)) {
                    $message = sprintf($this->language->get('text_mail_header'), date($this->language->get('datetime_format'), (int) $scan['date_added'])) . PHP_EOL . $message;
                    if ($this->config->get('security_fs_cron_notify')) {

                        if ($this->config->get('security_fs_cron_save')) {
                            $link = HTTP_SERVER . 'admin/index.php?route=security/fs_monitor/view&scan_id=' . $scan['scan_id'];
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
                        $mail->setText($message);
                        $mail->send();
                    }
                    if (!$this->config->get('security_fs_cron_save')) {
                        $this->model_security_fs_monitor_cron->removeScanNotification();
                        $this->model_security_fs_monitor_cron->setScanNotification($message);
                    }
                }
            }
        } else {
            $this->response->redirect($this->url->link('common/home'));
        }
    }

    private function addScan($name)
    {
        $files = $this->directory_scanner->getFiles();

        $scan_size = $this->fs_scans->getScanSize($files);

        $scan_id = $this->model_security_fs_monitor_cron->addScan($name, $this->language->get('text_cron_scan_user'), $files, $scan_size);

        return $scan_id;
    }

    private function recalculateScansData()
    {
        $scans = $this->model_security_fs_monitor_cron->getScans();

        $scansDiff = $this->fs_scans->getScansDiff($scans);

        $this->model_security_fs_monitor_cron->updateScansData($scansDiff);

    }
}
