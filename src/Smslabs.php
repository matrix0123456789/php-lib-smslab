<?php
/**
 * Created by PhpStorm.
 * User: jpyzio
 * Date: 10.10.16
 * Time: 19:21
 */

namespace Ittools\Smslabs;

class Smslabs
{
    const SEND_SMS_URL = 'https://api.smslabs.net.pl/apiSms/sendSms';
    const SENDERS_URL = 'https://api.smslabs.net.pl/apiSms/senders';
    const SMS_STATUS_URL = 'https://api.smslabs.net.pl/apiSms/smsStatus';
    const SMS_LIST_URL = 'https://api.smslabs.net.pl/apiSms/sms';
    const SMS_IN_URL = 'https://api.smslabs.net.pl/apiSms/smsIn';
    const ACCOUNT_URL = 'https://api.smslabs.net.pl/apiSms/account';

    private $appKey = null;
    private $secretKey = null;

    private $isFlash = false;
    private $senderId = null;
    private $expiraton = 0;
    private $sendDate = null;

    private $smsToSend = [];
    private $smsStatus = [];

    /**
     * @param boolean $isFlash
     */
    public function setIsFlash($isFlash)
    {
        $this->isFlash = (bool)$isFlash;
    }

    /**
     * @param string $senderId
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
    }

    /**
     * @param int $expiraton Minutes
     */
    public function setExpiraton($expiraton)
    {
        if ($expiraton > 5520 || $expiraton < 1) {
            throw new \InvalidArgumentException('Valid values: 1 - 5520');
        }

        $this->expiraton = (int)$expiraton;
    }

    /**
     * @param \DateTime $sendDate
     */
    public function setSendDate(\DateTime $sendDate)
    {
        $this->sendDate = $sendDate;
    }

    /**
     * Smslabs constructor.
     * @param string $appKey
     * @param string $secretKey
     * @param string $senderId
     */
    public function __construct($appKey, $secretKey, $senderId = null)
    {
        $this->appKey = $appKey;
        $this->secretKey = $secretKey;
        $this->senderId = $senderId;
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     * @param bool $isFlash
     * @param int $expiration
     * @param \DateTime $sendDate
     */
    public function add($phoneNumber, $message, $isFlash = null, $expiration = null, \DateTime $sendDate = null)
    {
        if (substr($phoneNumber, 0, 3) != '+48') {
            $phoneNumber = '+48'.$phoneNumber;
        }

        $sms = [
            'phone_number' => $phoneNumber,
            'message' => $message,
            'flash' => $isFlash === null ? (int)$this->isFlash : (int)$isFlash,
            'expiration' => $expiration === null ? (int)$this->expiraton : (int)$expiration,
            'sender_id' => $this->senderId,
        ];

        if ($this->sendDate !== null || $sendDate !== null) {
            $sms['send_date'] = (int)$sendDate->getTimestamp();
        }

        $this->smsToSend[] = $sms;
    }

    /**
     * @return bool
     */
    public function send()
    {
        if ($this->smsToSend == []) {
            return false;
        }

        foreach ($this->smsToSend as $sms) {
            $this->smsStatus[] = $this->sendRequest(self::SEND_SMS_URL, $sms, 'PUT');
        }

        $this->smsToSend = [];
    }

    /**
     * @return array|bool
     */
    public function status()
    {
        if ($this->smsStatus == []) {
            return false;
        }

        return $this->smsStatus;
    }

    /**
     * @return mixed
     */
    public function getSenders()
    {
        return $this->sendRequest(self::SENDERS_URL);
    }

    /**
     * @return mixed
     */
    public function getAccountBalance()
    {
        return $this->sendRequest(self::ACCOUNT_URL);
    }

    /**
     * @return mixed
     */
    public function getSmsIn()
    {
        return $this->sendRequest(self::SMS_IN_URL);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    public function getSmsOut($offset = 0, $limit = 100)
    {
        return $this->sendRequest(self::SMS_LIST_URL.'?offset='.$offset.'&limit='.$limit);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getSmsDetails($id)
    {
        return $this->sendRequest(self::SMS_STATUS_URL.'?id='.$id);
    }

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @return mixed
     */
    private function sendRequest($url, $data = null, $method = 'GET')
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $this->appKey.':'.$this->secretKey);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if ($data !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        return json_decode(curl_exec($curl));
    }
}