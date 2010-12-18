<?php
/**
 * Calls Model
 * 
 * Grants access to Twilio Calls resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class Calls extends TwilioAppModel {
	public $name = 'Calls';
	public $twilioSchema = array(
	
	);
	public $twilioSettings = array(
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
	);
}
?>