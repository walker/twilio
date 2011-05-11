<?php
/**
 * IncomingPhoneNumbers Model
 * 
 * Grants access to Twilio incoming phone numbers resource.
 *
 * @package twilio
 * @subpackage twilio.models
 * @author Joey Trapp <joey@loadsys.com>
 * @version 1.0
 * @copyright Loadsys
 **/
class IncomingPhoneNumbers extends TwilioAppModel {
	public $name = 'IncomingPhoneNumbers';
	public $schema = array(
	
	);
	public $crud = array(
		'read' => array(
			'allowed' => true,
			'path' => '/IncomingPhoneNumbers',
			'data' => false,
			'query' => array('PhoneNumber', 'FriendlyName')
		),
		'create' => array(
			'allowed' => true,
			'path' => '/IncomingPhoneNumbers',
			'data' => array('PhoneNumber', 'AreaCode', 'FriendlyName', 'VoiceUrl', 'VoiceMethod', 'VoiceFallbackUrl', 'VoiceFallbackMethod', 'StatusCallback', 'StatusCallbackMethod', 'SmsUrl', 'SmsFallbackMethod', 'VoiceCallerIdLookup'),
			'query' => false
		),
		'update' => array(
			'allowed' => true,
			'path' => '/IncomingPhoneNumbers/%s',
			'data' => array('AccountSid', 'PhoneNumber', 'ApiVersion', 'AreaCode', 'FriendlyName', 'VoiceUrl', 'VoiceMethod', 'VoiceFallbackUrl', 'VoiceFallbackMethod', 'StatusCallback', 'StatusCallbackMethod', 'SmsUrl', 'SmsFallbackMethod', 'VoiceCallerIdLookup'),
			'query' => false
		),
		'delete' => array(
			'allowed' => true,
			'path' => '/IncomingPhoneNumbers/%s',
			'data' => false,
			'query' => false
		)
	);
}
?>