<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */
class ControllerSecurityFsMonitor extends Controller
{
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language('security/fs_monitor');

        if (version_compare('2.1', substr(VERSION, 0, 3)) == 0) {

            $this->humanizer         = new Security\humanizer($registry);
            $this->directory_scanner = new Security\directory_scanner($registry);
            $this->fs_scans          = new Security\fs_scans($registry);

        } else {

            $this->load->library('security/humanizer');
            $this->load->library('security/directory_scanner');
            $this->load->library('security/fs_scans');

        }

        $this->load->model('security/fs_monitor');

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

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateScan()) {

            if (!isset($this->request->post['scan_name']) || empty($this->request->post['scan_name'])) {
                $this->session->data['error'] = $this->language->get('error_empty_name');

                $this->response->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
            }

            $scan_id = $this->addScan($this->request->post['scan_name']);

            $this->recalculateScansData();

            $this->session->data['success'] = $this->language->get('text_success_scan_created');

            $this->response->redirect($this->url->link('security/fs_monitor', 'scan_id=' . (int) $scan_id . '&token=' . $this->session->data['token'], 'SSL'));

        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['panel_title']   = $this->language->get('text_fs_monitor');

        // Button
        $data['button_scan']         = $this->language->get('button_scan');
        $data['button_settings']     = $this->language->get('button_settings');
        $data['button_scan_loading'] = $this->language->get('button_scan_loading');
        $data['button_save']         = $this->language->get('button_save');
        $data['button_delete']       = $this->language->get('button_delete');
        $data['button_cancel']       = $this->language->get('button_cancel');
        $data['button_view']         = $this->language->get('button_view');

        // Text
        $data['text_modal_title']           = $this->language->get('text_modal_title');
        $data['text_scan_name_placeholder'] = $this->language->get('text_scan_name_placeholder');

        // Label
        $data['text_label_scanned'] = $this->language->get('text_label_scanned');
        $data['text_label_new']     = $this->language->get('text_label_new');
        $data['text_label_changed'] = $this->language->get('text_label_changed');
        $data['text_label_deleted'] = $this->language->get('text_label_deleted');

        // Entry
        $data['entry_scan_name'] = $this->language->get('entry_scan_name');

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

        $scans = $this->model_security_fs_monitor->getScans();

        if (!$scans) {

            $this->addScan($this->language->get('text_initial_scan'));

            $this->recalculateScansData();

            $scans = $this->model_security_fs_monitor->getScans();

            $data['success'] = $this->language->get('text_success_scan_initial');
        }

        foreach ($scans as $key => $scan) {
            $date_key = $this->language->get('text_scans_on') . date_format(date_create($scan['date_added']), $this->language->get('text_date_format_short'));

            $scan['scan_data']['scan_size_compared_humanized'] = $this->humanizer->humanBytes($scan['scan_data']['scan_size_compared']);
            $scan['scan_data']['scan_size_humanized']          = $this->humanizer->humanBytes($scan['scan_data']['scan_size']);

            $data['scans'][$date_key][$key]                   = $scan;
            $data['scans'][$date_key][$key]['date_added_ago'] = $this->humanizer->humanDatePrecise($data['scans'][$date_key][$key]['date_added'], 'H:i:s');
            $data['scans'][$date_key][$key]['href']           = $this->url->link('security/fs_monitor/view', 'scan_id=' . $data['scans'][$date_key][$key]['scan_id'] . '&token=' . $this->session->data['token'], 'SSL');
        }

        $data['scan_notification'] = nl2br($this->model_security_fs_monitor->getScanNotification());

        $this->model_security_fs_monitor->removeScanNotification();

        $data['token'] = $this->session->data['token'];

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modules'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action_scan']     = $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_settings'] = $this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_delete']   = $this->url->link('security/fs_monitor/delete', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_cancel']   = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        if (version_compare('2.2', VERSION) <= 0) {
            $this->response->setOutput($this->load->view('security/fs_monitor', $data));
        } else {
            $this->response->setOutput($this->load->view('security/fs_monitor.tpl', $data));
        }
    }

    public function view()
    {

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title']   = $this->language->get('heading_title');
        $data['panel_title']     = $this->language->get('text_view');
        $data['button_cancel']   = $this->language->get('button_cancel');
        $data['button_settings'] = $this->language->get('button_settings');

        $data['text_label_scanned'] = $this->language->get('text_label_scanned');
        $data['text_label_new']     = $this->language->get('text_label_new');
        $data['text_label_changed'] = $this->language->get('text_label_changed');
        $data['text_label_deleted'] = $this->language->get('text_label_deleted');

        $data['text_column_name']   = $this->language->get('text_column_name');
        $data['text_column_type']   = $this->language->get('text_column_type');
        $data['text_column_size']   = $this->language->get('text_column_size');
        $data['text_column_mtime']  = $this->language->get('text_column_mtime');
        $data['text_column_ctime']  = $this->language->get('text_column_ctime');
        $data['text_column_rights'] = $this->language->get('text_column_rights');
        $data['text_column_crc']    = $this->language->get('text_column_crc');

        if (isset($this->request->get['scan_id'])) {
            $data['scan'] = $this->model_security_fs_monitor->getScan((int) $this->request->get['scan_id']);
        } else {
            $this->response->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
        }

        if ($data['scan']['scan_data']['scanned']) {
            foreach ($data['scan']['scan_data']['scanned'] as $file_name => $file_data) {
                $data['scan']['scan_data']['scanned'][$file_name]['filesize_humanized'] = $this->humanizer->humanBytes($file_data['filesize']);
                $data['scan']['scan_data']['scanned'][$file_name]['writable']           = is_file($file_name);
            }
        }
        if ($data['scan']['scan_data']['new']) {
            foreach ($data['scan']['scan_data']['new'] as $file_name => $file_data) {
                $data['scan']['scan_data']['new'][$file_name]['filesize_humanized'] = $this->humanizer->humanBytes($file_data['filesize']);
                if (isset($data['scan']['scan_data']['scanned'][$file_name]['writable'])) {
                    $data['scan']['scan_data']['new'][$file_name]['writable'] = $data['scan']['scan_data']['scanned'][$file_name]['writable'];
                } else {
                    $data['scan']['scan_data']['new'][$file_name]['writable'] = false;
                }
            }
        }
        if ($data['scan']['scan_data']['changed']) {
            foreach ($data['scan']['scan_data']['changed'] as $file_name => $file_data) {
                $data['scan']['scan_data']['changed'][$file_name]['new']['filesize_humanized'] = $this->humanizer->humanBytes($file_data['old']['filesize']);
                $postfix                                                                       = '';
                if (isset($file_data['diff']['filesize'])) {
                    if ($file_data['old']['filesize'] >= $file_data['new']['filesize']) {
                        $postfix = ' (+' . $this->humanizer->humanBytes(abs($file_data['new']['filesize'] - $file_data['old']['filesize'])) . ')';
                    } else {
                        $postfix = ' (-' . $this->humanizer->humanBytes(abs($file_data['new']['filesize'] - $file_data['old']['filesize'])) . ')';
                    }
                }
                $data['scan']['scan_data']['changed'][$file_name]['new']['filesize_humanized'] = $this->humanizer->humanBytes($file_data['new']['filesize']) . $postfix;
                if (isset($data['scan']['scan_data']['scanned'][$file_name]['writable'])) {
                    $data['scan']['scan_data']['changed'][$file_name]['writable'] = $data['scan']['scan_data']['scanned'][$file_name]['writable'];
                } else {
                    $data['scan']['scan_data']['changed'][$file_name]['writable'] = false;
                }
            }
        }
        if ($data['scan']['scan_data']['deleted']) {
            foreach ($data['scan']['scan_data']['deleted'] as $file_name => $file_data) {
                $data['scan']['scan_data']['deleted'][$file_name]['filesize_humanized'] = $this->humanizer->humanBytes($file_data['filesize']);

                if (isset($data['scan']['scan_data']['scanned'][$file_name]['writable'])) {
                    $data['scan']['scan_data']['deleted'][$file_name]['writable'] = $data['scan']['scan_data']['scanned'][$file_name]['writable'];
                } else {
                    $data['scan']['scan_data']['deleted'][$file_name]['writable'] = false;
                }
            }
        }

        $data['scan']['scan_data']['scan_size_compared_humanized'] = $this->humanizer->humanBytes($data['scan']['scan_data']['scan_size_compared']);
        $data['scan']['scan_data']['scan_size_humanized']          = $this->humanizer->humanBytes($data['scan']['scan_data']['scan_size']);
        $data['scan']['date_added_ago']                            = $this->humanizer->humanDatePrecise($data['scan']['date_added'], 'F j H:i:s');
        $data['scan']['href']                                      = $this->url->link('security/fs_monitor/view', 'scan_id=' . $data['scan']['scan_id'] . '&token=' . $this->session->data['token'], 'SSL');

        $data['token'] = $this->session->data['token'];

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modules'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_view'),
            'href' => $this->url->link('security/fs_monitor/view', 'scan_id=' . $data['scan']['scan_id'] . '&token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action_cancel']   = $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_settings'] = $this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_file']     = $this->url->link('security/fs_monitor/viewFile', 'token=' . $this->session->data['token'], 'SSL');

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        if (version_compare('2.2', VERSION) <= 0) {
            $this->response->setOutput($this->load->view('security/fs_monitor_view_scan', $data));
        } else {
            $this->response->setOutput($this->load->view('security/fs_monitor_view_scan.tpl', $data));
        }
    }

    public function settings()
    {

        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title') . ' - ' . $this->language->get('text_settings'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('security_fs', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success_saved');

            $this->response->redirect($this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['panel_title']   = $this->language->get('text_settings');

        $data['button_cancel']   = $this->language->get('button_cancel');
        $data['button_save']     = $this->language->get('button_save');
        $data['button_settings'] = $this->language->get('button_settings');
        $data['button_generate'] = $this->language->get('button_generate');

        $data['text_legend_scanner'] = $this->language->get('text_legend_scanner');
        $data['text_legend_cron']    = $this->language->get('text_legend_cron');

        // Entry
        $data['entry_base_path']       = $this->language->get('entry_base_path');
        $data['entry_extensions']      = $this->language->get('entry_extensions');
        $data['entry_extensions_help'] = $this->language->get('entry_extensions_help');
        $data['entry_include']         = $this->language->get('entry_include');
        $data['entry_exclude']         = $this->language->get('entry_exclude');

        $data['entry_cron_access_key']  = $this->language->get('entry_cron_access_key');
        $data['entry_cron_wget']        = $this->language->get('entry_cron_wget');
        $data['entry_cron_curl']        = $this->language->get('entry_cron_curl');
        $data['entry_cron_cli']         = $this->language->get('entry_cron_cli');
        $data['entry_cron_save']        = $this->language->get('entry_cron_save');
        $data['entry_cron_save_help']   = $this->language->get('entry_cron_save_help');
        $data['entry_cron_notify']      = $this->language->get('entry_cron_notify');
        $data['entry_cron_notify_help'] = $this->language->get('entry_cron_notify_help');

        $data['text_label_scanned'] = $this->language->get('text_label_scanned');
        $data['text_label_new']     = $this->language->get('text_label_new');
        $data['text_label_changed'] = $this->language->get('text_label_changed');
        $data['text_label_deleted'] = $this->language->get('text_label_deleted');

        $data['text_column_name']   = $this->language->get('text_column_name');
        $data['text_column_type']   = $this->language->get('text_column_type');
        $data['text_column_size']   = $this->language->get('text_column_size');
        $data['text_column_mtime']  = $this->language->get('text_column_mtime');
        $data['text_column_ctime']  = $this->language->get('text_column_ctime');
        $data['text_column_rights'] = $this->language->get('text_column_rights');
        $data['text_column_crc']    = $this->language->get('text_column_crc');

        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no']  = $this->language->get('text_no');

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

        $data['security_fs_cron_wget'] = '/usr/local/bin/wget -q -O- ' . str_replace('admin/', '', HTTP_SERVER) . 'index.php?route=security/fs_monitor_cron&access_key=';
        $data['security_fs_cron_curl'] = '/usr/local/bin/curl -s ' . str_replace('admin/', '', HTTP_SERVER) . 'index.php?route=security/fs_monitor_cron&access_key=';
        $data['security_fs_cron_cli']  = '/usr/local/bin/php -q ' . DIR_APPLICATION . 'index.php?route=security/fs_monitor_cron&access_key=';

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

        $data['token'] = $this->session->data['token'];

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modules'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_settings'),
            'href' => $this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action_cancel']   = $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_generate'] = $this->url->link('security/fs_monitor/generate', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_save']     = $this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], 'SSL');

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        if (version_compare('2.2', VERSION) <= 0) {
            $this->response->setOutput($this->load->view('security/fs_monitor_settings', $data));
        } else {
            $this->response->setOutput($this->load->view('security/fs_monitor_settings.tpl', $data));
        }
    }

    public function generate()
    {
        if ($this->user->hasPermission('modify', 'security/fs_monitor')) {
            $this->model_security_fs_monitor->checkAndInstall(true);

            $this->session->data['success'] = $this->language->get('text_success_saved');

            $this->response->redirect($this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], true));

        } else {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->response->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
        }
    }

    public function viewFile()
    {

        $this->document->setTitle($this->language->get('heading_title'));


        if ($this->user->hasPermission('modify', 'security/fs_monitor')) {

            if (isset($this->request->get['file_name'])) {
                $file_name = urldecode($this->request->get['file_name']);
                if (is_file($file_name)) {
                    $data['content'] = file_get_contents($file_name);
                }
            }

            if (empty($data['content'])) {
                $this->session->data['error'] = $this->language->get('error_permission');

                $this->response->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
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

            if (version_compare('2.2', VERSION) <= 0) {
                $this->response->setOutput($this->load->view('security/fs_monitor_view_file', $data));
            } else {
                $this->response->setOutput($this->load->view('security/fs_monitor_view_file.tpl', $data));
            }
        } else {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->response->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
        }
    }

    public function delete()
    {

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', 'security/fs_monitor')) {

            foreach ($this->request->post['scans'] as $key => $value) {
                $this->model_security_fs_monitor->deleteScan((int) $value);
            }

            $this->recalculateScansData();

            $this->session->data['success'] = $this->language->get('text_success_scans_deleted');

            $this->response->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));

        } else {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->response->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
        }

    }

    public function getmessages()
    {

        return array(
            0 => $this->model_security_fs_monitor->getScanNotification()
        );

    }

    public function geticon()
    {

        return 'fa fa-hdd-o';

    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'security/fs_monitor')) {
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

    private function validateScan()
    {
        if (!$this->user->hasPermission('modify', 'security/fs_monitor')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function addScan($name)
    {
        $files = $this->directory_scanner->getFiles();

        $scan_size = $this->fs_scans->getScanSize($files);

        $scan_id = $this->model_security_fs_monitor->addScan($name, $files, $scan_size);

        return $scan_id;
    }

    private function recalculateScansData()
    {
        $scans = $this->model_security_fs_monitor->getScans();

        $scansDiff = $this->fs_scans->getScansDiff($scans);

        $this->model_security_fs_monitor->updateScansData($scansDiff);

    }
}
?>