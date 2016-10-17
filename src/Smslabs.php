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

    /**
     * @var string
     */
    private $appKey = null;

    /**
     * @var string
     */
    private $secretKey = null;

    /**
     * @var bool
     */
    private $isFlash = false;

    /**
     * @var string
     */
    private $senderId = null;

    /**
     * @var int
     */
    private $expiratonMinutes = 0;

    /**
     * @var \DateTime
     */
    private $sendDateTime = null;

    /**
     * @var array[]
     */
    private $smsToSend = [];

    /**
     * @var SmsSentResponse[]
     */
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
     * @param int $expiratonMinutes
     */
    public function setExpiraton($expiratonMinutes)
    {
        if ($expiratonMinutes < 1 || $expiratonMinutes > 5520) {
            throw new \InvalidArgumentException('Valid values: 1 - 5520');
        }

        $this->expiratonMinutes = (int)$expiratonMinutes;
    }

    /**
     * @param \DateTime $sendDateTime
     */
    public function setSendDate(\DateTime $sendDateTime)
    {
        $this->sendDateTime = $sendDateTime;
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
     * @param int $expiratonMinutes
     * @param \DateTime $sendDateTime
     * @return bool
     */
    public function add(
        $phoneNumber,
        $message,
        $isFlash = null,
        $expiratonMinutes = null,
        \DateTime $sendDateTime = null
    ) {
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
            'expiration' => $expiratonMinutes === null ? (int)$this->expiratonMinutes : (int)$expiratonMinutes,
            'sender_id' => $this->senderId,
        ];

        if ($this->sendDateTime !== null || $sendDateTime !== null) {
            $sms['send_date'] = (int)$sendDateTime->getTimestamp();
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
            $httpResponse = $this->sendRequest(self::SEND_SMS_URL, $sms, 'PUT');
            $this->smsStatus[] = new SmsSentResponse($httpResponse['account'], $httpResponse['sms_id']);
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
     * @return SmsSentResponse[]
     */
    public function getSentStatus()
    {
        return $this->smsStatus;
    }

    /**
     * @return Sender[]
     */
    public function getAvailableSenders()
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

        return new AccountBalance($response['account'] / 100);
    }

    /**
     * @return InSms[]
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
     * @return OutSms[]
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
     * @param string $smsId
     * @return SmsDetails
     */
    public function getSmsDetails($smsId)
    {
        if ($this->senderId === null) {
            throw new \InvalidArgumentException('Sender ID');
        }

        $sms = $this->sendRequest(self::SMS_STATUS_URL.'?id='.$smsId);

        $smsDetails = SmsDetails::createFromArray($sms);

        return $smsDetails;

    }

}