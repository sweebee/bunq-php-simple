# bunq-php-simple
Simple wrapper for the bunq php sdk, for now just some basic functions.

### Installation
Before you can use it, you need to setup the configuration. Run the following command in your terminal:

```
vendor/bin/bunq-install
```

This will create the bunq.conf file.

### Examples

##### Get your accounts
```php
$bunq = new \Wiebenieuwenhuis\bunqApi('bunq.conf');

$accounts = $bunq->accounts->all();
var_dump($accounts);
```

##### Create a payment
```php

// Can be the ID of the account or an account object
$from_account = 12345; // By ID

// $from_account = $accounts[0]; // By account, see previous example

$recipient = [
    'type'  => 'IBAN',
    'name'  => 'Your recipients name',
    'value' => 'NL00BUNQ12345678901'
];

$amount = 5.25;

$bunq = new \Wiebenieuwenhuis\bunqApi('bunq.conf');
$result = $bunq->payments->create( $from_account, [
    'recipient' => $recipient,
    'amount'    => $amount,
]);
				
var_dump($result);

```

##### Create a webhook
```php
$bunq = new \Wiebenieuwenhuis\bunqApi('bunq.conf');

// Create a webhook callback
$bunq->callbacks->create('https://yourdomain.com/callback');

// Delete the callback
$bunq->callbacks->delete('https://yourdomain.com/callback');
```
