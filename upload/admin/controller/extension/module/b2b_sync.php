<?php

class ControllerExtensionModuleB2BSync extends Controller
{
    private $error = [];

    public function index(): void
    {
        $this->load->language('extension/module/b2b_sync');
        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] === 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_b2b_sync', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['action'] = $this->url->link('extension/module/b2b_sync', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['module_b2b_sync_status'] = $this->config->get('module_b2b_sync_status');
        $data['module_b2b_sync_feed_url'] = $this->config->get('module_b2b_sync_feed_url');
        $data['module_b2b_sync_margin'] = $this->config->get('module_b2b_sync_margin') ?: 0;

        $this->response->setOutput($this->load->view('extension/module/b2b_sync', $data));
    }

    public function sync(): void
    {
        if (!$this->user->hasPermission('modify', 'extension/module/b2b_sync')) {
            $this->response->addHeader('HTTP/1.1 403 Forbidden');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'error' => 'Permission denied.',
            ]));
            return;
        }

        $this->load->model('extension/module/b2b_sync');
        $result = $this->model_extension_module_b2b_sync->syncProducts();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));
    }

    protected function validate(): bool
    {
        if (!$this->user->hasPermission('modify', 'extension/module/b2b_sync')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
