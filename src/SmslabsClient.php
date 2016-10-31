<?php

namespace Ittoolspl\Smslabs;

use Ittoolspl\Smslabs\Entity\AccountBalance;
use Ittoolspl\Smslabs\Entity\Sender;
use Ittoolspl\Smslabs\Entity\SmsDetails;
use Ittoolspl\Smslabs\Entity\SmsIn;
use Ittoolspl\Smslabs\Entity\SmsOut;
use Ittoolspl\Smslabs\Entity\SmsSentResponse;
use Ittoolspl\Smslabs\Exception\EmptySMSQueueException;

class SmslabsClient
{
    const SEND_SMS_URL   = '/sendSms';
    const SENDERS_URL    = '/senders';
    const SMS_STATUS_URL = '/smsStatus';
    const SMS_LIST_URL   = '/sms';
    const SMS_IN_URL     = '/smsIn';
    const ACCOUNT_URL    = '/account';

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var bool
     */
    private $isFlashMessage = false;

    /**
     * @var string
     */
    private $senderId;

    /**
     * @var int
     */
    private $expirationMinutes = 0;

    /**
     * @var \DateTime
     */
    private $sendDateTime;

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
        $this->client = new HttpClient($appKey, $secretKey);
    }

    /**
     * @return HttpClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param HttpClient $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return boolean
     */
    public function isIsFlashMessage()
    {
        return $this->isFlashMessage;
    }

    /**
     * @param boolean $isFlashMessage
     * @return SmslabsClient $this
     */
    public function setIsFlashMessage($isFlashMessage)
    {
        $this->isFlashMessage = (bool)$isFlashMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @param string $senderId
     * @return SmslabsClient $this
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;

        return $this;
    }

    /**
     * @return int
     */
    public function getExpirationMinutes()
    {
        return $this->expirationMinutes;
    }

    /**
     * @param int $expirationMinutes
     * @return SmslabsClient $this
     */
    public function setExpirationMinutes($expirationMinutes)
    {
        if ($expirationMinutes < 1 || $expirationMinutes > 5520) {
            throw new \InvalidArgumentException('Valid values: 1 - 5520');
        }

        $this->expirationMinutes = (int)$expirationMinutes;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSendDateTime()
    {
        return $this->sendDateTime;
    }

    /**
     * @param \DateTime $sendDateTime
     * @return SmslabsClient $this
     */
    public function setSendDateTime(\DateTime $sendDateTime)
    {
        $this->sendDateTime = $sendDateTime;

        return $this;
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     * @param bool $isFlashMessage
     * @param int $expirationMinutes
     * @param \DateTime $sendDateTime
     * @return SmslabsClient $this
     */
    public function add(
        $phoneNumber,
        $message,
        $isFlashMessage = null,
        $expirationMinutes = null,
        \DateTime $sendDateTime = null
    ) {
        if ($this->checkPhoneNumber($phoneNumber) === false) {
            throw new \InvalidArgumentException('Invalid phone number');
        }

        $sms = [
            'phone_number' => $phoneNumber,
            'message' => $message,
            'flash' => $isFlashMessage === null ? (int)$this->isFlashMessage : (int)$isFlashMessage,
            'expiration' => $expirationMinutes === null ? (int)$this->expirationMinutes : (int)$expirationMinutes,
            'sender_id' => $this->senderId,
        ];

        if ($sendDateTime instanceof \DateTime) {
            $sms['send_date'] = (int)$sendDateTime->getTimestamp();
        } elseif ($this->sendDateTime instanceof \DateTime) {
            $sms['send_date'] = (int)$this->sendDateTime->getTimestamp();
        }

        $this->smsToSend[] = $sms;

        return $this;
    }

    /**
     * @param string $phoneNumber
     * @return bool
     */
    private function checkPhoneNumber($phoneNumber)
    {
        return (bool)preg_match('/^\+[0-9]{10,13}$/', $phoneNumber);
    }

    /**
     * @return SmslabsClient $this
     */
    public function send()
    {
        if (empty($this->smsToSend)) {
            throw new EmptySMSQueueException('No messages to send');
        }

        if ($this->senderId === null) {
            throw new \InvalidArgumentException('SenderId is missing');
        }

        foreach ($this->smsToSend as $key => $sms) {
            $httpResponse = $this->client->sendRequest(self::SEND_SMS_URL, $sms, 'PUT');

            $this->smsStatus[] = new SmsSentResponse($httpResponse['account'], $httpResponse['sms_id']);

            unset($this->smsToSend[$key]);
        }

        return $this;
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
        $sendersResponse = $this->client->sendRequest(self::SENDERS_URL);

        $list = [];

        foreach ($sendersResponse as $sender) {
            $list[] = Sender::createFromResponseArray($sender);
        }

        return $list;
    }

    /**
     * @return AccountBalance
     */
    public function getAccountBalance()
    {
        $balanceResponse = $this->client->sendRequest(self::ACCOUNT_URL);

        return new AccountBalance($balanceResponse['account'] / 100);
    }

    /**
     * @return SmsIn[]
     */
    public function getSmsIn()
    {
        $smsInResponse = $this->client->sendRequest(self::SMS_IN_URL);

        $list = [];

        foreach ($smsInResponse as $smsIn) {
            $list[] = SmsIn::createFromResponseArray($smsIn);
        }

        return $list;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return SmsOut[]
     */
    public function getSmsOut($offset = 0, $limit = 100)
    {
        $smsOutResponse = $this->client->sendRequest(self::SMS_LIST_URL.'?offset='.$offset.'&limit='.$limit);

        $list = [];

        foreach ($smsOutResponse as $smsOut) {
            $list[] = SmsOut::createFromResponseArray($smsOut);
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

        $detailsResponse = $this->client->sendRequest(self::SMS_STATUS_URL.'?id='.$smsId);

        $smsDetails = SmsDetails::createFromResponseArray($detailsResponse);

        return $smsDetails;
    }

    /**
     * @return array[]
     */
    public function getSmsQueue()
    {
        return $this->smsToSend;
    }
}
