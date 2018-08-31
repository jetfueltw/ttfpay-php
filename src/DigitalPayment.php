<?php

namespace Jetfuel\Ttfpay;

use Jetfuel\Ttfpay\Traits\ResultParser;
use Jetfuel\Ttfpay\Constants\Channel;

class DigitalPayment extends Payment
{
    use ResultParser;

    /**
     * DigitalPayment constructor.
     *
     * @param string $merchantId
     * @param string $secretKey
     * @param null|string $baseApiUrl
     */
    public function __construct($merchantId, $secretKey, $baseApiUrl = null)
    {
        parent::__construct($merchantId, $secretKey, $baseApiUrl);
    }

    /**
     * Create digital payment order.
     *
     * @param string $tradeNo
     * @param string $channel
     * @param float $amount
     * @param string $notifyUrl
     * @param string $returnUrl
     * @return array
     */
    public function order($tradeNo, $channel, $amount, $notifyUrl)
    {
        
        $payload = $this->signPayload([
            'merchantNo'          => $this->merchantId,
            'orderTime'           => $this->getCurrentTime(),
            'customerOrderNo'     => $tradeNo,
            'amount'              => $this->convertYuanToFen($amount),
            'subject'             => 'GOODS_SUBJECT',
            'body'                => 'GOODS_BODY',
            'payerIp'             => '127.0.0.1',
            'payerAccountNo'      => '6217002430014693379',
            'notifyUrl'           => $notifyUrl,
            'channel'             => $channel,
            'payType'             => CHANNEL::CHANNEL_PAYTYPE[$channel],
            'signType'            =>'MD5'
        ]);
        return $this->parseResponse($this->httpClient->post('mas/mobile/create.do', $payload));
    }
}
