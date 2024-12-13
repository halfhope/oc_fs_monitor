<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

include_once(DIR_SYSTEM . 'library/security/compatible_controller.php');

class ControllerExtensionModuleFsMonitor extends CompatibleController
{
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language('extension/module/fs_monitor');

        $this->compatibleLoadLibrary('security/humanizer');
        $this->compatibleLoadLibrary('security/directory_scanner');
        $this->compatibleLoadLibrary('security/fs_scans');

        $this->load->model('extension/module/fs_monitor');

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


    public function registerCron()
    {
        $this->load->model('setting/cron');
        
        $json = array();
        
        $cycle = $this->request->post['cycle'];
        
        $data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));
        
        $cron_link = $this->url->link('marketplace/cron', 'user_token=' . $this->session->data['user_token'], 'SSL');

        if ($this->user->hasPermission('modify', 'extension/module/fs_monitor')) {
            $cron_scan = $this->model_setting_cron->getCronByCode('fs_monitor');
            if (!empty($cron_scan)) {
                $json['error'] = sprintf($this->language->get('error_cron_job_installed'), $cron_link);
            }else{
                $cron_scan = $this->model_setting_cron->addCron('fs_monitor', $cycle, 'extension/module/fs_monitor/cron', true);
                if ($cron_scan) {
                    $json['success'] = sprintf($this->language->get('success_cron_job_installed'), $cron_link);
                }
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function install(){
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/fs_monitor');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/fs_monitor');
    }

    public function index()
    {

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateScan()) {

            if (!isset($this->request->post['scan_name']) || empty($this->request->post['scan_name'])) {
                $this->session->data['error'] = $this->language->get('error_empty_name');

                $this->compatibleRedirect($this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'));
            }

            $scan_id = $this->addScan($this->request->post['scan_name']);

            $this->session->data['success'] = $this->language->get('text_success_scan_created');

            $this->compatibleRedirect($this->url->link('extension/module/fs_monitor', 'scan_id=' . (int) $scan_id . '&user_token=' . $this->session->data['user_token'], 'SSL'));

        }

        $this->template_data['heading_title'] = $this->language->get('heading_title');
        $this->template_data['panel_title']   = $this->language->get('text_fs_monitor');

        // Button
        $this->template_data['button_scan']         = $this->language->get('button_scan');
        $this->template_data['button_settings']     = $this->language->get('button_settings');
        $this->template_data['button_scan_loading'] = $this->language->get('button_scan_loading');
        $this->template_data['button_save']         = $this->language->get('button_save');
        $this->template_data['button_delete']       = $this->language->get('button_delete');
        $this->template_data['button_cancel']       = $this->language->get('button_cancel');
        $this->template_data['button_view']         = $this->language->get('button_view');

        // Text
        $this->template_data['text_modal_title']           = $this->language->get('text_modal_title');
        $this->template_data['text_scan_name_placeholder'] = $this->language->get('text_scan_name_placeholder');

        // Label
        $this->template_data['text_label_scanned'] = $this->language->get('text_label_scanned');
        $this->template_data['text_label_new']     = $this->language->get('text_label_new');
        $this->template_data['text_label_changed'] = $this->language->get('text_label_changed');
        $this->template_data['text_label_deleted'] = $this->language->get('text_label_deleted');

        // Entry
        $this->template_data['entry_scan_name'] = $this->language->get('entry_scan_name');

        if (isset($this->session->data['error'])) {
            $this->template_data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $this->template_data['error_warning'] = $this->error['warning'];
        } else {
            $this->template_data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->template_data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->template_data['success'] = '';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data = array(
            'start'           => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit'           => $this->config->get('config_admin_limit')
        );

        $pagination = new Pagination();
        $pagination->total = $this->model_extension_module_fs_monitor->getTotalScans();
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_admin_limit');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'] . '&page={page}', 'SSL');

        $this->template_data['pagination'] = $pagination->render();

        $scans = $this->model_extension_module_fs_monitor->getScans($data);

        if (!$scans) {

            $this->addScan($this->language->get('text_initial_scan'));

            $scans = $this->model_extension_module_fs_monitor->getScans();

            $this->template_data['success'] = $this->language->get('text_success_scan_initial');
        }

        foreach ($scans as $key => $scan) {
            $date_key = $this->language->get('text_scans_on') . date_format(date_create($scan['date_added']), $this->language->get('text_date_format_short'));

            $scan['scan_size_abs_humanized'] = $this->humanizer->humanBytes($scan['scan_size_abs']);
            $scan['scan_size_rel_humanized'] = $this->humanizer->humanBytes($scan['scan_size_rel']);

            $this->template_data['scans'][$date_key][$key]                   = $scan;
            $this->template_data['scans'][$date_key][$key]['date_added_ago'] = $this->humanizer->humanDatePrecise($this->template_data['scans'][$date_key][$key]['date_added'], 'H:i:s');
            $this->template_data['scans'][$date_key][$key]['href']           = $this->url->link('extension/module/fs_monitor/view', 'scan_id=' . $this->template_data['scans'][$date_key][$key]['scan_id'] . '&user_token=' . $this->session->data['user_token'], 'SSL');
        }

        $this->template_data['user_token'] = $this->session->data['user_token'];

        $this->template_data['breadcrumbs'] = array();

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modules'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_fs_monitor'),
            'href' => $this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->template_data['action_init']     = $this->url->link('extension/module/fs_monitor/init', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $this->template_data['action_scan']     = $this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $this->template_data['action_settings'] = $this->url->link('extension/module/fs_monitor/settings', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $this->template_data['action_delete']   = $this->url->link('extension/module/fs_monitor/delete', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $this->template_data['action_cancel']   = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL');

        $child = array(
            'common/header',
            'common/column_left',
            'common/footer'
        );

        $this->response->setOutput($this->compatibleRender('extension/module/fs_monitor', $this->template_data, $child, true));
    }

    public function view()
    {

        $this->document->setTitle($this->language->get('heading_title'));

        $this->template_data['heading_title']   = $this->language->get('heading_title');
        $this->template_data['panel_title']     = $this->language->get('text_view');
        $this->template_data['button_rename']   = $this->language->get('button_rename');
        $this->template_data['button_rename_loading'] = $this->language->get('button_rename_loading');
        $this->template_data['button_cancel']   = $this->language->get('button_cancel');
        $this->template_data['button_settings'] = $this->language->get('button_settings');

        $this->template_data['text_label_scanned'] = $this->language->get('text_label_scanned');
        $this->template_data['text_label_new']     = $this->language->get('text_label_new');
        $this->template_data['text_label_changed'] = $this->language->get('text_label_changed');
        $this->template_data['text_label_deleted'] = $this->language->get('text_label_deleted');

        $this->template_data['text_column_name']   = $this->language->get('text_column_name');
        $this->template_data['text_column_type']   = $this->language->get('text_column_type');
        $this->template_data['text_column_size']   = $this->language->get('text_column_size');
        $this->template_data['text_column_mtime']  = $this->language->get('text_column_mtime');
        $this->template_data['text_column_ctime']  = $this->language->get('text_column_ctime');
        $this->template_data['text_column_rights'] = $this->language->get('text_column_rights');
        $this->template_data['text_column_crc']    = $this->language->get('text_column_crc');

        $this->template_data['text_modal_rename_title'] = $this->language->get('text_modal_rename_title');
        $this->template_data['text_scan_name_placeholder'] = $this->language->get('text_scan_name_placeholder');
        $this->template_data['entry_scan_name'] = $this->language->get('entry_scan_name');

        if (isset($this->session->data['error'])) {
            $this->template_data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $this->template_data['error_warning'] = $this->error['warning'];
        } else {
            $this->template_data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->template_data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->template_data['success'] = '';
        }

        if (isset($this->request->get['scan_id'])) {
            $this->template_data['scan'] = $this->model_extension_module_fs_monitor->getScan((int) $this->request->get['scan_id'], true);
        } else {
            $this->compatibleRedirect($this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        }

        if ($this->template_data['scan']['scan_data']['scanned']) {
            foreach ($this->template_data['scan']['scan_data']['scanned'] as $file_name => $file_data) {
                $this->template_data['scan']['scan_data']['scanned'][$file_name]['filesize_humanized'] = $this->humanizer->humanBytes($file_data['filesize']);
                $this->template_data['scan']['scan_data']['scanned'][$file_name]['writable']           = file_exists($file_name);
                $this->template_data['scan']['scan_data']['scanned'][$file_name]['relpath']           = str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name);
                $this->template_data['scan']['scan_data']['scanned'][$file_name]['extension']           = pathinfo($file_name, PATHINFO_EXTENSION);
                $this->template_data['scan']['scan_data']['scanned'][$file_name]['filemtime']           = date('d.m.Y H:i:s',$file_data['filemtime']);
                $this->template_data['scan']['scan_data']['scanned'][$file_name]['filectime']           = date('d.m.Y H:i:s',$file_data['filectime']);
                $this->template_data['scan']['scan_data']['scanned'][$file_name]['fileperms']           = substr(decoct($file_data['fileperms']), -4);
            }
        }
        if ($this->template_data['scan']['scan_data']['new']) {
            foreach ($this->template_data['scan']['scan_data']['new'] as $file_name => $file_data) {
                $this->template_data['scan']['scan_data']['new'][$file_name]['filesize_humanized'] = $this->humanizer->humanBytes($file_data['filesize']);
                $this->template_data['scan']['scan_data']['new'][$file_name]['relpath']           = str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name);

                $this->template_data['scan']['scan_data']['new'][$file_name]['extension']           = pathinfo($file_name, PATHINFO_EXTENSION);
                $this->template_data['scan']['scan_data']['new'][$file_name]['filemtime']           = date('d.m.Y H:i:s',$file_data['filemtime']);
                $this->template_data['scan']['scan_data']['new'][$file_name]['filectime']           = date('d.m.Y H:i:s',$file_data['filectime']);
                $this->template_data['scan']['scan_data']['new'][$file_name]['fileperms']           = substr(decoct($file_data['fileperms']), -4);

                if (isset($this->template_data['scan']['scan_data']['scanned'][$file_name]['writable'])) {
                    $this->template_data['scan']['scan_data']['new'][$file_name]['writable'] = $this->template_data['scan']['scan_data']['scanned'][$file_name]['writable'];
                } else {
                    $this->template_data['scan']['scan_data']['new'][$file_name]['writable'] = false;
                }
            }
        }
        if ($this->template_data['scan']['scan_data']['changed']) {
            foreach ($this->template_data['scan']['scan_data']['changed'] as $file_name => $file_data) {
                $this->template_data['scan']['scan_data']['changed'][$file_name]['new']['filesize_humanized'] = $this->humanizer->humanBytes($file_data['old']['filesize']);
                $postfix = '';
                if (isset($file_data['diff']['filesize'])) {
                    if ($file_data['old']['filesize'] >= $file_data['new']['filesize']) {
                        $postfix = ' (+' . $this->humanizer->humanBytes(abs($file_data['new']['filesize'] - $file_data['old']['filesize'])) . ')';
                    } else {
                        $postfix = ' (-' . $this->humanizer->humanBytes(abs($file_data['new']['filesize'] - $file_data['old']['filesize'])) . ')';
                    }
                }
                $this->template_data['scan']['scan_data']['changed'][$file_name]['relpath']           = str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name);
                $this->template_data['scan']['scan_data']['changed'][$file_name]['extension']           = pathinfo($file_name, PATHINFO_EXTENSION);
                $this->template_data['scan']['scan_data']['changed'][$file_name]['fileperms']           = substr(decoct($this->template_data['scan']['scan_data']['changed'][$file_name]['new']['fileperms']), -4);

                $this->template_data['scan']['scan_data']['changed'][$file_name]['new']['filemtime'] = date('d.m.Y H:i:s', $this->template_data['scan']['scan_data']['changed'][$file_name]['new']['filemtime']);
                $this->template_data['scan']['scan_data']['changed'][$file_name]['new']['filectime'] = date('d.m.Y H:i:s', $this->template_data['scan']['scan_data']['changed'][$file_name]['new']['filectime']);
                $this->template_data['scan']['scan_data']['changed'][$file_name]['new']['filesize_humanized'] = $this->humanizer->humanBytes($file_data['new']['filesize']) . $postfix;
                if (isset($this->template_data['scan']['scan_data']['scanned'][$file_name]['writable'])) {
                    $this->template_data['scan']['scan_data']['changed'][$file_name]['writable'] = $this->template_data['scan']['scan_data']['scanned'][$file_name]['writable'];
                } else {
                    $this->template_data['scan']['scan_data']['changed'][$file_name]['writable'] = false;
                }
            }
        }
        if ($this->template_data['scan']['scan_data']['deleted']) {
            foreach ($this->template_data['scan']['scan_data']['deleted'] as $file_name => $file_data) {
                $this->template_data['scan']['scan_data']['deleted'][$file_name]['filesize_humanized'] = $this->humanizer->humanBytes($file_data['filesize']);
                $this->template_data['scan']['scan_data']['deleted'][$file_name]['fileperms']           = substr(decoct($this->template_data['scan']['scan_data']['deleted'][$file_name]['fileperms']), -4);

                $this->template_data['scan']['scan_data']['deleted'][$file_name]['extension']           = pathinfo($file_name, PATHINFO_EXTENSION);
                $this->template_data['scan']['scan_data']['deleted'][$file_name]['filemtime']           = date('d.m.Y H:i:s',$file_data['filemtime']);
                $this->template_data['scan']['scan_data']['deleted'][$file_name]['filectime']           = date('d.m.Y H:i:s',$file_data['filectime']);
                $this->template_data['scan']['scan_data']['deleted'][$file_name]['relpath']           = str_replace(realpath(DIR_APPLICATION . '..') . DIRECTORY_SEPARATOR, '', $file_name);

                if (isset($this->template_data['scan']['scan_data']['scanned'][$file_name]['writable'])) {
                    $this->template_data['scan']['scan_data']['deleted'][$file_name]['writable'] = $this->template_data['scan']['scan_data']['scanned'][$file_name]['writable'];
                } else {
                    $this->template_data['scan']['scan_data']['deleted'][$file_name]['writable'] = false;
                }
            }
        }

        $this->template_data['scan']['scan_size_abs_humanized'] = $this->humanizer->humanBytes($this->template_data['scan']['scan_size_abs']);
        $this->template_data['scan']['scan_size_rel_humanized'] = $this->humanizer->humanBytes($this->template_data['scan']['scan_size_rel']);
        $this->template_data['scan']['date_added_ago']          = $this->humanizer->humanDatePrecise($this->template_data['scan']['date_added'], 'F j H:i:s');
        $this->template_data['scan']['href']                    = $this->url->link('extension/module/fs_monitor/view', 'scan_id=' . $this->template_data['scan']['scan_id'] . '&user_token=' . $this->session->data['user_token'], 'SSL');

        $this->template_data['user_token'] = $this->session->data['user_token'];

        $this->template_data['breadcrumbs'] = array();

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modules'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_fs_monitor'),
            'href' => $this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_view'),
            'href' => $this->url->link('extension/module/fs_monitor/view', 'scan_id=' . $this->template_data['scan']['scan_id'] . '&user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->template_data['action_cancel']   = $this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $this->template_data['action_settings'] = $this->url->link('extension/module/fs_monitor/settings', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $this->template_data['action_file']     = $this->url->link('extension/module/fs_monitor/viewFile', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $this->template_data['action_rename']   = $this->url->link('extension/module/fs_monitor/rename', 'scan_id=' . (int) $this->template_data['scan']['scan_id'] . '&user_token=' . $this->session->data['user_token'], 'SSL');

        $child = array(
            'common/header',
            'common/column_left',
            'common/footer'
        );

        $this->response->setOutput($this->compatibleRender('extension/module/fs_monitor_view_scan', $this->template_data, $child, true));
    }

    public function settings()
    {

        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title') . ' - ' . $this->language->get('text_settings'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('security_fs', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success_saved');

            $this->compatibleRedirect($this->url->link('extension/module/fs_monitor/settings', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->template_data['heading_title'] = $this->language->get('heading_title');
        $this->template_data['panel_title']   = $this->language->get('text_settings');

        $this->template_data['button_cancel']   = $this->language->get('button_cancel');
        $this->template_data['button_save']     = $this->language->get('button_save');
        $this->template_data['button_settings'] = $this->language->get('button_settings');
        $this->template_data['button_generate'] = $this->language->get('button_generate');

        // OpenCart cron jobs section
        $this->template_data['entry_cron_jobs']         = $this->language->get('entry_cron_jobs');
        $this->template_data['button_add_task']         = $this->language->get('button_add_task');
        $this->template_data['text_cron_interval_hour'] = $this->language->get('text_cron_interval_hour');
        $this->template_data['text_cron_interval_day']  = $this->language->get('text_cron_interval_day');
        $this->template_data['text_cron_interval_month']  = $this->language->get('text_cron_interval_month');
        
        $this->template_data['entry_cron_access_key']  = $this->language->get('entry_cron_access_key');
        $this->template_data['entry_cron_wget']        = $this->language->get('entry_cron_wget');
        $this->template_data['entry_cron_curl']        = $this->language->get('entry_cron_curl');
        $this->template_data['entry_cron_cli']         = $this->language->get('entry_cron_cli');
        $this->template_data['entry_cron_save']        = $this->language->get('entry_cron_save');
        $this->template_data['entry_cron_save_help']   = $this->language->get('entry_cron_save_help');
        $this->template_data['entry_cron_notify']      = $this->language->get('entry_cron_notify');
        $this->template_data['entry_cron_notify_help'] = $this->language->get('entry_cron_notify_help');

        // Legend
        $this->template_data['text_legend_module']          = $this->language->get('text_legend_module');
        $this->template_data['text_legend_scanner']         = $this->language->get('text_legend_scanner');
        $this->template_data['text_legend_cron_opencart']   = $this->language->get('text_legend_cron_opencart');

        // Entry
        $this->template_data['entry_admin_dir']       = $this->language->get('entry_admin_dir');
        $this->template_data['entry_base_path']       = $this->language->get('entry_base_path');
        $this->template_data['entry_extensions']      = $this->language->get('entry_extensions');
        $this->template_data['entry_extensions_help'] = $this->language->get('entry_extensions_help');
        $this->template_data['entry_include']         = $this->language->get('entry_include');
        $this->template_data['entry_exclude']         = $this->language->get('entry_exclude');


        $this->template_data['text_label_scanned'] = $this->language->get('text_label_scanned');
        $this->template_data['text_label_new']     = $this->language->get('text_label_new');
        $this->template_data['text_label_changed'] = $this->language->get('text_label_changed');
        $this->template_data['text_label_deleted'] = $this->language->get('text_label_deleted');

        $this->template_data['text_column_name']   = $this->language->get('text_column_name');
        $this->template_data['text_column_type']   = $this->language->get('text_column_type');
        $this->template_data['text_column_size']   = $this->language->get('text_column_size');
        $this->template_data['text_column_mtime']  = $this->language->get('text_column_mtime');
        $this->template_data['text_column_ctime']  = $this->language->get('text_column_ctime');
        $this->template_data['text_column_rights'] = $this->language->get('text_column_rights');
        $this->template_data['text_column_crc']    = $this->language->get('text_column_crc');

        $this->template_data['text_yes'] = $this->language->get('text_yes');
        $this->template_data['text_no']  = $this->language->get('text_no');

        if (isset($this->session->data['error'])) {
            $this->template_data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        } elseif (isset($this->error['warning'])) {
            $this->template_data['error_warning'] = $this->error['warning'];
        } else {
            $this->template_data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->template_data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $this->template_data['success'] = '';
        }

        if (isset($this->error['base_path'])) {
            $this->template_data['error_base_path'] = $this->error['base_path'];
        } else {
            $this->template_data['error_base_path'] = '';
        }

        if (isset($this->error['extensions'])) {
            $this->template_data['error_extensions'] = $this->error['extensions'];
        } else {
            $this->template_data['error_extensions'] = '';
        }

        if (isset($this->error['access_key'])) {
            $this->template_data['error_access_key'] = $this->error['access_key'];
        } else {
            $this->template_data['error_access_key'] = '';
        }

        if (isset($this->request->post['security_fs_admin_dir'])) {
            $this->template_data['security_fs_admin_dir'] = $this->request->post['security_fs_admin_dir'];
        } else {
            $this->template_data['security_fs_admin_dir'] = $this->config->get('security_fs_admin_dir');
        }

        if (isset($this->request->post['security_fs_base_path'])) {
            $this->template_data['security_fs_base_path'] = $this->request->post['security_fs_base_path'];
        } else {
            $this->template_data['security_fs_base_path'] = $this->config->get('security_fs_base_path');
        }

        if (isset($this->request->post['security_fs_extensions'])) {
            $this->template_data['security_fs_extensions'] = $this->request->post['security_fs_extensions'];
        } else {
            $this->template_data['security_fs_extensions'] = $this->config->get('security_fs_extensions');
        }

        if (isset($this->request->post['security_fs_include'])) {
            $this->template_data['security_fs_include'] = $this->request->post['security_fs_include'];
        } else {
            $this->template_data['security_fs_include'] = $this->config->get('security_fs_include');
        }

        if (isset($this->request->post['security_fs_exclude'])) {
            $this->template_data['security_fs_exclude'] = $this->request->post['security_fs_exclude'];
        } else {
            $this->template_data['security_fs_exclude'] = $this->config->get('security_fs_exclude');
        }

        // Cron
        if (isset($this->request->post['security_fs_cron_save'])) {
            $this->template_data['security_fs_cron_save'] = $this->request->post['security_fs_cron_save'];
        } else {
            $this->template_data['security_fs_cron_save'] = $this->config->get('security_fs_cron_save');
        }

        if (isset($this->request->post['security_fs_cron_notify'])) {
            $this->template_data['security_fs_cron_notify'] = $this->request->post['security_fs_cron_notify'];
        } else {
            $this->template_data['security_fs_cron_notify'] = $this->config->get('security_fs_cron_notify');
        }

        if (isset($this->request->post['security_fs_cron_access_key'])) {
            $this->template_data['security_fs_cron_access_key'] = $this->request->post['security_fs_cron_access_key'];
        } else {
            $this->template_data['security_fs_cron_access_key'] = $this->config->get('security_fs_cron_access_key');
        }

        $this->template_data['security_fs_cron_wget'] = '/usr/local/bin/wget -q -O- ' . str_replace($this->config->get('security_fs_admin_dir') . '/', '', HTTP_SERVER) . 'index.php?route=security/fs_monitor_cron&access_key=';
        $this->template_data['security_fs_cron_curl'] = '/usr/local/bin/curl -s ' . str_replace($this->config->get('security_fs_admin_dir') . '/', '', HTTP_SERVER) . 'index.php?route=security/fs_monitor_cron&access_key=';
        $this->template_data['security_fs_cron_cli']  = '/usr/local/bin/php -q ' . str_replace($this->config->get('security_fs_admin_dir') . '/', '', DIR_APPLICATION) . 'index.php?route=security/fs_monitor_cron&access_key=';
        
        // End cron

        $this->template_data['user_token'] = $this->session->data['user_token'];

        $this->template_data['breadcrumbs'] = array();

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_modules'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->template_data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_settings'),
            'href' => $this->url->link('extension/module/fs_monitor/settings', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->template_data['action_cancel']   = $this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $this->template_data['action_generate'] = $this->url->link('extension/module/fs_monitor/generate', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $this->template_data['action_save']     = $this->url->link('extension/module/fs_monitor/settings', 'user_token=' . $this->session->data['user_token'], 'SSL');

        $child = array(
            'common/header',
            'common/column_left',
            'common/footer'
        );

        $this->response->setOutput($this->compatibleRender('extension/module/fs_monitor_settings', $this->template_data, $child, true));

    }

    public function generate()
    {
        if ($this->user->hasPermission('modify', 'extension/module/fs_monitor')) {
            $this->model_extension_module_fs_monitor->checkAndInstall(true);

            $this->session->data['success'] = $this->language->get('text_success_saved');

            $this->compatibleRedirect($this->url->link('extension/module/fs_monitor/settings', 'user_token=' . $this->session->data['user_token'], true));

        } else {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->compatibleRedirect($this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        }
    }

    public function rename()
    {
        if ($this->user->hasPermission('modify', 'extension/module/fs_monitor')) {
            if (isset($this->request->post['scan_name']) && !empty($this->request->post['scan_name'])) {

                $scan_name = $this->request->post['scan_name'];

                $this->model_extension_module_fs_monitor->rename((int)$this->request->get['scan_id'], $scan_name);

                $this->session->data['success'] = $this->language->get('text_success_renamed');

                $this->compatibleRedirect($this->url->link('extension/module/fs_monitor/view',  'scan_id=' . (int)$this->request->get['scan_id'] . '&user_token=' . $this->session->data['user_token'], true));

            } else {

                $this->session->data['error'] = $this->language->get('error_empty_name');

                $this->compatibleRedirect($this->url->link('extension/module/fs_monitor/view',  'scan_id=' . (int)$this->request->get['scan_id'] . '&user_token=' . $this->session->data['user_token'], true));

            }

        } else {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->compatibleRedirect($this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        }
    }

    public function cron()
    {
 
        $scans = $this->model_extension_module_fs_monitor->getScans();

        if (!$scans) {

            $this->addScan($this->language->get('text_initial_scan'));

        }

        $last_scan = $this->model_extension_module_fs_monitor->getLastScan();

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

                $scan_id = $this->model_extension_module_fs_monitor->addScan($this->language->get('text_cron_scan_name'), $files, $scan_size, $this->language->get('text_cron_scan_user'));
                $scan = $this->model_extension_module_fs_monitor->getLastScan();

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
                        $link = HTTP_SERVER . $this->config->get('security_fs_admin_dir') .'/index.php?route=extension/module/fs_monitor/view&scan_id=' . $scan['scan_id'];
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
    }

    public function viewFile()
    {

        $this->document->setTitle($this->language->get('heading_title'));


        if ($this->user->hasPermission('modify', 'extension/module/fs_monitor')) {

            if (isset($this->request->get['file_name'])) {
                $file_name = urldecode($this->request->get['file_name']);
                if (file_exists($file_name) && is_file($file_name)) {
                    $this->template_data['content'] = file_get_contents($file_name);
                }
            }

            if (empty($this->template_data['content'])) {
                $this->session->data['error'] = $this->language->get('error_permission');

                $this->compatibleRedirect($this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'));
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
                    $this->template_data['mode'] = 'php';
                    break;
                case 'css':
                    $this->template_data['mode'] = 'css';
                    break;
                case 'js':
                    $this->template_data['mode'] = 'javascript';
                    break;
                case 'sql':
                    $this->template_data['mode'] = 'sql';
                    break;
                case 'twig':
                    $this->template_data['mode'] = 'twig';
                    break;
                default:
                    $this->template_data['mode'] = 'php';
                    break;
            }

            $this->template_data['heading_title'] = $file_name;

            $child = array(
                'common/header',
                'common/footer'
            );

            $this->response->setOutput($this->compatibleRender('extension/module/fs_monitor_view_file', $this->template_data, $child, true));

        } else {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->compatibleRedirect($this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        }
    }

    public function delete()
    {

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['scans'] && $this->user->hasPermission('modify', 'extension/module/fs_monitor')) {

            foreach ($this->request->post['scans'] as $key => $value) {
                $this->model_extension_module_fs_monitor->deleteScan((int) $value);
            }

            $this->session->data['success'] = $this->language->get('text_success_scans_deleted');

            $this->compatibleRedirect($this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'));

        } else {
            $this->session->data['error'] = $this->language->get('error_permission');

            $this->compatibleRedirect($this->url->link('extension/module/fs_monitor', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        }

    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/fs_monitor')) {
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
        if (!$this->user->hasPermission('modify', 'extension/module/fs_monitor')) {
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

        $scan_id = $this->model_extension_module_fs_monitor->addScan($name, $files, $scan_size);

        return $scan_id;
    }

}
?>