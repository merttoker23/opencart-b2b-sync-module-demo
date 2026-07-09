<?php

class ControllerExtensionModuleB2BSyncCron extends Controller
{
    public function index(): void
    {
        $token = $this->request->get['token'] ?? '';
        if ($token !== $this->config->get('module_b2b_sync_cron_token')) {
            $this->response->addHeader('HTTP/1.1 403 Forbidden');
            $this->response->setOutput('Forbidden');
            return;
        }

        $this->load->model('extension/module/b2b_supplier_sync');
        $result = $this->model_extension_module_b2b_supplier_sync->syncProducts();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));
    }
}
