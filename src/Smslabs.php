<?php

namespace Ittools\Smslabs;

use GuzzleHttp\Client;
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
    private $isFlashMessage = false;

    /**
     * @var string
     */
    private $senderId = null;

    /**
     * @var int
     */
    private $expirationMinutes = 0;

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
     * @param boolean $isFlashMessage
     */
    public function setIsFlashMessage($isFlashMessage)
    {
        $this->isFlashMessage = (bool)$isFlashMessage;
    }

    /**
     * @param string $senderId
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
    }

    /**
     * @param int $expirationMinutes
     */
    public function setExpiration($expirationMinutes)
    {
        if ($expirationMinutes < 1 || $expirationMinutes > 5520) {
            throw new \InvalidArgumentException('Valid values: 1 - 5520');
        }

        $this->expirationMinutes = (int)$expirationMinutes;
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
     * @param string $message
     * @param bool $isFlashMessage
     * @param int $expirationMinutes
     * @param \DateTime $sendDateTime
     * @return bool
     */
    public function add(
        $phoneNumber,
        $message,
        $isFlashMessage = null,
        $expirationMinutes = null,
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
            'flash' => $isFlashMessage === null ? (int)$this->isFlashMessage : (int)$isFlashMessage,
            'expiration' => $expirationMinutes === null ? (int)$this->expirationMinutes : (int)$expirationMinutes,
            'sender_id' => $this->senderId,
        ];

        if ($this->sendDateTime !== null || $sendDateTime !== null) {
            $sms['send_date'] = (int)$sendDateTime->getTimestamp();
        }

        $this->smsToSend[] = $sms;

        return true;
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
     * @return bool
     */
    public function send()
    {
        if (empty($this->smsToSend)) {
            return false;
        }

        if ($this->senderId === null) {
            throw new \InvalidArgumentException('SenderId is missing');
        }

        foreach ($this->smsToSend as $sms) {
            $httpResponse = $this->sendRequest(self::SEND_SMS_URL, $sms, 'PUT');
            $this->smsStatus[] = new SmsSentResponse($httpResponse->account, $httpResponse->sms_id);
        }

        $this->smsToSend = [];

        return true;
    }

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @return \stdClass
     * @throws InvalidResponseException
     */
    private function sendRequest($url, $data = null, $method = 'GET')
    {
        if (!in_array($method, ['GET', 'PUT'])) {
            throw new \InvalidArgumentException('Invalid method type.');
        }

        $client = new Client();
        $response = $client->request($method, self::API_URL.$url, [
            'auth' => [$this->appKey, $this->secretKey],
            'form_params' => $data,
        ]);

        if ($response->getStatusCode() != 200) {
            throw new InvalidResponseException();
        }

        $bodyJson = (string)$response->getBody();

        $bodyObj = \GuzzleHttp\json_decode($bodyJson);

        if (!property_exists($bodyObj, 'data')) {
            throw new InvalidResponseException('Missing data property in response');
        }

        return $bodyObj->data;
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
            $list[] = Sender::createFromResponseObject($sender);
        }

        return $list;
    }

    /**
     * @return AccountBalance
     */
    public function getAccountBalance()
    {
        $response = $this->sendRequest(self::ACCOUNT_URL);

        return new AccountBalance($response->account / 100);
    }

    /**
     * @return InSms[]
     */
    public function getSmsIn()
    {
        $response = $this->sendRequest(self::SMS_IN_URL);

        $list = [];

        foreach ($response as $sms) {
            $list[] = InSms::createFromResponseObject($sms);
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
            $list[] = OutSms::createFromResponseObject($sms);
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

        $smsDetails = SmsDetails::createFromResponseObject($sms);

        return $smsDetails;

    }

}