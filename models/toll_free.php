<?php
/**
 * TollFree Model
 * 
 * Grants access to Twilio toll free resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class TollFree extends TwilioAppModel {
	public $name = 'TollFree';
	public $twilioSchema = array(	
	);
	public $twilioSettings = array(
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
	);
}
?>