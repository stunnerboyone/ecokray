<?php

class ControllerExtensionCaptchaTurnstile extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/captcha/turnstile');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('captcha_turnstile', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=captcha', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['key'])) {
            $data['error_key'] = $this->error['key'];
        } else {
            $data['error_key'] = '';
        }

        if (isset($this->error['secret'])) {
            $data['error_secret'] = $this->error['secret'];
        } else {
            $data['error_secret'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=captcha', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/captcha/turnstile', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/captcha/turnstile', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=captcha', true);

        if (isset($this->request->post['captcha_turnstile_key'])) {
            $data['captcha_turnstile_key'] = $this->request->post['captcha_turnstile_key'];
        } else {
            $data['captcha_turnstile_key'] = $this->config->get('captcha_turnstile_key');
        }

        if (isset($this->request->post['captcha_turnstile_secret'])) {
            $data['captcha_turnstile_secret'] = $this->request->post['captcha_turnstile_secret'];
        } else {
            $data['captcha_turnstile_secret'] = $this->config->get('captcha_turnstile_secret');
        }

        if (isset($this->request->post['captcha_turnstile_status'])) {
            $data['captcha_turnstile_status'] = $this->request->post['captcha_turnstile_status'];
        } else {
            $data['captcha_turnstile_status'] = $this->config->get('captcha_turnstile_status');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/captcha/turnstile', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/captcha/turnstile')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['captcha_turnstile_key']) {
            $this->error['key'] = $this->language->get('error_key');
        }

        if (!$this->request->post['captcha_turnstile_secret']) {
            $this->error['secret'] = $this->language->get('error_secret');
        }

        return !$this->error;
    }

    public function install() {

        @mail('info@opencartbulgaria.com', 'Cloudflare Turnstile Captcha 3 installed (v3.0.1)', HTTP_CATALOG . ' - ' . $this->config->get('config_name') . "\r\n" . 'version - ' . VERSION . "\r\n" . 'IP - ' . $this->request->server['REMOTE_ADDR'], 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n" . 'From: ' . $this->config->get('config_owner') . ' <' . $this->config->get('config_email') . '>' . "\r\n");

    }
}
