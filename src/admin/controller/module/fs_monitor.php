<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

include_once(DIR_SYSTEM . 'library/security/compatible_controller.php');

class ControllerModuleFSMonitor extends CompatibleController
{

    public function index()
    {
        $this->compatibleRedirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], 'SSL'));
    }

}