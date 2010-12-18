<?php
/**
 * AvailablePhoneNumbers Model
 * 
 * Grants access to AvailablePhoneNumbers resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class AvailablePhoneNumbers extends TwilioAppModel {
	public $name = 'AvailablePhoneNumbers';
	public $twilioSchema = array(
	);
	public $twilioSettings = array(
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
	);
}
?>