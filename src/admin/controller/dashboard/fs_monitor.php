<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Admin\Controller\Extension\FSMonitor\Dashboard;
class FSMonitor extends \Opencart\System\Engine\Controller {

	private	$_route 			= 'extension/fs_monitor/module/fs_monitor';
	public 	$_version 			= '1.2.3.1';
	private	$_dashboard_route 	= 'extension/fs_monitor/dashboard/fs_monitor';
	private	$_extensions_route 	= 'marketplace/extension';

	private $error = [];

	public function index(): void {
		
		$this->load->language($this->_dashboard_route);

		$this->document->setTitle($this->language->get('heading_title'));
		$data['heading_title'] = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_fs_monitor', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->_extensions_route, 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true));
		}

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][]  = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];
		
		$data['breadcrumbs'][]  = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->_extensions_route, 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true)
		];

		$data['breadcrumbs'][]  = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->_dashboard_route, 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['action'] = $this->url->link($this->_dashboard_route, 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link($this->_extensions_route, 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true);

		if (isset($this->request->post['dashboard_fs_monitor_width'])) {
			$data['dashboard_fs_monitor_width'] = $this->request->post['dashboard_fs_monitor_width'];
		} else {
			$data['dashboard_fs_monitor_width'] = $this->config->get('dashboard_fs_monitor_width');
		}
	
		$data['columns'] = [6, 12];
			
		if (isset($this->request->post['dashboard_fs_monitor_status'])) {
			$data['dashboard_fs_monitor_status'] = $this->request->post['dashboard_fs_monitor_status'];
		} else {
			$data['dashboard_fs_monitor_status'] = $this->config->get('dashboard_fs_monitor_status');
		}

		if (isset($this->request->post['dashboard_fs_monitor_sort_order'])) {
			$data['dashboard_fs_monitor_sort_order'] = $this->request->post['dashboard_fs_monitor_sort_order'];
		} else {
			$data['dashboard_fs_monitor_sort_order'] = $this->config->get('dashboard_fs_monitor_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view($this->_route . '/widget_form', $data));
	}

	protected function validate(): bool {
		if (!$this->user->hasPermission('modify', $this->_dashboard_route)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function dashboard(): string {
		return $this->load->controller($this->_route . '|widget');
	}
}