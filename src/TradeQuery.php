<?php

namespace Jetfuel\Ttfpay;

use Jetfuel\Ttfpay\Traits\ResultParser;


//只適用網銀
class TradeQuery extends Payment
{
    use ResultParser;

    /**
     * TradeQuery constructor.
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
     * Find Order by trade number.
     *
     * @param string $tradeNo
     * @return array|null
     */
    public function find($tradeNo)
    {
        $businessData = [
            'out_trade_no'  => $tradeNo,
        ];
        $payload = $this->signPayload([
            'biz_content'        => json_encode($businessData),
            'app_id'     => $this->merchantId,
            'method'    => 'realpay.trade.ebankquery',
            'format'     => 'JSON',
            'charset'    => 'utf-8',
            'timestamp'  => date('Ymd H:i:s'),
            'version'    => '1.0',
            'sign_type'  => 'MD5'
        ]);

        $order = $this->parseResponse($this->httpClient->formpost('mas/realpay/gateway.do', $payload));
        
        if (!isset($order['realpay_trade_query_response']['code']) || $order['realpay_trade_query_response']['code'] !== '0000') {
            return null;
        }

        return $order;
    }

    /**
     * Is order already paid.
     *
     * @param string $tradeNo
     * @return bool
     */
    public function isPaid($tradeNo)
    {
        $order = $this->find($tradeNo);

        //付款成功才有 trade_status欄位
        if ($order === null || !isset($order['realpay_trade_query_response']['code']) || $order['realpay_trade_query_response']['code'] !== '0000' || !isset($order['realpay_trade_query_response']['trade_status'])) {
            return false;
        }

        return true;
    }
}
