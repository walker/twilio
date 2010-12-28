<?php

App::import('Core', 'HttpSocket');

class TwilioSource extends DataSource {
	protected $_schema = array();
	protected $_requestUrl = null;
	protected $_HttpSocket = null;
	protected $_httpScheme = 'https';
	protected $_username = null;
	protected $_password = null;
	protected $_domain = 'api.twilio.com';
	protected $_apiVersion = '2010-04-01';
	protected $_sid = null;
	
	public __construct($config) {
		parent::__construct($config);
		$this->_username = $config['login'];
		$this->_password = $config['password'];
		$this->_domain = $config['api'];
		$this->_apiVersion = $config['version'];
		$this->_sid = $config['sid'];
		$this->_HttpSocket = new HttpSocket();
	}
	
	public function listSources() {
		return array_keys($this->mapMethods);	
	}
	
	public function read(&$model, $queryData = array()) {
		$type = "GET";
		$url = $this->_requestUrl;
		$apiMethod = str_replace('Twilio', '', $model->alias);
		if ($this->methodMap[$apiMethod]['read']['allowed']) {
			$path = $query = null
			if (isset($queryData['path'])) {
				$path = $queryData['path'];
			}
			if (isset($queryData['query'])) {
				$query = $queryData['query'];
			}
			$response = $this->apiRequest($apiMethod, $path, $query, $url);
		} else {
			return false;
		}
	}
	
	public function create(&$model, $fields = array(), $values = array()) {
		$type = "POST";
	}

	public function update(&$model, $fields = array(), $values = array()) {
		$type = "POST";
	}
	
	public function delete(&$model, $id = null) {
		$type = "DELETE";
	}

	protected apiRequest($apiMethod = null, $path = null, $query = null, $type = "POST") {
		if (!$apiMethod) {
			return false;
		}
		$startPath = '/Accounts/'.$this->_sid;
		$ext = '.json';
		if (!$path) {
			$path = $startPath;
		}	
		$request = array(
			'method' => $type,
			'uri' => array(
				'scheme' => $this->_httpScheme,
				'host' => $this->_domain,
				'path' => $path,
				'query' => $query
			),
			'auth' => array(
				'method' => 'Basic',
				'user' => $this->_username,
				'pass' => $this->_password
			)
		)
	}
}

?>