<?php
/**
 * Conferences Model
 * 
 * Grants access to Twilio conferences resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class ConferenceParticipants extends TwilioAppModel {
	public $name = 'ConferenceParticipants';
	public $twilioSchema = array(
	);
	public $twilioSettings = array(
		'read' => array(
			'allowed' => true,
			'path' => '/Conferences/%s/Participants/%s',
			'date' => false,
			'query' => array('Muted')
		),
		'create' => array(
			'allowed' => false
		),
		'update' => array(
			'allowed' => true,
			'path' => '/Conferences/%s/Participants/%s',
			'data' => array('Muted'),
			'query' => false
		),
		'delete' => array(
			'allowed' => false
		)
	);
}
?>