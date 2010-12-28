<?php
/**
 * Sms Model
 * 
 * Grants access to Twilio sms resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class Sms extends TwilioAppModel {
	public $name = 'Sms';
	public $schema = array(
	
	);
	public $crud = array(
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
	);
}
?>