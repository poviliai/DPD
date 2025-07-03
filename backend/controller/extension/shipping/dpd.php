<?php
namespace Opencart\Admin\Controller\Extension\Shipping;

class Dpd extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('extension/shipping/dpd');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['action'] = '';
        $data['cancel'] = '';

        $this->response->setOutput($this->load->view('extension/shipping/dpd', $data));
    }
}
