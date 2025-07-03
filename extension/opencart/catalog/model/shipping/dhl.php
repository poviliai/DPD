<?php
namespace Opencart\Catalog\Model\Extension\Opencart\Shipping;
/**
 * Class dhl
 *
 * Can be called from $this->load->model('extension/opencart/shipping/dhl');
 *
 * @package Opencart\Catalog\Model\Extension\Opencart\Shipping
 */
class dhl extends \Opencart\System\Engine\Model {
	/**
	 * Get Quote
	 *
	 * @param array<string, mixed> $address array of data
	 *
	 * @return array<string, mixed>
	 */
	public function getQuote(array $address): array {
		$this->load->language('extension/opencart/shipping/dhl');
		$method_data = [];
       /*
		$this->load->model('localisation/geo_zone');

		$results = $this->model_localisation_geo_zone->getGeoZone((int)$this->config->get('shipping_dhl_geo_zone_id'), (int)$address['country_id'], (int)$address['zone_id']);

		if (!$this->config->get('shipping_dhl_geo_zone_id')) {
			$status = true;
		} elseif ($results) {
			$status = true;
		} else {
			$status = false;
		}

		
        
		if ($status) {
			$quote_data = [];

			$quote_data['dhl'] = [
				'code'         => 'dhl.dhl',
				'name'         => $this->language->get('text_description'),
				'cost'         => $this->config->get('shipping_dhl_cost'),
				'tax_class_id' => $this->config->get('shipping_dhl_tax_class_id'),
				'text'         => $this->currency->format($this->tax->calculate($this->config->get('shipping_dhl_cost'), $this->config->get('shipping_dhl_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
			];

			$method_data = [
				'code'       => 'dhl',
				'name'       => $this->language->get('heading_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_dhl_sort_order'),
				'error'      => false
			];
		}
        */
		return $method_data;
	}
}
