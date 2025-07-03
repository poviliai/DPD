<?php
/**
 * DPD Shipping Module for OpenCart 4
 *
 * Developed for TELENORMA Holding AG
 * Website: https://telenorma.ag/
 * Hauptniederlassung Albstadt
 * Johannes-Brahms-Str. 4, 72461 Albstadt, Germany
 *
 * Created by: Vika Poviliai <v.poviliai@gmail.com>
 * Copyright © TELENORMA Holding AG
 *
 * @package     Opencart\Admin\Controller\Extension\Opencart\Shipping
 * @version     1.0.0
 */

namespace Opencart\Admin\Controller\Extension\Opencart\Shipping;

class Dpd extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('extension/opencart/shipping/dpd');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/opencart/shipping/dpd', 'user_token=' . $this->session->data['user_token'])
		];

        $data['save'] = $this->url->link('extension/opencart/shipping/dpd.save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping');
        $data['user_token'] = $this->session->data['user_token'];

        foreach ($this->request->post as $key => $value) {
            $data[$key] = $value;
        }

        $settings = $this->model_setting_setting->getSetting('shipping_dpd');
        foreach ($settings as $key => $value) {
            if (!isset($data[$key])) {
                $data[$key] = $value;
            }
        }
        

        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/opencart/shipping/dpd', $data));
    }

    public function save(): void {
        $this->load->language('extension/opencart/shipping/dpd');
        $json = [];

        if (!$this->user->hasPermission('modify', 'extension/opencart/shipping/dpd')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('shipping_dpd', $this->request->post);
            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
