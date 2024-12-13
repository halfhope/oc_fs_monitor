<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

include_once(DIR_SYSTEM . 'library/security/compatible_controller.php');

class ControllerExtensionModuleFSMonitor extends CompatibleController
{

    public function index()
    {
        $this->compatibleRedirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function install(){
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'security/fs_monitor');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'security/fs_monitor');
    }

}