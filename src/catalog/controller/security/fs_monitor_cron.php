<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

include_once(DIR_SYSTEM . 'library/security/compatible_controller.php');

class ControllerSecurityFsMonitorCron extends CompatibleController
{
    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language('security/fs_monitor_cron');

        $this->compatibleLoadLibrary('security/humanizer');
        $this->compatibleLoadLibrary('security/directory_scanner');
        $this->compatibleLoadLibrary('security/fs_scans');

        $this->load->model('security/fs_monitor_cron');

        // add include paths
        $include_paths   = array_map('trim', explode(PHP_EOL, $this->config->get('security_fs_include')));
        $include_paths[] = $this->config->get('security_fs_base_path');
        if (!empty($include_paths)) {
            $this->directory_scanner->setIncludePaths($include_paths);
        }

        // add exclude paths
        $exclude_paths = array_map('trim', explode(PHP_EOL, $this->config->get('security_fs_exclude')));
        if (!empty($exclude_paths)) {
            $this->directory_scanner->setExcludePaths($exclude_paths);
        }

        // add default replace path
        $this->directory_scanner->setReplacePath(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR);
        
        // add extensions
        $extensions = $this->config->get('security_fs_extensions');
        if (!empty($extensions)) {
            $this->directory_scanner->setExtensions(array_map('trim', explode(PHP_EOL, $extensions)));
        }

    }

    public function index()
    {
        // check access_key
        if (isset($this->request->get['access_key']) && $this->request->get['access_key'] == $this->config->get('security_fs_cron_access_key')) {

            $scans = $this->model_security_fs_monitor_cron->getScans();

            if (!$scans) {

                $this->addScan($this->language->get('text_initial_scan'));

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

                    $scan_id = $this->model_security_fs_monitor_cron->addScan($this->language->get('text_cron_scan_name'), $this->language->get('text_cron_scan_user'), $files, $scan_size);

                    $scan = $this->model_security_fs_monitor_cron->getLastScan();

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
                            $link = HTTP_SERVER . $this->config->get('security_fs_admin_dir') .'/index.php?route=security/fs_monitor/view&scan_id=' . $scan['scan_id'];
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
            $this->compatibleRedirect($this->url->link('common/home'));
        }
    }

    private function addScan($name)
    {
        $files = $this->directory_scanner->getFiles();

        $scan_size = $this->fs_scans->getScanSize($files);

        $scan_id = $this->model_security_fs_monitor->addScan($name, $files, $scan_size);

        return $scan_id;
    }

}
