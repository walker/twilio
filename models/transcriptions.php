<?php
/**
 * Transcriptions Model
 * 
 * Grants access to Twilio transcriptions resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class Transcriptions extends TwilioAppModel {
	public $name = 'Transcriptions';
	public $twilioSchema = array(
	
	);
	public $twilioSettings = array(
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