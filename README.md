PHP client for SMSLabs.pl
======
[![Licence MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://travis-ci.org/ittoolspl/php-lib-smslab.svg?branch=master)](https://travis-ci.org/ittoolspl/php-lib-smslab)
[![Build Status](https://scrutinizer-ci.com/g/ittoolspl/php-lib-smslab/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ittoolspl/php-lib-smslab/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ittoolspl/php-lib-smslab/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ittoolspl/php-lib-smslab/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ittoolspl/php-lib-smslab/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ittoolspl/php-lib-smslab/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8d1dcd58-b1d1-4caa-8659-cb0c76402786/mini.png)](https://insight.sensiolabs.com/projects/8d1dcd58-b1d1-4caa-8659-cb0c76402786)
[![Dependency Status](https://www.versioneye.com/user/projects/5813e0c0d33a712754f2a6eb/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/5813e0c0d33a712754f2a6eb)

## Installation

The recommended way to install the library is through [Composer](http://getcomposer.org):

```sh
$ composer require ittoolspl/php-lib-smslabs
```
## Usage

**Constructor:**
```
$sms = new \Ittoolspl\Smslabs\SmslabsClient($appKey, $secret);
 ```

**Send SMS:**
```
$sms->setSenderId($numberOrSenderId)
    ->setExpirationMinutes($minutes) // optionally
    ->setSendDate($dateTime) // optionally
    ->setFlashMessage($isFlashMessage) // optionally
    ->add($number, $message)
    ->send();
```

**Show queued SMS (before send())**
```
$sms->getSmsQueue();
```

**Get recently sent SMS status (after send()):**
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
