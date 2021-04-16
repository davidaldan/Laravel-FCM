# Laravel-FCM


## Introduction

Laravel-FCM is an easy to use package working with both Laravel and Lumen for sending push notification with [Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging/) (FCM).

It currently **only supports HTTP protocol** for :

- sending a downstream message to one or multiple devices
- managing groups and sending message to a group
- sending topics messages

## Installation

Composer json file 

	$ "davidaldan/laravel-fcm": "0.0.*"

### Laravel

Register the provider directly in your app configuration file config/app.php `config/app.php`:

```php
'providers' => [
	// ...

	LaravelFCM\FCMServiceProvider::class,
]
```

Add the facade aliases in the same file:

```php
'aliases' => [
	...
	'FCM'      => LaravelFCM\Facades\FCM::class,
	'FCMGroup' => LaravelFCM\Facades\FCMGroup::class, // Optional
]
```

Publish the package config file using the following command:


	$ php artisan vendor:publish --provider="LaravelFCM\FCMServiceProvider"


### Package Configuration

In your `.env` file, add the server key and the secret key for the Firebase Cloud Messaging:

```php
FCM_SERVER_KEY=my_secret_server_key
FCM_SENDER_ID=my_secret_sender_id
```

### Downstream Messages

A downstream message is a notification message, a data message, or both, that you send to a target device or to multiple target devices using its registration_Ids.

The following use statements are required for the examples below:

```php
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\PayloadApnsBuilder;
use FCM;
```

#### Sending a Downstream Message to a Device

```php
$optionBuilder = new OptionsBuilder();
$optionBuilder->setTimeToLive(60*20);

$notificationBuilder = new PayloadNotificationBuilder('my title');
$notificationBuilder->setBody('Hello world')
				    ->setSound('default');

$dataBuilder = new PayloadDataBuilder();
$dataBuilder->addData([
	'a_data' 					=> 'my_data', //extra data can be array and with json_encode()
	'notification_foreground'	=> "true" //true-false
]);

$option = $optionBuilder->build();
$notification = $notificationBuilder->build();
$data = $dataBuilder->build();

$token = "a_registration_from_your_database";

$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

$downstreamResponse->numberSuccess();
$downstreamResponse->numberFailure();
$downstreamResponse->numberModification();

// return Array - you must remove all this tokens in your database
$downstreamResponse->tokensToDelete();

// return Array (key : oldToken, value : new token - you must change the token in your database)
$downstreamResponse->tokensToModify();

// return Array - you should try to resend the message to the tokens in the array
$downstreamResponse->tokensToRetry();

// return Array (key:token, value:error) - in production you should remove from your database the tokens
$downstreamResponse->tokensWithError();
```

#### Sending a Downstream Message to Multiple Devices

```php
$optionBuilder = new OptionsBuilder();
$optionBuilder->setTimeToLive(60*20);

$notificationBuilder = new PayloadNotificationBuilder('my title');
$notificationBuilder->setBody('Hello world')
				    ->setSound('default');

$dataBuilder = new PayloadDataBuilder();
$dataBuilder->addData(['a_data' => 'my_data']);

$option = $optionBuilder->build();
$notification = $notificationBuilder->build();
$data = $dataBuilder->build();

// You must change it to get your tokens
$tokens = MYDATABASE::pluck('fcm_token')->toArray();

$downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

$downstreamResponse->numberSuccess();
$downstreamResponse->numberFailure();
$downstreamResponse->numberModification();

// return Array - you must remove all this tokens in your database
$downstreamResponse->tokensToDelete();

// return Array (key : oldToken, value : new token - you must change the token in your database)
$downstreamResponse->tokensToModify();

// return Array - you should try to resend the message to the tokens in the array
$downstreamResponse->tokensToRetry();

// return Array (key:token, value:error) - in production you should remove from your database the tokens present in this array
$downstreamResponse->tokensWithError();
```


## Notification Sound


Android:

```
->setChannelId('services_channel_id')
```

iOS:
```
$apnsBuilder = new PayloadApnsBuilder();
$apnsBuilder->addData([
	'payload' => [
		'aps' => [
			'sound'=>'default'
		]
	]
]);
```



## Licence

This library is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

Some of this documentation is coming from the official documentation. You can find it completely on the [Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging/) Website.
