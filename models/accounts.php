<?php
/**
 * Accounts Model
 * 
 * Grants access to Twilio accounts resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class Accounts extends TwilioAppModel {
	public $name = 'Accounts';
	public $schema = array(
	);
	public $crud = array(
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
	);
	public $pathOptions = array(
		'base' => false,
		'user' => true,
		'version' => true
	);
}
?>