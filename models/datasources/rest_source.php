<?php
/**
 * Twilio Rest Datasource
 *
 * Just a basic rest datasource specifically for the twilio plugin. Could
 * be modified in time to not be specific at all to the twilio plugin.
 *
 * This datasource will look for a public property in models called $schema
 * to return in the RestSource::describe().
 *
 * This datasource is designed to be manipulated by public Model properties.
 * Each model that uses this datasource should have a property $crud that is an
 * is that contains 4 keys (create, read, update and delete), and the values for
 * those keys should an array of the following options:
 * 
 * allowed - boolean - whether or not the action is crud action is allowed
 * path - string - the default path that should be applied to the domain to reach the api resource
 * data - mixed - array of valid data keys that can be sent with create and update calls or false
 * query - mixed - array of valid query string options that can be sent added onto url or false
 *
 * If the data key is an array and the action is a create or update, then the valid $model->data 
 * fields that are in the $crud data array will be sent in the POST request to the api.
 *
 * If the query key is an array and the action is read, then the valid conditions in the find conditions
 * will be added to the url as a query string.
 *
 * Here is an example of the database.php property:
 *
 * $twilio = array(
 *     'domain' => 'api.twilio.com',
 *     'type' => 'https',
 *     'username' => 'username',
 *     'password' => 'password',
 *     'dataType' => '(json|xml)',
 *     'basePath' => '/2010-04-01/Accounts/<sid>'
 * )
 *
 * There is also an option for ext which can be json or blank for twilio. If datatype
 * is json then the json ext will be added automatically.
 *
 * @package twilio
 * @subpackage twilio.models.datasource
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 */
 
App::import('Core', 'HttpSocket');
App::import('Core', 'Xml');

class RestSource extends DataSource {

	/**
	 * Default options for datasource
	 *
	 * @var array
	 * @access public
	 */
	public $_baseConfig = array(
		'domain' => 'api.twilio.com',
		'scheme' => 'http',
		'auth' => 'Basic',
		'username' => '',
		'password' => '',
		'version' => '',
		'ext' => '',
		'type' => 'json'
	);

	/**
	 * Member property that is an object of HttpSocket. Set in __construct
	 *
	 * @var object
	 * @access protected
	 */
	protected $_httpSocket;

	/**
	 * Default constructor
	 *
	 * @param array $config
	 * @access public
	 * @return void
	 */
	public function __construct($config = array()) {
		$this->_httpSocket = new HttpSocket();
		parent::__construct($config);
	}

