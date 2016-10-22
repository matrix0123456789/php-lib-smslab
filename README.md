PHP client for SMSLabs.pl
======

[![Build Status](https://travis-ci.org/jpyzio/php-lib-smslab.svg?branch=master)](https://travis-ci.org/jpyzio/php-lib-smslab)

Examples:
--
**Constructor:**
```
$sms = new \Ittools\Smslabs\SmslabsClient($appKey, $secret);
 ```

**Send SMS:**
```
$sms->setSenderId($numberOrSenderId)
    ->setExpiration($minutes) // optionally
    ->setSendDate($dateTime) // optionally
    ->setIsFlashMessage($isFlashMessage) // optionally
    ->add($number, $message)
    ->send();
```

**Get recently sent SMS status (use after send() method):**
```
$sms->getSentStatus();
```

**Get available SenderId:**
```
$sms->getAvailableSenders();
```

**Account Balance:**
```
$sms->getAccountBalance();
```

**Check incoming SMS:**
```
$sms->getSmsIn();
```

**Check sent SMS:**
```
$sms->getSmsOut($offset, $limit);
```

**Check details of SMS:**
```
$sms->getSmsDetails();
```
