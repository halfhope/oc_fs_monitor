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

        $this->load->library('security/humanizer');
        $humanizer = new Security\humanizer($registry);
        $registry->set('humanizer', $humanizer);

        $this->load->library('security/directory_scanner');
        $directory_scanner = new Security\directory_scanner($registry);
        $registry->set('directory_scanner', $directory_scanner);

        $this->load->library('security/fs_scans');
        $fs_scans = new Security\fs_scans($registry);
        $registry->set('fs_scans', $fs_scans);

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

        // modal
        $this->document->addScript('view/javascript/jquery/modal/jquery.modal.min.js');
        $this->document->addStyle('view/javascript/jquery/modal/jquery.modal.min.css');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateScan()) {

            if (!isset($this->request->post['scan_name']) || empty($this->request->post['scan_name'])) {
                $this->session->data['error'] = $this->language->get('error_empty_name');

                $this->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
            }

            $scan_id = $this->addScan($this->request->post['scan_name']);

            $this->recalculateScansData();

            $this->session->data['success'] = $this->language->get('text_success_scan_created');

            $this->redirect($this->url->link('security/fs_monitor', 'scan_id=' . (int) $scan_id . '&token=' . $this->session->data['token'], 'SSL'));

        }

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['panel_title']   = $this->language->get('text_fs_monitor');

        // Button
        $this->data['button_scan']         = $this->language->get('button_scan');
        $this->data['button_settings']     = $this->language->get('button_settings');
        $this->data['button_scan_loading'] = $this->language->get('button_scan_loading');
        $this->data['button_save']         = $this->language->get('button_save');
        $this->data['button_delete']       = $this->language->get('button_delete');
        $this->data['button_cancel']       = $this->language->get('button_cancel');
        $this->data['button_view']         = $this->language->get('button_view');

        // Text
        $this->data['text_modal_title']           = $this->language->get('text_modal_title');
        $this->data['text_scan_name_placeholder'] = $this->language->get('text_scan_name_placeholder');

        // Label
        $this->data['text_label_scanned'] = $this->language->get('text_label_scanned');
        $this->data['text_label_new']     = $this->language->get('text_label_new');
        $this->data['text_label_changed'] = $this->language->get('text_label_changed');
        $this->data['text_label_deleted'] = $this->language->get('text_label_deleted');

        // Entry
        $this->data['entry_scan_name'] = $this->language->get('entry_scan_name');

        if (isset($this->session->data['error'])) {
            $this->data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        $scans = $this->model_security_fs_monitor->getScans();

        if (!$scans) {

            $this->addScan($this->language->get('text_initial_scan'));

            $this->recalculateScansData();

            $scans = $this->model_security_fs_monitor->getScans();

            $this->data['success'] = $this->language->get('text_success_scan_initial');
        }

        foreach ($scans as $key => $scan) {
            $date_key = $this->language->get('text_scans_on') . date_format(date_create($scan['date_added']), $this->language->get('text_date_format_short'));

            $scan['scan_data']['scan_size_compared_humanized'] = $this->humanizer->humanBytes($scan['scan_data']['scan_size_compared']);
            $scan['scan_data']['scan_size_humanized']          = $this->humanizer->humanBytes($scan['scan_data']['scan_size']);

            $this->data['scans'][$date_key][$key]                   = $scan;
            $this->data['scans'][$date_key][$key]['date_added_ago'] = $this->humanizer->humanDatePrecise($this->data['scans'][$date_key][$key]['date_added'], 'H:i:s');
            $this->data['scans'][$date_key][$key]['href']           = $this->url->link('security/fs_monitor/view', 'scan_id=' . $this->data['scans'][$date_key][$key]['scan_id'] . '&token=' . $this->session->data['token'], 'SSL');
        }

        $this->data['scan_notification'] = nl2br($this->model_security_fs_monitor->getScanNotification());

        $this->model_security_fs_monitor->removeScanNotification();

        $this->data['token'] = $this->session->data['token'];

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modules'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action_scan']     = $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['action_settings'] = $this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['action_delete']   = $this->url->link('security/fs_monitor/delete', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['action_cancel']   = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        $this->template = 'security/fs_monitor.tpl';

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function view()
    {

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['heading_title']   = $this->language->get('heading_title');
        $this->data['panel_title']     = $this->language->get('text_view');
        $this->data['button_cancel']   = $this->language->get('button_cancel');
        $this->data['button_settings'] = $this->language->get('button_settings');

        $this->data['text_label_scanned'] = $this->language->get('text_label_scanned');
        $this->data['text_label_new']     = $this->language->get('text_label_new');
        $this->data['text_label_changed'] = $this->language->get('text_label_changed');
        $this->data['text_label_deleted'] = $this->language->get('text_label_deleted');

        $this->data['text_column_name']   = $this->language->get('text_column_name');
        $this->data['text_column_type']   = $this->language->get('text_column_type');
        $this->data['text_column_size']   = $this->language->get('text_column_size');
        $this->data['text_column_mtime']  = $this->language->get('text_column_mtime');
        $this->data['text_column_ctime']  = $this->language->get('text_column_ctime');
        $this->data['text_column_rights'] = $this->language->get('text_column_rights');
        $this->data['text_column_crc']    = $this->language->get('text_column_crc');

        if (isset($this->request->get['scan_id'])) {
            $this->data['scan'] = $this->model_security_fs_monitor->getScan((int) $this->request->get['scan_id']);
        } else {
            $this->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
        }

        if ($this->data['scan']['scan_data']['scanned']) {
            foreach ($this->data['scan']['scan_data']['scanned'] as $file_name => $file_data) {
                $this->data['scan']['scan_data']['scanned'][$file_name]['filesize_humanized'] = $this->humanizer->humanBytes($file_data['filesize']);
                $this->data['scan']['scan_data']['scanned'][$file_name]['writable']           = is_file($file_name);
            }
        }
        if ($this->data['scan']['scan_data']['new']) {
            foreach ($this->data['scan']['scan_data']['new'] as $file_name => $file_data) {
                $this->data['scan']['scan_data']['new'][$file_name]['filesize_humanized'] = $this->humanizer->humanBytes($file_data['filesize']);
                if (isset($this->data['scan']['scan_data']['scanned'][$file_name]['writable'])) {
                    $this->data['scan']['scan_data']['new'][$file_name]['writable'] = $this->data['scan']['scan_data']['scanned'][$file_name]['writable'];
                } else {
                    $this->data['scan']['scan_data']['new'][$file_name]['writable'] = false;
                }
            }
        }
        if ($this->data['scan']['scan_data']['changed']) {
            foreach ($this->data['scan']['scan_data']['changed'] as $file_name => $file_data) {
                $this->data['scan']['scan_data']['changed'][$file_name]['new']['filesize_humanized'] = $this->humanizer->humanBytes($file_data['old']['filesize']);
                $postfix                                                                       = '';
                if (isset($file_data['diff']['filesize'])) {
                    if ($file_data['old']['filesize'] >= $file_data['new']['filesize']) {
                        $postfix = ' (+' . $this->humanizer->humanBytes(abs($file_data['new']['filesize'] - $file_data['old']['filesize'])) . ')';
                    } else {
                        $postfix = ' (-' . $this->humanizer->humanBytes(abs($file_data['new']['filesize'] - $file_data['old']['filesize'])) . ')';
                    }
                }
                $this->data['scan']['scan_data']['changed'][$file_name]['new']['filesize_humanized'] = $this->humanizer->humanBytes($file_data['new']['filesize']) . $postfix;
                if (isset($this->data['scan']['scan_data']['scanned'][$file_name]['writable'])) {
                    $this->data['scan']['scan_data']['changed'][$file_name]['writable'] = $this->data['scan']['scan_data']['scanned'][$file_name]['writable'];
                } else {
                    $this->data['scan']['scan_data']['changed'][$file_name]['writable'] = false;
                }
            }
        }
        if ($this->data['scan']['scan_data']['deleted']) {
            foreach ($this->data['scan']['scan_data']['deleted'] as $file_name => $file_data) {
                $this->data['scan']['scan_data']['deleted'][$file_name]['filesize_humanized'] = $this->humanizer->humanBytes($file_data['filesize']);

                if (isset($this->data['scan']['scan_data']['scanned'][$file_name]['writable'])) {
                    $this->data['scan']['scan_data']['deleted'][$file_name]['writable'] = $this->data['scan']['scan_data']['scanned'][$file_name]['writable'];
                } else {
                    $this->data['scan']['scan_data']['deleted'][$file_name]['writable'] = false;
                }
            }
        }

        $this->data['scan']['scan_data']['scan_size_compared_humanized'] = $this->humanizer->humanBytes($this->data['scan']['scan_data']['scan_size_compared']);
        $this->data['scan']['scan_data']['scan_size_humanized']          = $this->humanizer->humanBytes($this->data['scan']['scan_data']['scan_size']);
        $this->data['scan']['date_added_ago']                            = $this->humanizer->humanDatePrecise($this->data['scan']['date_added'], 'F j H:i:s');
        $this->data['scan']['href']                                      = $this->url->link('security/fs_monitor/view', 'scan_id=' . $this->data['scan']['scan_id'] . '&token=' . $this->session->data['token'], 'SSL');

        $this->data['token'] = $this->session->data['token'];

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modules'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_view'),
            'href' => $this->url->link('security/fs_monitor/view', 'scan_id=' . $this->data['scan']['scan_id'] . '&token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action_cancel']   = $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['action_settings'] = $this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['action_file']     = $this->url->link('security/fs_monitor/viewFile', 'token=' . $this->session->data['token'], 'SSL');

        $this->template = 'security/fs_monitor_view_scan.tpl';

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function settings()
    {

        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title') . ' - ' . $this->language->get('text_settings'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('security_fs', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success_saved');

            $this->redirect($this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], true));
        }

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['panel_title']   = $this->language->get('text_settings');

        $this->data['button_cancel']   = $this->language->get('button_cancel');
        $this->data['button_save']     = $this->language->get('button_save');
        $this->data['button_settings'] = $this->language->get('button_settings');
        $this->data['button_generate'] = $this->language->get('button_generate');

        $this->data['text_legend_scanner'] = $this->language->get('text_legend_scanner');
        $this->data['text_legend_cron']    = $this->language->get('text_legend_cron');

        // Entry
        $this->data['entry_base_path']       = $this->language->get('entry_base_path');
        $this->data['entry_extensions']      = $this->language->get('entry_extensions');
        $this->data['entry_extensions_help'] = $this->language->get('entry_extensions_help');
        $this->data['entry_include']         = $this->language->get('entry_include');
        $this->data['entry_exclude']         = $this->language->get('entry_exclude');

        $this->data['entry_cron_access_key']  = $this->language->get('entry_cron_access_key');
        $this->data['entry_cron_wget']        = $this->language->get('entry_cron_wget');
        $this->data['entry_cron_curl']        = $this->language->get('entry_cron_curl');
        $this->data['entry_cron_cli']         = $this->language->get('entry_cron_cli');
        $this->data['entry_cron_save']        = $this->language->get('entry_cron_save');
        $this->data['entry_cron_save_help']   = $this->language->get('entry_cron_save_help');
        $this->data['entry_cron_notify']      = $this->language->get('entry_cron_notify');
        $this->data['entry_cron_notify_help'] = $this->language->get('entry_cron_notify_help');

        $this->data['text_label_scanned'] = $this->language->get('text_label_scanned');
        $this->data['text_label_new']     = $this->language->get('text_label_new');
        $this->data['text_label_changed'] = $this->language->get('text_label_changed');
        $this->data['text_label_deleted'] = $this->language->get('text_label_deleted');

        $this->data['text_column_name']   = $this->language->get('text_column_name');
        $this->data['text_column_type']   = $this->language->get('text_column_type');
        $this->data['text_column_size']   = $this->language->get('text_column_size');
        $this->data['text_column_mtime']  = $this->language->get('text_column_mtime');
        $this->data['text_column_ctime']  = $this->language->get('text_column_ctime');
        $this->data['text_column_rights'] = $this->language->get('text_column_rights');
        $this->data['text_column_crc']    = $this->language->get('text_column_crc');

        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no']  = $this->language->get('text_no');

        if (isset($this->session->data['error'])) {
            $this->data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        if (isset($this->error['base_path'])) {
            $this->data['error_base_path'] = $this->error['base_path'];
        } else {
            $this->data['error_base_path'] = '';
        }

        if (isset($this->error['extensions'])) {
            $this->data['error_extensions'] = $this->error['extensions'];
        } else {
            $this->data['error_extensions'] = '';
        }

        if (isset($this->error['access_key'])) {
            $this->data['error_access_key'] = $this->error['access_key'];
        } else {
            $this->data['error_access_key'] = '';
        }

        if (isset($this->request->post['security_fs_base_path'])) {
            $this->data['security_fs_base_path'] = $this->request->post['security_fs_base_path'];
        } else {
            $this->data['security_fs_base_path'] = $this->config->get('security_fs_base_path');
        }

        if (isset($this->request->post['security_fs_extensions'])) {
            $this->data['security_fs_extensions'] = $this->request->post['security_fs_extensions'];
        } else {
            $this->data['security_fs_extensions'] = $this->config->get('security_fs_extensions');
        }

        if (isset($this->request->post['security_fs_include'])) {
            $this->data['security_fs_include'] = $this->request->post['security_fs_include'];
        } else {
            $this->data['security_fs_include'] = $this->config->get('security_fs_include');
        }

        if (isset($this->request->post['security_fs_exclude'])) {
            $this->data['security_fs_exclude'] = $this->request->post['security_fs_exclude'];
        } else {
            $this->data['security_fs_exclude'] = $this->config->get('security_fs_exclude');
        }

        if (isset($this->request->post['security_fs_cron_access_key'])) {
            $this->data['security_fs_cron_access_key'] = $this->request->post['security_fs_cron_access_key'];
        } else {
            $this->data['security_fs_cron_access_key'] = $this->config->get('security_fs_cron_access_key');
        }

        $this->data['security_fs_cron_wget'] = '/usr/local/bin/wget -q -O- ' . str_replace('admin/', '', HTTP_SERVER) . 'index.php?route=security/fs_monitor_cron&access_key=';
        $this->data['security_fs_cron_curl'] = '/usr/local/bin/curl -s ' . str_replace('admin/', '', HTTP_SERVER) . 'index.php?route=security/fs_monitor_cron&access_key=';
        $this->data['security_fs_cron_cli']  = '/usr/local/bin/php -q ' . DIR_APPLICATION . 'index.php?route=security/fs_monitor_cron&access_key=';

        if (isset($this->request->post['security_fs_cron_save'])) {
            $this->data['security_fs_cron_save'] = $this->request->post['security_fs_cron_save'];
        } else {
            $this->data['security_fs_cron_save'] = $this->config->get('security_fs_cron_save');
        }

        if (isset($this->request->post['security_fs_cron_notify'])) {
            $this->data['security_fs_cron_notify'] = $this->request->post['security_fs_cron_notify'];
        } else {
            $this->data['security_fs_cron_notify'] = $this->config->get('security_fs_cron_notify');
        }

        $this->data['token'] = $this->session->data['token'];

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modules'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_settings'),
            'href' => $this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action_cancel']   = $this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['action_generate'] = $this->url->link('security/fs_monitor/generate', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['action_save']     = $this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], 'SSL');

        $this->template = 'security/fs_monitor_settings.tpl';

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function generate()
    {
        if ($this->user->hasPermission('modify', 'security/fs_monitor')) {
            $this->model_security_fs_monitor->checkAndInstall(true);

            $this->session->data['success'] = $this->language->get('text_success_saved');

            $this->redirect($this->url->link('security/fs_monitor/settings', 'token=' . $this->session->data['token'], true));

        } else {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
        }
    }

    public function viewFile()
    {

        $this->document->setTitle($this->language->get('heading_title'));


        if ($this->user->hasPermission('modify', 'security/fs_monitor')) {

            if (isset($this->request->get['file_name'])) {
                $file_name = urldecode($this->request->get['file_name']);
                if (is_file($file_name)) {
                    $this->data['content'] = file_get_contents($file_name);
                }
            }

            if (empty($this->data['content'])) {
                $this->session->data['error'] = $this->language->get('error_permission');

                $this->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
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
                    $this->data['mode'] = 'php';
                    break;
                case 'css':
                    $this->data['mode'] = 'css';
                    break;
                case 'js':
                    $this->data['mode'] = 'javascript';
                    break;
                default:
                    $this->data['mode'] = 'php';
                    break;
            }

            $this->data['heading_title'] = $file_name;

            $this->template = 'security/fs_monitor_view_file.tpl';

            $this->children = array(
                'common/header',
                'common/footer'
            );

            $this->response->setOutput($this->render());

        } else {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
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

            $this->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));

        } else {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
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