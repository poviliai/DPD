<?php
/**
 * DPD API Client for OpenCart 4
 *
 * Developed for TELENORMA Holding AG
 * Website: https://telenorma.ag/
 * Created by: Vika Poviliai <v.poviliai@gmail.com>
 * Copyright © TELENORMA Holding AG
 */

 class DpdApi {
     private $username;
     private $password;
     private $sandbox = true;
 
     private $base;
     private $wsdl_login;
     private $wsdl_shipment; 
     private $location_shipment;
     private $auth_token_file;
     private $client;

     protected $config;
 
     public function __construct( $config = []) {
        
        $this->config  = new \Opencart\System\Engine\Config();
        
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


         $this->username = $config['username'];
         $this->password = $config['password'];
         $this->sandbox = $config['sandbox'] ?? true;
 
         $this->base = $this->sandbox ? 'https://public-ws-stage.dpd.com' : 'https://public-ws.dpd.com';
         $this->wsdl_login = $this->base . '/services/LoginService/V2_0/?wsdl';
         $this->wsdl_shipment = $this->base . '/services/ShipmentService/V4_4?wsdl';
         $this->location_shipment = $this->base . '/services/ShipmentService/V4_4';
 
         $this->auth_token_file = DIR_STORAGE . 'cache/dpd_auth_token.json';
     }
 
     public function getToken() {
 
         if ($this->isTokenCached()) {
             $data = json_decode(file_get_contents($this->auth_token_file), true);
             return $data['token'];
         }
 
         return $this->login();
     }
 
     private function isTokenCached() {
         if (!file_exists($this->auth_token_file)) return false;
 
         $data = json_decode(file_get_contents($this->auth_token_file), true);
         $expires = strtotime($data['expires'] ?? 'now -1 hour');
 
         return time() < $expires;
     }
 
     public function login() {
         
         try {
             $this->client = new SoapClient($this->wsdl_login, ['trace' => true]);
 
             $params = [
                 'delisId' => $this->username,
                 'password' => $this->password,
                 'messageLanguage' => 'en_EN'
             ];
 
             $response = $this->client->getAuth($params);
             if (isset($response->return->authToken)) {
                 $data = [
                     'token' => $response->return->authToken,
                     'expires' => date('Y-m-d H:i:s', strtotime('+23 hours'))
                 ];
                 file_put_contents($this->auth_token_file, json_encode($data));
                 return $response->return->authToken;
             } else {
                $this->log('[DPD Auth Error] No token in response.');
                $this->log('[DPD SOAP REQUEST] ' . $this->client->__getLastRequest());
                $this->log('[DPD SOAP RESPONSE] ' . $this->client->__getLastResponse());
             }
 
         } catch (Exception $e) {
            $this->log('[DPD Login Exception] ' . $e->getMessage());
             if ($this->client) {
                $this->log('[DPD SOAP REQUEST] ' . $this->client->__getLastRequest());
                $this->log('[DPD SOAP RESPONSE] ' . $this->client->__getLastResponse());
             }
         }
 
         return null;
     }
 
     public function getSoapClient() {
         return $this->client ?? null;
     }

     public function getUsername() {
		return $this->username;
	}

    public function log(string $message): void {
        $log_file = DIR_STORAGE . 'logs/dpd.log';
        if (@file_put_contents($log_file, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND) === false) {
            error_log('[DPD API] Failed to write to log: ' . $log_file);
        }        
    }


    public function getCreateShipmentEndpoint() {        
        return [
                'wsdl'            =>   $this->wsdl_shipment,
                'location'        =>   $this->location_shipment,
                'action'          =>  'urn:storeOrders',
                'version'         =>  1, // SOAP 1.1
        ];
    }
        
    
 }
 