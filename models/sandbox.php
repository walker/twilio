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
	public $_schema = array(
		'voice_url' => array(
			'type' => 'string',
			'null' => false
		),
		'voice_method' => array(
			'type' => 'string'
		),
		'sms_url' => array(
			'type' => 'string'
		),
		'sms_method' => array(
			'type' => 'string'
		)
	);
	public $crud = array(
		'read' => array(
			'allowed' => true,
			'path' => '/Sandbox',
			'query' => false,
			'data' => false
		),
		'create' => array(
			'allowed' => false
		),
		'update' => array(
			'allowed' => true,
			'path' => '/Sandbox',
			'query' => false,
			'data' => array('VoiceUrl', 'VoiceMethod', 'SmsUrl', 'SmsMethod')
		),
		'delete' => array(
			'allowed' => false
		)
	);
	
	/**
	 * Set the model->id to a dummy value to trick the Model into calling update
	 * rather than create. The Sandbox resource has no id and there is only one
	 * resource for an account, so all saves should be updates and never creates.
	 *
	 * @access public
	 * @return bool
	 */
	public function beforeSave() {
		$this->id = 1234;
		return true;
	}
}
?>