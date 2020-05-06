<?php
	/*
	 * GeoIP Class
	 *
	 * This PHP class is free software: you can redistribute it and/or modify
	   the code under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version. 
	 *
	 * However, the license header, copyright and author credits 
	   must not be modified in any form and always be displayed.
	 *
	 * This class is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.
	 *
	 * @author geoPlugin (gp_support@geoplugin.com)
	 * @copyright Copyright geoPlugin (gp_support@geoplugin.com)
	 * $version 2.0
	 *
	 * --> Brought into the 21st Century by Xunnamius of Dark Gray. <--
	 *
	 * This PHP class uses the PHP Webservice of http://www.geoplugin.com/ to geolocate IP addresses
	 * Geographical location of the IP address (visitor) and locate currency (symbol, code and exchange rate) are returned.
	 *
	 ** Requires cURL or allow_url_fopen
	 *** See http://www.geoplugin.com/webservices/php for more specific details of this free service
	 */
	
	class GeoIP
	{
		protected $host = 'http://www.geoplugin.net/php.gp?ip={IP}';
		protected $data = array();
		
		public function __construct($ip=NULL)
		{ if(is_string($ip)) return $this->locate($ip); }
		
		public function __get($property)
		{ return array_key_exists($property, $this->data) ? $this->data[$property] : NULL; }
		
		/* Used to manage exceptions */
		protected function except($msg, $lvl, $deep)
		{
			if(class_exists('DEH', FALSE)) DEH::except($msg, $lvl, $deep);
			else
			{
				$pre = '<span style="color: red; font-weight: bold;">';
				$msg = $msg.'</span>';
				if($lvl == E_USER_ERROR) die($pre.'Error: '.$msg);
				else echo $pre.'Warning: '.$msg;
			}
		}
		
		/* Getches data from the server */
		protected function fetch($host)
		{
			$response = NULL;
			
			// Use cURL to fetch data
			if(function_exists('curl_init'))
			{		
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $host);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.0');
				$response = curl_exec($ch);
				curl_close ($ch);
			}
			
			// Otherwise fall back to fopen()
			else if(ini_get('allow_url_fopen'))
				$response = file_get_contents($host, 'r');
			else $this->except('GeoIP Error -> Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini!', E_USER_ERROR, 2);
			return $response;
		}
		
		/* Geolocates an IP */
		public function locate($ip=NULL)
		{
			$ip = is_string($ip) ? $ip : $_SERVER['REMOTE_ADDR'];
			$host = str_replace('{CURRENCY}', $this->currency, str_replace('{IP}', $ip, $this->host));		
			
			if($data = @unserialize($this->fetch($host)))
			{
				$this->data['ip'] = $ip;
				$this->data['city'] = $data['geoplugin_city'];
				$this->data['region'] = $data['geoplugin_region'];
				$this->data['areaCode'] = $data['geoplugin_areaCode'];
				$this->data['dmaCode'] = $data['geoplugin_dmaCode'];
				$this->data['countryCode'] = $data['geoplugin_countryCode'];
				$this->data['countryName'] = $data['geoplugin_countryName'];
				$this->data['continentCode'] = $data['geoplugin_continentCode'];
				$this->data['latitude'] = $data['geoplugin_latitude'];
				$this->data['longitude'] = $data['geoplugin_longitude'];
				$this->data['currencyCode'] = $data['geoplugin_currencyCode'];
				$this->data['currencySymbol'] = $data['geoplugin_currencySymbol'];
				return TRUE;
			}
			
			return FALSE;
		}
		
		/* Grabs nearby locations */
		public function nearby($radius=10, $limit=NULL)
		{
			$return = NULL;
			if(is_numeric($this->data['latitude']) && is_numeric($this->data['longitude']))
			{
				$host = "http://www.geoplugin.net/extras/nearby.gp?lat={$this->data['latitude']}&long={$this->data['longitude']}&radius={$radius}";
				if(is_numeric($limit)) $host .= "&limit={$limit}";
				$return = unserialize($this->fetch($host));
			}
			
			else $this->except('GeoIP Warning -> Incorrect latitude or longitude values.', E_USER_WARNING, 2);
			return $return;
		}
	}

	/* Class-specific Constants */
	
	// Protect page from direct access
	if(count(get_included_files()) <= 1) die('<h1 style="color: red; font-weight: bold;">No.</h1>');
?>