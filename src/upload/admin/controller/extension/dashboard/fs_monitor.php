<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

include_once(DIR_SYSTEM . 'library/security/compatible_controller.php');

class ControllerExtensionDashboardFSMonitor extends CompatibleController
{
	private $error = array();

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->load->language('security/fs_monitor');
		$this->load->language('extension/dashboard/fs_monitor');
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_fs_monitor', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/dashboard/fs_monitor', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/dashboard/fs_monitor', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true);

		if (isset($this->request->post['dashboard_fs_monitor_width'])) {
			$data['dashboard_fs_monitor_width'] = $this->request->post['dashboard_fs_monitor_width'];
		} else {
			$data['dashboard_fs_monitor_width'] = $this->config->get('dashboard_fs_monitor_width');
		}
	
		$data['columns'] = array();
		
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}
				
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

		$this->response->setOutput($this->load->view('security/fs_monitor_widget_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/dashboard/fs_monitor')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function dashboard() {
		return $this->compatibleGetChild('security/fs_monitor/widget');
	}
}