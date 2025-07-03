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
 * @package     Opencart\Catalog\Controller\Api
 * @version     1.0.0
 */

namespace Opencart\Catalog\Controller\Api;

use Opencart\System\Engine\Controller;

class DpdWebhook extends Controller {
    public function index(): void {
        $this->load->language('api/dpd_webhook');

        // Check if request is POST
        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $this->response->addHeader('HTTP/1.1 405 Method Not Allowed');
            $this->response->setOutput(json_encode(['error' => 'Only POST requests allowed']));
            return;
        }

        // Optional: Token validation
        $expected_token = $this->config->get('dpd_webhook_token');
        $received_token = $this->request->get['token'] ?? '';

        if ($expected_token && $received_token !== $expected_token) {
            $this->response->addHeader('HTTP/1.1 403 Forbidden');
            $this->response->setOutput(json_encode(['error' => 'Invalid token']));
            return;
        }

        // Get the JSON payload
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response->addHeader('HTTP/1.1 400 Bad Request');
            $this->response->setOutput(json_encode(['error' => 'Invalid JSON']));
            return;
        }

        // Log the event to file
        $log = new \Opencart\System\Library\Log('dpd_webhook.log');
        $log->write('Received DPD Webhook: ' . print_r($data, true));

        // TODO: Save to DB if needed
        // $this->db->query("INSERT INTO " . DB_PREFIX . "dpd_tracking_events SET ...");

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['status' => 'success']));
    }
}
