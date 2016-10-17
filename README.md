Client to communicate with SMSLabs.pl
======

Examples:
--
**Constructor:**
 - $sms = new \Ittools\Smslabs\Smslabs($appKey, $secret);

**Send SMS:**
 - $sms->setSenderId($numberOrSenderId);
 - $sms->setExpiration($minutes); // optionally
 - $sms->setSendDate($dateTime); // optionally
 - $sms->setIsFlashMessage($isFlashMessage); // optionally
 - $sms->add($number, $message);
 - $sms->send();

**Get available SenderId:**
 - $sms->getAvailableSenders();

**Get send SMS status:**
 - $sms->getSentStatus();

**Account Balance:**
 - $sms->getAccountBalance();

**Check incoming SMS:**
 - $sms->getSmsIn();

**Check sent SMS:**
 - $sms->getSmsOut($offset, $limit);

**Check details of SMS:**
 - $sms->getSmsDetails();
