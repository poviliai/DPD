<?php
namespace Opencart\Admin\Controller\Extension\Opencart\Shipping;

class Dhl extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('extension/shipping/dhl');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_setting_setting->editSetting('shipping_dhl', $this->request->post);
            $this->model_setting_setting->editSetting('dhl_user', $this->request->post);
            $this->model_setting_setting->editSetting('dhl_test', $this->request->post);
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping'));
        }

        // Стандартні поля
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_back'] = $this->language->get('button_back');

        // Вкладки та легенди
        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_access'] = $this->language->get('tab_access');
        $data['tab_test_mode'] = $this->language->get('tab_test_mode');
        $data['legend_shipper'] = $this->language->get('legend_shipper');
        $data['legend_app_credentials'] = $this->language->get('legend_app_credentials');
        $data['legend_access'] = $this->language->get('legend_access');
        $data['legend_test_mode'] = $this->language->get('legend_test_mode');
        $data['text_dhl_developer_link'] = $this->language->get('text_dhl_developer_link');

        // Поля налаштувань
        $fields = [
            'entry_status',
            'entry_status_frontend',
            'entry_shipper_name', 'entry_shipper_street', 'entry_shipper_home_number',
            'entry_shipper_postcode', 'entry_shipper_city', 'entry_shipper_tel',
            'entry_shipper_email', 'entry_shipper_contact_person',
            'entry_application_id', 'entry_application_token',
            'entry_dhl_user_account', 'entry_dhl_password_account', 'entry_dhl_product',
            'entry_dhl_ekp_number', 'entry_dhl_procedure', 'entry_dhl_participation', 'entry_dhl_billing_number',
            'entry_sandbox_mode', 'entry_developer_id', 'entry_developer_password',
            'entry_sandbox_key', 'entry_sandbox_secret', 'entry_sandbox_url',
            'entry_prod_key', 'entry_prod_secret', 'entry_prod_url',
            'entry_billing_number', 'entry_product', 'entry_ship_date'
        ];

        foreach ($fields as $field) {
            $data[$field] = $this->language->get($field);
        }

        // Значення з config
        $values = [
            'shipping_dhl_status', 'shipping_dhl_status_frontend',
            'shipping_dhl_shipper_name', 'shipping_dhl_shipper_street', 'shipping_dhl_shipper_home_number',
            'shipping_dhl_shipper_postcode', 'shipping_dhl_shipper_city', 'shipping_dhl_shipper_tel',
            'shipping_dhl_shipper_email', 'shipping_dhl_shipper_contact_person',
            'shipping_dhl_application_id', 'shipping_dhl_application_token',
            'dhl_user_account', 'dhl_password_account', 'dhl_product',
            'dhl_ekp_number', 'dhl_procedure', 'dhl_participation', 'dhl_billing_number',
            'dhl_test_mode', 'dhl_developer_id', 'dhl_developer_password',
            'shipping_dhl_sandbox_mode', 'shipping_dhl_sandbox_key', 'shipping_dhl_sandbox_secret', 'shipping_dhl_sandbox_url',
            'shipping_dhl_prod_key', 'shipping_dhl_prod_secret', 'shipping_dhl_prod_url',
            'shipping_dhl_billing_number', 'shipping_dhl_product', 'shipping_dhl_ship_date'
        ];

        foreach ($values as $key) {
            $data[$key] = $this->config->get($key);
        }

        // Посилання
        $data['action'] = $this->url->link('extension/opencart/shipping/dhl', 'user_token=' . $this->session->data['user_token']);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping');

        // Компоненти сторінки
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/opencart/shipping/dhl', $data));
    }
}
