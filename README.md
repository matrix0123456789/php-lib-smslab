Client to communicate with SMSLabs.pl
======

Examples:
--
**Constructor:**
 - $sms = new \Ittools\Smslabs\Smslabs($appKey, $secret);

**Send SMS:**
 - $sms->setSenderId($numberOrSenderId)
 - ->setExpiration($minutes) // optionally
 - ->setSendDate($dateTime) // optionally
 - ->setIsFlashMessage($isFlashMessage) // optionally
 - ->add($number, $message)
 - ->send();

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
