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
class Conferences extends TwilioAppModel {
	public $name = 'Conferences';
	public $schema = array(	
	);
	public $crud = array(
		'read' => array(
			'allowed' => true,
			'path' => '/Conferences/%s',
			'data' => false,
			'query' => array('Status', 'FriendlyName', 'DateCreated', 'DateUpdated')
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