<?php
/**
 * OutgoingCallerIds Model
 * 
 * Grants access to Twilio outgoing caller ids resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class OutgoingCallerIds extends TwilioAppModel {
	public $name = 'OutgoingCallerIds';
	public $twilioSchema = array(
	
	);
	public $twilioSettings = array(
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
	);
}
?>