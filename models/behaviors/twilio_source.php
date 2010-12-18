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
	protected $methodMap = array(
		'Accounts' => array(
			'read' => array(
				'allowed' => true
				'path' => '/Accounts/%s'
				'data' => false,
				'query' => false
			),
			'create' => array(
				'allowed' => false
			),
			'update' => array(
				'allowed' => true
				'path' => '/Accounts/%s'
				'data' => array('FriendlyName'),
				'query' => false
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'AvailablePhoneNumbers' => array(
			'read' => array(
				'allowed' => true,
				'path' => '/AvailablePhoneNumbers/US/Local',
				'data' => false,
				'query' => array('AreaCode','Contains','InRegion','InPostalCode','NearLatLong','NearNumber','InLata','InRateCenter','Distance')
			),
			'create' => array(
				'allowed' => false
			),
			'update' => array(
				'allowed' => false
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'TollFree' => array(
			'read' => array(
				'allowed' => true,
				'path' => '/AvailablePhoneNumbers/US/TollFree',
				'data' => false,
				'query' => array('Contains')
			),
			'create' => array(
				'allowed' => false
			),
			'update' => array(
				'allowed' => false
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'OutgoingCallerIds' => array(
			'read' => array(
				'allowed' => true,
				'path' => '/OutgoingCallerIds',
				'data' => false,
				'query' => array('PhoneNumber', 'FriendlyName')
			),
			'create' => array(
				'allowed' => true,
				'path' => '/OutgoingCallerIds',
				'data' => array('PhoneNumber','FriendlyName','CallDelay','Extension'),
				'query' => false
			),
			'update' => array(
				'allowed' => true,
				'path' => '/OutgoingCallerIds/%s',
				'data' => array('FriendlyName'),
				'query' => false
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'IncomingPhoneNumbers' => array(
			'read' => array(
				'allowed' => true,
				'path' => '/IncomingPhoneNumbers',
				'data' => false,
				'query' => array('PhoneNumber', 'FriendlyName')
			),
			'create' => array(
				'allowed' => true,
				'path' => '/IncomingPhoneNumbers',
				'data' => array('PhoneNumber', 'AreaCode', 'FriendlyName', 'VoiceUrl', 'VoiceMethod', 'VoiceFallbackUrl', 'VoiceFallbackMethod', 'StatusCallback', 'StatusCallbackMethod', 'SmsUrl', 'SmsFallbackMethod', 'VoiceCallerIdLookup'),
				'query' => false
			),
			'update' => array(
				'allowed' => true,
				'path' => '/IncomingPhoneNumbers/%s',
				'data' => array('PhoneNumber', 'ApiVersion', 'AreaCode', 'FriendlyName', 'VoiceUrl', 'VoiceMethod', 'VoiceFallbackUrl', 'VoiceFallbackMethod', 'StatusCallback', 'StatusCallbackMethod', 'SmsUrl', 'SmsFallbackMethod', 'VoiceCallerIdLookup'),
				'query' => false
			),
			'delete' => array(
				'allowed' => true,
				'path' => '/IncomingPhoneNumbers/%s',
				'data' => false,
				'query' => false
			)
		),
		'Calls' => array(
			'read' => array(
				'allowed' => true,
				'path' => '/Calls/%s',
				'data' => false,
				'query' => array('To', 'From', 'Status', 'StartTime', 'EndTime')
			),
			'create' => array(
				'allowed' => true,
				'path' => '/Calls',
				'data' => array('To', 'From', 'Url', 'Method', 'FallbackUrl', 'FallbackMethod', 'StatusCallback', 'StatusCallbackMethod', 'SendDigits', 'IfMachine', 'Timeout'),
				'query' => false
			),
			'update' => array(
				'allowed' => true,
				'path' => '/Calls/%s',
				'data' => array('Url', 'Method', 'Status'),
				'query' => false
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'Conferences' => array(
			'read' => array(
				'allowed' => true,
				'path' => '/Conferences/%s',
				'data' => false,
				'query' => array('Status', 'FriendlyName', 'DateCreated', 'DateUpdated')
			),
			'create' => array(
				'allowed' => false
			),
			'update' => array(
				'allowed' => false
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'ConferenceParticipants' => array(
			'read' => array(
				'allowed' => true,
				'path' => '/Conferences/%s/Participants/%s',
				'date' => false,
				'query' => array('Muted')
			),
			'create' => array(
				'allowed' => false
			),
			'update' => array(
				'allowed' => true,
				'path' => '/Conferences/%s/Participants/%s',
				'data' => array('Muted'),
				'query' => false
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'SMSMessages' => array(
			'read' => array(
				'allowed' => true,
				'path' => '/SMS/Messages/%s',
				'data' => false,
				'query' => array('To', 'From', 'DateSent')
			),
			'create' => array(
				'allowed' => true,
				'path' => '/SMS/Messages',
				'data' => array('From', 'To', 'Body', 'StatusCallback'),
				'query' => false
			),
			'update' => array(
				'allowed' => false
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'Recordings' => array(
			'read' => array(
				'allowed' => true
			),
			'create' => array(
				'allowed' => false
			),
			'update' => array(
				'allowed' => true
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'Transcriptions' => array(
			'read' => array(
				'allowed' => true
			),
			'create' => array(
				'allowed' => false
			),
			'update' => array(
				'allowed' => true
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'Notifications' => array(
			'read' => array(
				'allowed' => true
			),
			'create' => array(
				'allowed' => false
			),
			'update' => array(
				'allowed' => true
			),
			'delete' => array(
				'allowed' => false
			)
		),
		'Sandbox' => array(
			'read' => array(
				'allowed' => true
			),
			'create' => array(
				'allowed' => false
			),
			'update' => array(
				'allowed' => true
			),
			'delete' => array(
				'allowed' => false
			)
		)
	);
	
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