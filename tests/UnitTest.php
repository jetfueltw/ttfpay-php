<?php

namespace Test;

use Faker\Factory;
use Jetfuel\Ttfpay\BankPayment;
use Jetfuel\Ttfpay\Constants\Bank;
use Jetfuel\Ttfpay\Constants\Channel;
use Jetfuel\Ttfpay\DigitalPayment;
use Jetfuel\Ttfpay\TradeQuery;
use Jetfuel\Ttfpay\Traits\NotifyWebhook;
use Jetfuel\Ttfpay\BalanceQuery;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    private $orgId;
    private $merchantId;
    private $secretKey;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->orgId = getenv('ORG_ID');
        $this->merchantId = getenv('MERCHANT_ID');
        $this->secretKey = getenv('SECRET_KEY');
    }

    public function testDigitalPaymentOrder()
    {
        $faker = Factory::create();
        $tradeNo = date('YmdHis').rand(1000, 9999);
        $channel = Channel::ALIPAY;
        $amount = 1001;
        $notifyUrl = 'http://a.a.com';
        $returnUrl = 'http://a.a.com';

        $payment = new DigitalPayment($this->orgId, $this->merchantId, $this->secretKey);
        $result = $payment->order($tradeNo, $channel, $amount, $notifyUrl, $returnUrl);
        var_dump($result);
        $this->assertArrayHasKey('result',$result);
        
        return $tradeNo;
    }

    /**
     * @depends testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    public function testDigitalPaymentOrderFind($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->orgId, $this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);
        var_dump($result);
        $this->assertEquals('00', $result['respCode']);
    }

    /**
     * @depends testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    public function testDigitalPaymentOrderIsPaid($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->orgId, $this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testBankPaymentOrder()
    {
        $faker = Factory::create();
        $tradeNo = date('YmdHis').rand(1000, 9999);
        $bank = Bank::CMB;
        $amount = 2;
        $returnUrl = 'http://www.yahoo.com';//$faker->url;
        $notifyUrl = 'http://www.yahoo.com';//'$faker->url;

        $payment = new BankPayment($this->orgId, $this->merchantId, $this->secretKey);
        $result = $payment->order($tradeNo, $bank, $amount, $notifyUrl, $returnUrl);
        var_dump($result);

        $this->assertContains('http', $result, '', true);

        return $tradeNo;
    }

    /**
     * @depends testBankPaymentOrder
     *
     * @param $tradeNo
     */
    public function testBankPaymentOrderFind($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->orgId, $this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);
        
        $this->assertEquals('00', $result['respCode']);
    }

    /**
     * @depends testBankPaymentOrder
     *
     * @param $tradeNo
     */
    public function testBankPaymentOrderIsPaid($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->orgId, $this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testTradeQueryFindOrderNotExist()
    {
        $faker = Factory::create();
        $tradeNo = substr($faker->uuid,0,20);

        $tradeQuery = new TradeQuery($this->orgId, $this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);

        $this->assertNull($result);
    }

    public function testTradeQueryIsPaidOrderNotExist()
    {
        $faker = Factory::create();
        $tradeNo = substr($faker->uuid,0,20);

        $tradeQuery = new TradeQuery($this->orgId, $this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testNotifyWebhookVerifyNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'orgid'          => '0320182127160693',
            'merno'          => '162018282716063887',
            'amount'         => '100',
            'goods_info'     => 'goods_info',
            'trade_date'     => '2018-07-02 17:18:34',
            'trade_status'   => '0',
            'order_id'       => '201807021717589354',
            'plat_order_id'  => '2018040917175841914692',
            'sign_data'      => '0edef5dfd2a73432d9bceab84c23a776',
            'timestamp'      => '20180702185013',
        ];
        
        $this->assertTrue($mock->verifyNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookParseNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'orgid'          => '0320182127160693',
            'merno'          => '162018282716063887',
            'amount'         => '100',
            'goods_info'     => 'goods_info',
            'trade_date'     => '2018-07-02 17:18:34',
            'trade_status'   => '0',
            'order_id'       => '201807021717589354',
            'plat_order_id'  => '2018040917175841914692',
            'sign_data'      => '0edef5dfd2a73432d9bceab84c23a776',
            'timestamp'      => '20180702185013',
        ];

        $this->assertEquals([
            'orgid'          => '0320182127160693',
            'merno'          => '162018282716063887',
            'amount'         => '1',
            'goods_info'     => 'goods_info',
            'trade_date'     => '2018-07-02 17:18:34',
            'trade_status'   => '0',
            'order_id'       => '201807021717589354',
            'plat_order_id'  => '2018040917175841914692',
            'sign_data'      => '0edef5dfd2a73432d9bceab84c23a776',
            'timestamp'      => '20180702185013',
        ], $mock->parseNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookSuccessNotifyResponse()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $this->assertEquals('{"responseCode": "0000"}', $mock->successNotifyResponse());
    }

}
