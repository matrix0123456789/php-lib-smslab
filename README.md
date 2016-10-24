PHP client for SMSLabs.pl
======
[![Licence MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://scrutinizer-ci.com/g/jpyzio/php-lib-smslab/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jpyzio/php-lib-smslab/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jpyzio/php-lib-smslab/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jpyzio/php-lib-smslab/?branch=master)
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

**Show queued SMS befor send***
```
$sms->getSmsQueue();
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