	/**
	 * Read method used for model find queries
	 *
	 * @param object $model
	 * @param array $queryData
	 * @access public
	 * @return mixed array or false
	 */
	public function create(&$model, $fields = array(), $values = array()) {
		$options = $this->_checkCrud($model, 'create');
		if (!$options) {
			return false;
		}
		foreach ($fields as &$field) {
			$field = Inflector::classify($field);
		}
		$query = false;
		$data = $this->_parseData($model, array_combine($fields, $values), $options['data']);
		$response = $this->_request($model, $options['path'], $query, $data, 'POST');
		if (is_array($response) && !empty($response)) {
			$model->data = array($model->alias => $response);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Read method used for model find queries
	 *
	 * @param object $model
	 * @param array $queryData
	 * @access public
	 * @return mixed array or false
	 */
	public function read(&$model, $queryData = array()) {
		$options = $this->_checkCrud($model, 'read');
		$data = array();
		
		if (isset($queryData['conditions']['sid']) && empty($queryData['conditions']['sid'])) {
			$options['path'] = sprintf($options['path'], $queryData['conditions']['sid']);
		} else {
			$options['path'] = str_replace('/%s', '', $options['path']);
		}
		
		if (!$options) {
			return false;
		}
		$query = false;
		if (!empty($options['query']) && !empty($queryData['conditions'])) {
			$query = $this->_parseQuery($model, $queryData['conditions'], $options['query']);
		}
		return array($model->alias => $this->_request($model, $options['path'], $query, $data, 'GET'));
	}

	/**
	 * Read method used for model find queries
	 *
	 * @param object $model
	 * @param array $queryData
	 * @access public
	 * @return mixed array or false
	 */
	public function update(&$model, $fields = array(), $values = array()) {
		$options = $this->_checkCrud($model, 'update');
		if (!$options) {
			return false;
		}
		foreach ($fields as &$field) {
			$field = Inflector::classify($field);
		}
		$query = false;
		$data = $this->_parseData($model, array_combine($fields, $values), $options['data']);
		$response = $this->_request($model, $options['path'], $query, $data, 'POST');
		if (is_array($response) && !empty($response)) {
			return array($model->alias => $response);
		} else {
			return false;
		}
	}
	
	/**
	 * Read method used for model find queries
	 *
	 * @param object $model
	 * @param array $queryData
	 * @access public
	 * @return mixed array or false
	 */
	public function delete(&$model,  $id = null) {
		$options = $this->_checkCrud($model, 'delete');
		if (!$options) {
			return false;
		}
	}
	
	/**
	 * Lists the resources available
	 *
	 * @access public
	 * @return array
	 */
	public function listSources() {
	
	}
	
	/**
	 * Checks the model for a public property $schema and returns that. If
	 * that property does not exist, an empty array is returned.
	 *
	 * @param object $model
	 * @access public
	 * @return array
	 */
	public function describe(&$model) {
		if (property_exists($model, '_schema')) {
			return $model->_schema;
		} else {
			return array();
		}
	}
	
	/**
	 * Performs a web request using the HttpSocket class and then calls
	 * the appropriate method for parsing the response. Returns the parsed
	 * response or boolean false if the request fails.
	 *
	 * @param object $model
	 * @param string $path
	 * @param array $query
	 * @param array $data
	 * @param string $method
	 * @access protected
	 * @return mixed
	 */
	protected function _request(&$model, $path = '', $query = array(), $data = array(), $method = 'GET') {
		if(isset($data['RunAsAccount']) && isset($data['RunAsAccountToken'])) {
			$this->config['username'] = $data['RunAsAccount'];
			$this->config['password'] = $data['RunAsAccountToken'];
			unset($data['RunAsAccount']);
			unset($data['RunAsAccountToken']);
		} else if(isset($query['RunAsAccount']) && isset($query['RunAsAccountToken'])) {
			$this->config['username'] = $query['RunAsAccount'];
			$this->config['password'] = $query['RunAsAccountToken'];
			unset($query['RunAsAccount']);
			unset($query['RunAsAccountToken']);
		}
		
		if(isset($data['Ext']) && isset($data['Type'])) {
			$this->config['ext'] = $data['Ext'];
			$this->config['type'] = $data['Type'];
			unset($data['Type']);
		} else if(isset($query['Ext']) && isset($query['Type'])) {
			$this->config['ext'] =  $query['Ext'];
			$this->config['type'] =  $query['Type'];
			unset($query['Ext']);
			unset($query['Type']);
		}
		
		$request = array(
			'method' => $method,
			'uri' => array(
				'scheme' => $this->config['scheme'],
				'host' => $this->config['domain'],
				'path' => $path,
				'query' => $query
			)
		);
		if (strtoupper($method) === 'POST' && !empty($data)) {
			$request['body'] = $data;
		}
		$request['uri']['path'] = $this->_buildPath($model, $path);	
		if (!empty($this->config['auth'])) {
			$request['auth'] = array(
				'method' => $this->config['auth'],
				'user' => $this->config['username'],
				'pass' => $this->config['password']
			);
			$request['uri']['user'] = $this->config['username'];
			$request['uri']['pass'] = $this->config['password'];
		}
		$response = $this->_httpSocket->request($request);
		switch ($this->config['type']) {
			case 'json':
				return $this->_parseJson($response);
			break;
			case 'xml':
				return $this->_parseXml($response);
			break;
			case 'csv':
			default:
				return $response;
		}
	}
	
	/**
	 * Method that takes the model and desired path that was sent into _request
	 * and returns the correct path to the twilio resource based on the settings
	 * in model->pathOptions
	 *
	 * @param object $model
	 * @param string $path
	 * @access protected
	 * @return string
	 */
	protected function _buildPath(&$model, $path) {
		$options = array();
		if (property_exists($model, 'pathOptions')) {
			$options = $model->pathOptions;
		}
		if (empty($options)) {
			$_path = '/'.$this->config['version'].'/Accounts/'.$this->config['username'].'/'.$path;
		} else {
			if ($options['user'] && $options['user'] && !$options['base']) {
				if (strstr($path, '%s')) {
					if (isset($model->id) && !empty($model->id)) {
						$_path = '/'.$this->config['version'].'/'.sprintf($path, $model->id);
					} else {
						$_path = '/'.$this->config['version'].'/'.sprintf($path, $this->config['username']);
					}
				} else {
					$_path = '/'.$this->config['version'].'/'.$path;
				}
			} else {
				if (strstr($path, '%s')) {
					$_path = '/'.$this->config['version'].'/Accounts/'.$this->config['username'].'/'.sprintf($path, $model->id);
				} else {
					$_path = '/'.$this->config['version'].'/Accounts/'.$this->config['username'].'/'.$path;
				}
			}
		}
		if (!empty($this->config['ext'])) {
			$_path .= '.'.str_replace('.', '', $this->config['ext']);
		}
		$_path = str_replace('//', '/', $_path);
		return $_path;
	}
	
	/**
	 * If config['type'] is json then this method will parse the response
	 * and return the json parsed object as an associative array by default.
	 *
	 * @param string $response
	 * @access protected
	 * @return array
	 */
	protected function _parseJson($response = null) {
		return json_decode($response, true);
	}
	
	/**
	 * If config['type'] is xml then this method will parse the response
	 * and return the xml parsed object as an associative array by default.
	 *
	 * @param string $response
	 * @access protected
	 * @return array
	 */
	protected function _parseXml($response = null) {
		$xml = new Xml($response);
		return $xml->toArray();
	}
	
	/**
	 * Takes the conditions given to Model::find and the valid query string options
	 * defined in the rest model crud property and returns an array for the HttpSocket::request
	 * query option.
	 *
	 * @param object $model
	 * @param array $conditions
	 * @param array $valid
	 * @access protected
	 * @return mixed array or false
	 */
	protected function _parseQuery(&$model, $conditions = array(), $valid = array()) {
		if (empty($conditions) || empty($valid)) {
			return false;
		}
		$ret = array();
		foreach ($conditions as $key => $value) {
			if(Inflector::classify($key)=='RunAsAccount' || Inflector::classify($key)=='RunAsAccountToken') {
				$ret[Inflector::classify($key)] = $value;
			} else if (in_array(Inflector::classify($key), $valid)) {
				$ret[Inflector::classify($key)] = $value;
			}
		}
		return $ret;
	}
	
	/**
	 * Takes the conditions given to Model::save and the valid post data options
	 * defined in the rest model crud property and returns an array for the HttpSocket::request
	 * data option.
	 *
	 * @param object $model
	 * @param array $data
	 * @param array $valid
	 * @access protected
	 * @return mixed array or false
	 */
	protected function _parseData(&$model, $data = array(), $valid = array()) {
		if (empty($data) || empty($valid)) {
			return false;
		}
		$ret = array();
		if (isset($data[$model->alias])) {
			$data = $data[$model->alias];
		}
		foreach ($data as $key => $value) {
			$valid[] = 'RunAsAccount';
			$valid[] = 'RunAsAccountToken';
			if (in_array($key, $valid)) {
				$ret[$key] = $value;
			}
		}
		return $ret;
	}
	
	/**
	 * Checks that the model has a property called crud and that the method being accessed
	 * is allowed. Returns false if crud doesn't exist or the method is not allowed. Returns
	 * the value stored in $model->crud[$type] if it exists
	 *
	 * @param object $model
	 * @param string $type
	 * @access protected
	 * @return mixed false or array
	 */
	protected function _checkCrud(&$model, $type = 'read') {
		if (!property_exists($model, 'crud')) {
			trigger_error($model->name." does not contain a the property crud", E_USER_WARNING);
			return false;
		}
		if (!$model->crud[$type]['allowed']) {
			trigger_error($model->name." does not allow ".$type." requests", E_USER_WARNING);
			return false;
		}
		return $model->crud[$type];
	}
	
	/**
	 * Method Not Allowed
	 */
	public function begin(&$model) {
		trigger_error("Transactions are not supported", E_USER_WARNING);
		return false;
	}
	/**
	 * Method Not Allowed
	 */
	public function commit(&$model) {
		trigger_error("Transactions are not supported", E_USER_WARNING);
		return false;
	}
	/**
	 * Method Not Allowed
	 */
	public function rollback(&$model) {
		trigger_error("Transactions are not supported", E_USER_WARNING);
		return false;
	}
}

?>