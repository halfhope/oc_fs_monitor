<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */
class ControllerModuleFSMonitor extends Controller
{

    public function index()
    {
        $this->response->redirect($this->url->link('security/fs_monitor', 'token=' . $this->session->data['token'], true));
    }

}