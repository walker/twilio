<?php
/**
 * Sandbox Model
 * 
 * Grants access to Twilio sandbox resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class Sandbox extends TwilioAppModel {
	public $name = 'Sandbox';
	public $schema = array(
	
	);
	public $crud = array(
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
	);
}
?>