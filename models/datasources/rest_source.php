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
 *     'basePath' => '/Accounts/<sid>'
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

class RestSource extends DataSource {

	protected $_baseConfig = array(
		'domain' => '',
		'basePath' => '',
		'type' => 'http',
		'auth' => 'basic',
		'username' => '',
		'password' => '',
		'ext' => '',
		'dataType' => 'json'
	);

	protected $_httpSocket;

	public function __construct($config = array()) {
		$this->_httpSocket = new HttpSocket();
		parent::__construct($config);
	}

	public function read(&$model, $queryData = array()) {
		if (!property_exists($model, 'crud')) {
			trigger_error($model->name." does not contain a the property crud", E_USER_WARNING);
		}
	}
	
	public function create(&$model, $fields = array(), $values()) {
		if (!property_exists($model, 'crud')) {
			trigger_error($model->name." does not contain a the property crud", E_USER_WARNING);
		}
	}
	
	public function update(&$model, $fields = array(), $value()) {
		if (!property_exists($model, 'crud')) {
			trigger_error($model->name." does not contain a the property crud", E_USER_WARNING);
		}
	}
	
	public function delete(&$model,  $id = null) {
		if (!property_exists($model, 'crud')) {
			trigger_error($model->name." does not contain a the property crud", E_USER_WARNING);
		}
	}
	
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
		if (property_exists($model, 'schema')) {
			return $model->schema;
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
	 * @access protected
	 * @return mixed
	 */
	protected function _request(&$model) {
		
	}
	
	/**
	 * If config['datatype'] is json then this method will parse the response
	 * and return the json parsed object as an associative array by default.
	 *
	 * @param string $response
	 * @access protected
	 * @return array
	 */
	protected function _parseJson($resonse = null) {
	
	}
	
	/**
	 * If config['datatype'] is xml then this method will parse the response
	 * and return the xml parsed object as an associative array by default.
	 *
	 * @param string $response
	 * @access protected
	 * @return array
	 */
	protected function _parseXml($response = null) {
		
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