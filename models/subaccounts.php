<?php
/**
 * Subaccounts Model
 * 
 * Grants access to Twilio subaccounts resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class Subaccounts extends TwilioAppModel {
	public $name = 'Subaccounts';
	public $schema = array(
	);
	public $crud = array(
		'read' => array(
			'allowed' => true,
			'path' => '/Accounts',
			'data' => false,
			'query' => false
		),
		'create' => array(
			'allowed' => true,
			'path' => '/Accounts',
			'data' => array('FriendlyName'),
			'query' => false
		),
		'update' => array(
			'allowed' => true,
			'path' => '/Accounts/%s',
			'data' => array('FriendlyName', 'Status'),
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