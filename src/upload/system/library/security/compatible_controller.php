<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class CompatibleController extends Controller {
    public $template_data;

    public function __construct($registry) {
        parent::__construct($registry);
    }

    public function compatibleGetChild($action, $params = array()) {

        if (version_compare('2', VERSION) >= 0) {
            return $this->getChild($action, $params);
        } else {
            return $this->load->controller($action, $params);
        }

    }

    public function compatibleLoadLibrary($route){
        // Sanitize the call
        $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);

        $file = DIR_SYSTEM . 'library/' . $route . '.php';
        $class = str_replace('/', '\\', $route);

        if (is_file($file)) {
            if (version_compare('2', VERSION) >= 0) {
                include_once($file);
            } else {
                include_once(modification($file));
            }

            $this->registry->set(basename($route), new $class($this->registry));

        } else {
            throw new \Exception('Error: Could not load library ' . $route . '!');
        }
    }

    public function compatibleRender($template, $data, $child = array(), $admin = false) {
        if (version_compare('2', VERSION) >= 0) {
            $this->data = $data;

            if (!empty($child)) {
                $this->children = $child;
            }

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $template .'.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/' . $template .'.tpl';
            } else {
                $this->template = ((!$admin) ? 'default/template/' : '') . $template .'.tpl';
            }

            return $this->render();
        } else {
            foreach ($child as $child) {
                $data[substr($child, strpos($child, '/') + 1)] = $this->load->controller($child);
            }

            if (version_compare('2.2', VERSION) >= 0) {
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $template .'.tpl')) {
                    return $this->load->view($this->config->get('config_template') . '/template/' . $template .'.tpl', $data);
                } else {
                    return $this->load->view( ((!$admin) ? 'default/template/' : '') . $template .'.tpl', $data);
                }
            } else {
                return $this->load->view($template, $data);
            }
        }
    }

    public function compatibleRedirect($url, $status = 302){
        if (version_compare('2', VERSION) >= 0) {
            $this->redirect($url, $status);
        } else {
            $this->response->redirect($url, $status);
        }
    }

    public function compatibleLanguageImage(&$languages){
        if (version_compare('2.2', VERSION) <= 0) {
            foreach ($languages as $key => $language) {
                $languages[$key]['img_src'] = 'language/' . $language['code'] . '/' . $language['code'] . '.png';
            }
        }else{
            foreach ($languages as $key => $language) {
                $languages[$key]['img_src'] = 'view/image/flags/' . $language['image'];
            }
        }
    }

}