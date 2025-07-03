<?php
/**
 * DPD Shipping Backend Controller for OpenCart 4
 *
 * Developed for TELENORMA Holding AG
 * Website: https://telenorma.ag/
 * Hauptniederlassung Albstadt
 * Johannes-Brahms-Str. 4, 72461 Albstadt, Germany
 *
 * Created by: Vika Poviliai <v.poviliai@gmail.com>
 * Copyright © TELENORMA Holding AG
 *
 * @package     Opencart\Admin\Controller\Sale
 * @version     1.0.0
 */

namespace Opencart\Admin\Controller\Sale;

// на початку файлу (до class ...)
use setasign\Fpdi\Fpdi;

class DpdShipping extends \Opencart\System\Engine\Controller {

    private $dpd;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->dpd = $this->getDpdApi(); // ✅ ініціалізація тут
    }

    private function getDpdApi(): \DpdApi {
        require_once DIR_SYSTEM . 'library/dpd/api.php';
        
        $sandbox = $this->config->get('shipping_dpd_sandbox');
    
        $config = $sandbox ? [
            'username' => $this->config->get('shipping_dpd_stage_user'),
            'password' => $this->config->get('shipping_dpd_stage_pass'),
            'sandbox'  => true
        ] : [
            'username' => $this->config->get('shipping_dpd_prod_user'),
            'password' => $this->config->get('shipping_dpd_prod_pass'),
            'sandbox'  => false
        ];
    
        return new \DpdApi($config);
    }


    public function token(): void {
        $json = [];
        
        // Отримання токена
        $token = $this->dpd->getToken();

        if ($token) {
            $json['success'] = 'Token received.';
            $json['token'] = $token;
        } else {
            $this->dpd->log('[DPD] Token not received.');
            $json['error'] = 'Token not received.';
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    public function getRequestBody(int $weight = 100){

        $this->load->language('sale/order');
        $this->load->model('sale/order');
        $order_id = (int)($this->request->get['order_id'] ?? 0);
        $order_info = $this->model_sale_order->getOrder($order_id);

        //var_dump($order_info);

        if (!$order_info) {
            throw new \Exception('Order not found');
         }

         $data['username'] = $this->dpd->getUsername();
         $data['token']  =  $this->dpd->getToken();

         $data['order_id'] = $order_id;

         $data['recipient'] = [
            'name1'       => !empty($order_info['shipping_company']) ? $order_info['shipping_company'] : $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'],
            'name2'       => !empty($order_info['shipping_company']) ? $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'] : '',
            'street'      => $order_info['shipping_address_1'],
            'houseNo'     => $order_info['shipping_address_2'],
            'country'     => $order_info['shipping_iso_code_2'],
            'zipCode'     => $order_info['shipping_postcode'],
            'city'        => $order_info['shipping_city'],
        ];

        $data['sender'] = [
            'name1'          => $this->config->get('shipping_dpd_sender_company'),
            'name2'          => $this->config->get('shipping_dpd_sender_name'),
            'street'         => $this->config->get('shipping_dpd_sender_street'),
            'houseNo'        => $this->config->get('shipping_dpd_sender_houseNo'),
            'country'        => $this->config->get('shipping_dpd_sender_country') ?? 'DE',
            'zipCode'        => $this->config->get('shipping_dpd_sender_postcode'),
            'city'           => $this->config->get('shipping_dpd_sender_city'),
            'customerNumber' => '123456789',
        ];

        //check number parcel

         // Отримуємо shipment_id
        $number = 1;
        $query = $this->db->query("SELECT dpd_shipment_id FROM `" . DB_PREFIX . "dpd_shipment` WHERE order_id = '" . (int)$order_id . "'");
        if($query->num_rows){
            $number += $query->num_rows;
        }

        $data['mpsCustomerReferenceNumber1'] = 'CustomerReferenceNumber#'.$order_id. '_'.$number;
        $data['identificationNumber']        = 'identificationNumber#'.$order_id. '_'.$number;
        $data['sendingDepot']                = $this->config->get('shipping_dpd_depot');
        $data['product']                     = $this->config->get('shipping_dpd_product_type');
        $data['mpsWeight']                   = $weight;
        $map = $this->getDpdLanguageMap();
        $oc_code = $this->config->get('config_language');
        $data['messageLanguage'] = $map[$oc_code] ?? 'de_DE';

        $data['parcels'] = [
            'customerReferenceNumber1'        =>  'customerReferenceNumber_'.$order_id. '0001',
             'weight'                         =>  $weight ,
         ];
     
         $data['productAndServiceData'] = [
             'orderType' => 'consignment'
         ];

         $data['splitByParcel'] = 'false';
 
         //here we render tpl template 
         $template = new \Opencart\System\Library\Template\Template();
         $template->addPath(DIR_TEMPLATE); // додає базову директорію
         return $template->render('sale/create_shipping', $data);
        
    }

    public function create() {

        $json = [];

        $this->load->language('sale/order');
        $this->load->model('sale/order');
    
        $order_id = (int)($this->request->get['order_id'] ?? 0);
        $order_info = $this->model_sale_order->getOrder($order_id);
    
        if (!$order_info) {
            throw new \Exception('Order not found');
        }
               
        //get all values $wsdl,  $location, $action ,  $version from array $endpoint
        $endpoint = $this->dpd->getCreateShipmentEndpoint();
        extract($endpoint);
       
        $options = [
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE
        ];

        $client = new \SoapClient($wsdl, $options);
             
        $data['username'] = $this->dpd->getUsername();
        $data['token']  =  $this->dpd->getToken();

        //here we render tpl template 
        $template = new \Opencart\System\Library\Template\Template();
        $template->addPath(DIR_TEMPLATE); // додає базову директорію
        $weight = isset($this->request->post['weight'])? $this->request->post['weight'] : 100;
        //var_dump($weight);

        try {

             $request = $this->getRequestBody($weight); //$template->render('sale/create_shipping', $data);
        //var_dump($request);

            $response = $client->__doRequest($request, $location, $action, $version);
           // echo "<pre>SOAP Response:\n\n" . htmlentities($response) . "</pre>";

            // SimpleXML seems to have problems with the colon ":" in the <xxx:yyy> response tags, so take them out
            $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3',  $response);
            $xml = simplexml_load_string($xml);
            $json1 = json_encode($xml);
            $responseArray = json_decode($json1, true);
            if(!empty($responseArray["soapBody"]["ns2storeOrdersResponse"]) && is_array($responseArray["soapBody"]["ns2storeOrdersResponse"])){
                foreach($responseArray["soapBody"]["ns2storeOrdersResponse"] as $orderResult){
                    //var_dump($orderResult);
                    if(!empty($orderResult["shipmentResponses"]) && is_array($orderResult["shipmentResponses"]) && !array_diff(['mpsId','parcelInformation','identificationNumber'], array_keys($orderResult["shipmentResponses"])) && !empty($orderResult["shipmentResponses"]["parcelInformation"]["parcelLabelNumber"])){
                        $result = $orderResult["shipmentResponses"];  
                        $this->db->query("INSERT INTO ".DB_PREFIX."dpd_shipment SET 
                            order_id = '" . (int)$order_id . "',
                            mpsId ='". $this->db->escape($result["mpsId"])."',
                            parcelLabelNumber	='". $this->db->escape($result["parcelInformation"]["parcelLabelNumber"])."',
                            identificationNumber	='". $this->db->escape($result["identificationNumber"])."',
                            status = 'created',
                            date_created = NOW(),
                            date_updated = NOW()
                        ");
                        $shipment_id = $this->db->getLastId();
                        if(!empty($shipment_id)  && !empty($orderResult["output"]["content"])){
                            $this->db->query("INSERT INTO ".DB_PREFIX."dpd_parcel SET 
                                dpd_shipment_id = '" . (int)$shipment_id . "',
                                parcelLabelNumber	='". $this->db->escape($result["parcelInformation"]["parcelLabelNumber"])."',
                                label  =   '" . $this->db->escape($orderResult["output"]["content"]) . "'
                            ");
                        }
                    }
                }

                $json['success'] = 'Shipment created successfully.';

            }else{
                throw new \Exception('No response or missing parcelInformation');
            }

        } catch (\Exception $e) {
            $this->dpd->log('[DPD createShipment Exception] ' . $e->getMessage());
            $json['error'] = 'DPD Error: ' . $e->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function dpd_area(array $args = []): string {

        $order_id = $args['order_id'] ?? 0;

        if(!(int)$order_id){
            return false;
        }

        $this->load->language('sale/dpd_shipping');
    
        $data['head_title']         = $this->language->get('heading_title');
        $data['text_create_label']  = $this->language->get('text_create_label');
        $data['text_print_label']   = $this->language->get('text_print_label');
        $data['text_existing_labels'] = $this->language->get('text_existing_labels');

        $data['user_token'] = $this->session->data['user_token'];
        $data['order_id'] = $order_id;
    
        $data['print_label_link'] = $this->url->link(
            'sale/dpd_shipping.label',
            'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$order_id,
            true
        );
    
          // Вибрати всі shipment_id для order_id
        $shipment_query = $this->db->query("SELECT dpd_shipment_id FROM `" . DB_PREFIX . "dpd_shipment` WHERE order_id = '" . (int)$order_id . "'");

    
        $data['labels'] = [];
    
        if ($shipment_query->rows) {
            $shipment_ids = array_column($shipment_query->rows, 'dpd_shipment_id');
    
            // Готуємо ID для IN (...)
            $shipment_id_list = implode(',', array_map('intval', $shipment_ids));

            // Отримуємо всі етикетки
            $label_query = $this->db->query("SELECT parcelLabelNumber FROM `" . DB_PREFIX . "dpd_parcel` WHERE dpd_shipment_id IN (" . $shipment_id_list . ")");
    
            foreach ($label_query->rows as $row) {
                $data['labels'][] = $row['parcelLabelNumber'];
            }
        }
    
        return $this->load->view('sale/dpd_area', $data);
    }

    public function label(): void {
        $order_id = $this->request->get['order_id'] ?? 0;

        if (!$order_id) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['error' => 'Missing order_id']));
            return;
        }

        // Вибрати всі shipment_id для order_id
        $shipment_query = $this->db->query("SELECT dpd_shipment_id FROM `" . DB_PREFIX . "dpd_shipment` WHERE order_id = '" . (int)$order_id . "'");

        if (!$shipment_query->num_rows) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['error' => 'No shipment found for this order']));
            return;
        }

        // Зібрати всі shipment_id
        $shipment_ids = array_column($shipment_query->rows, 'dpd_shipment_id');
        $shipment_id_list = implode(',', array_map('intval', $shipment_ids));

        // Отримати всі етикетки
        $label_query = $this->db->query("SELECT label FROM `" . DB_PREFIX . "dpd_parcel` WHERE dpd_shipment_id IN (" . $shipment_id_list . ")");
        if (!$label_query->num_rows) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['error' => 'No labels found for this shipment']));
            return;
        }

        // Підключити PDF-бібліотеку
        require_once DIR_SYSTEM . 'library/dpd_pdf/autoload.php';
        // use вже є на початку файлу: use setasign\Fpdi\Fpdi;

        $pdf = new \setasign\Fpdi\Fpdi();

        foreach ($label_query->rows as $row) {
            $decoded = base64_decode($row['label']);
            $tmpfile = tempnam(sys_get_temp_dir(), 'dpd_pdf_');
            file_put_contents($tmpfile, $decoded);

            try {
                $pageCount = $pdf->setSourceFile($tmpfile);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $tpl = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($tpl);

                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tpl);
                }
            } catch (\Exception $e) {
                // можливо, пошкоджений PDF — пропустити
            }

            unlink($tmpfile);
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="dpd_labels_' . $order_id . '.pdf"');
        $pdf->Output('I');
        exit;
    }

    
           
    
    
    //languages
    private function getDpdLanguageMap(): array {
        $this->load->model('localisation/language');
    
        $languages = $this->model_localisation_language->getLanguages();
    
        $map = [];
    
        foreach ($languages as $lang) {
            // приклад: 'en-gb' → 'en_EN', 'de-de' → 'de_DE'
            $code = strtolower($lang['code']); // opencart format
            [$lang_part, $region_part] = explode('-', $code);
            $dpd_code = strtolower($lang_part) . '_' . strtoupper($region_part);
            $map[$code] = $dpd_code;
        }
    
        return $map;
    }
    
      
    
}
