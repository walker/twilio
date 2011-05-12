#Installation

Drop this plugin into the plugins folder of your project. You might consider including it as a submodule.

Add a new configuration to your database.php file:

```php
var $twilio = array(
	'driver' => 'twilio.restSource',
	'scheme' => 'https',
	'auth' => 'Basic',
	'username' => '[Account Sid Here]',
	'password' => '[Account Token Here]',
	'version' => '2010-04-01',
	'ext' => 'json',
	'type' => 'json',
);
```

#Use

Use one of the classes in one of two ways:

```php
ClassRegistry::init('Twilio.IncomingPhoneNumbers')->find('all');
```

```php
App::import('Model', 'Twilio.IncomingPhoneNumbers');
$phone_numbers = new IncomingPhoneNumbers();
$phone_numbers->find('all');
```


To run the a command as a different account, specify the Account Sid and Token in the data or conditions.

This one would find all incoming phone numbers for the specified account:

```php
$incomingPhoneNumbersModel->find('all', array('conditions' => array('run_as_account' => '[Account Sid]', 'run_as_account_token' => '[Account token]'));
```

This one would create a call for the specified account:

```php
$callsModel->save(array('to'=>'[Phone Number]', 'from'=>'Twilio Phone number', 'url'=>'The next URL the TwiML should be pulled from', 'status_callback'=>'The url that the final data report should be sent to (twilio sends this after the call hangs up)', 'run_as_account' => '[Account Sid]', 'run_as_account_token' => '[Account token]'));
```