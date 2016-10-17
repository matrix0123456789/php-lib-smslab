<?php

namespace Ittools\Smslabs;

use Ittools\Smslabs\Exception\InvalidResponseException;

class Smslabs
{
    const API_URL = 'https://api.smslabs.net.pl/apiSms';

    const SEND_SMS_URL = '/sendSms';
    const SENDERS_URL = '/senders';
    const SMS_STATUS_URL = '/smsStatus';
    const SMS_LIST_URL = '/sms';
    const SMS_IN_URL = '/smsIn';
    const ACCOUNT_URL = '/account';

    private $appKey = null;
    private $secretKey = null;

    private $isFlash = false;
    private $senderId = null;
    private $expiraton = 0;
    private $sendDate = null;

    private $smsToSend = [];
    private $smsStatus = [];

    /**
     * Smslabs constructor.
     * @param string $appKey
     * @param string $secretKey
     */
    public function __construct($appKey, $secretKey)
    {
        $this->appKey = $appKey;
        $this->secretKey = $secretKey;
    }

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
        if ($expiraton < 1 || $expiraton > 5520) {
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
     * @param string $phoneNumber
     * @return bool
     */
    private function checkPhoneNumber($phoneNumber)
    {
        if (preg_match('/\+[0-9]{10,13}/', $phoneNumber)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     * @param bool $isFlash
     * @param int $expiration
     * @param \DateTime $sendDate
     * @return bool
     */
    public function add($phoneNumber, $message, $isFlash = null, $expiration = null, \DateTime $sendDate = null)
    {
        if (strlen($phoneNumber) == 9) {
            $phoneNumber = '+48'.$phoneNumber;
        }

        if ($this->checkPhoneNumber($phoneNumber) === false) {
            return false;
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

        return true;
    }

    /**
     * @return bool
     */
    public function send()
    {
        if ($this->smsToSend == []) {
            return false;
        }

        if ($this->senderId === null) {
            throw new \InvalidArgumentException('SenderId is missing');
        }

        foreach ($this->smsToSend as $sms) {
            $this->smsStatus[] = $this->sendRequest(self::SEND_SMS_URL, $sms, 'PUT');
        }

        $this->smsToSend = [];

        return true;
    }

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @return array
     * @throws InvalidResponseException
     */
    private function sendRequest($url, $data = null, $method = 'GET')
    {
        $curl = curl_init(self::API_URL.$url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $this->appKey.':'.$this->secretKey);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if ($data !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        if (!$response || !isset($response['code']) || $response['code'] != 200) {
            throw new InvalidResponseException();
        }

        return $response['data'];
    }

    /**
     * @return array
     */
    public function getSentStatus()
    {
        return $this->smsStatus;
    }

    /**
     * @return array
     */
    public function getSenders()
    {
        $response = $this->sendRequest(self::SENDERS_URL);

        $list = [];

        foreach ($response as $sender) {
            $list[] = new Sender($sender['id'], $sender['name'], $sender['sender']);
        }

        return $list;
    }

    /**
     * @return AccountBalance
     */
    public function getAccountBalance()
    {
        $response = $this->sendRequest(self::ACCOUNT_URL);

        return new AccountBalance(isset($response['account']) ? $response['account'] : null);
    }

    /**
     * @return array
     */
    public function getSmsIn()
    {
        $response = $this->sendRequest(self::SMS_IN_URL);

        $list = [];

        foreach ($response as $sms) {
            $list[] = new InSms(
                $sms['_id'],
                $sms['s_cnt'],
                $sms['s_con'],
                $sms['no_to'],
                $sms['in_t'],
                $sms['no_fr'],
                $sms['stat']
            );
        }

        return $list;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getSmsOut($offset = 0, $limit = 100)
    {
        $response = $this->sendRequest(self::SMS_LIST_URL.'?offset='.$offset.'&limit='.$limit);

        $list = [];

        foreach ($response as $sms) {
            $list[] = new OutSms(
                $sms['_id'],
                $sms['del_t'],
                $sms['s_cnt'],
                $sms['price'],
                $sms['s_con'],
                $sms['no_to'],
                $sms['in_t'],
                $sms['stat'],
                $sms['stat_d'],
                $sms['no_fr']
            );
        }

        return $list;
    }

        /**
     * @param int $id
     * @return SmsDetails
     */
    public function getSmsDetails($id)
    {
        if ($this->senderId === null) {
            throw new \InvalidArgumentException('Sender ID');
        }

        $sms = $this->sendRequest(self::SMS_STATUS_URL.'?id='.$id);

        $smsDetails = SmsDetails::createFromArray($sms);
        return $smsDetails;
        
    }

}