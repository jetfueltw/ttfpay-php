<?php

namespace Jetfuel\Ttfpay;
use Jetfuel\Ttfpay\Traits\ResultParser;
use Jetfuel\Ttfpay\Constants\Bank;

class BankPayment extends Payment
{
    use ResultParser;

    /**
     * BankPayment constructor.
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
     * Create bank payment order.
     *
     * @param string $tradeNo
     * @param string $bank
     * @param float $amount
     * @param string $notifyUrl
     * @param string $returnUrl
     * @return string
     */
    public function order($tradeNo, $bank, $amount, $notifyUrl)
    {
        $businessData = [
            'out_trade_no'       => $tradeNo,
            'total_amount'       => $this->convertYuanToFen($amount),
            'subject'            => 'GOODS_SUBJECT',
            'body'               => 'GOODS_BODY',
            'pay_type'           => 'DEBIT_CARD',
            'channel'            => $bank,
            'payer_ip'           => '127.0.0.1',
            'referer_url'        => 'http://www.yahoo.com'
        ];
        $payload = $this->signPayload([
            'biz_content'        => json_encode($businessData),
            'app_id'     => $this->merchantId,
            'method'    => 'realpay.trade.ebankpay',
            'format'     => 'JSON',
            'charset'    => 'utf-8',
            'timestamp'  => date('Ymd H:i:s'),//$this->getCurrentTime(),
            'version'    => '1.0',
            'notify_url' => $notifyUrl,
            'sign_type'  => 'MD5'
        ]);

        $response = $this->parseResponse($this->httpClient->formpost('mas/realpay/gateway.do', $payload));
        
        if (isset($response['realpay_trade_create_ebank_pay_response']['pay_url']) && isset($response['realpay_trade_create_ebank_pay_response']['code'])) 
        {
           if ($response['realpay_trade_create_ebank_pay_response']['code'] == '0000') {
            $url = $response['realpay_trade_create_ebank_pay_response']['pay_url'];
            
            return '<script> window.location = "'. $url .'"; </script>';
           }
        }

        return null;
    }
}
