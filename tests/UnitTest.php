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

        $this->merchantId = getenv('MERCHANT_ID');
        $this->secretKey = getenv('SECRET_KEY');
    }

    // public function testDigitalPaymentOrder()
    // {
    //     $faker = Factory::create();
    //     $tradeNo = date('YmdHis').rand(1000, 9999);
    //     $channel = Channel::UNIONPAY;
    //     $amount = 10;
    //     $notifyUrl = 'http://a.a.com';
    //     //$returnUrl = 'http://a.a.com';

    //     $payment = new DigitalPayment($this->merchantId, $this->secretKey);
    //     $result = $payment->order($tradeNo, $channel, $amount, $notifyUrl);
    //     var_dump($result);
    //     //$this->assertArrayHasKey('result',$result);
        
    //     return $tradeNo;
    // }

    /**
     * @dependss testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    // public function testDigitalPaymentOrderFind($tradeNo)
    // {
    //     $tradeQuery = new TradeQuery($this->orgId, $this->merchantId, $this->secretKey);
    //     $result = $tradeQuery->find($tradeNo);
    //     var_dump($result);
    //     $this->assertEquals('00', $result['respCode']);
    // }

    /**
     * @dependss testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    // public function testDigitalPaymentOrderIsPaid($tradeNo)
    // {
    //     $tradeQuery = new TradeQuery($this->orgId, $this->merchantId, $this->secretKey);
    //     $result = $tradeQuery->isPaid($tradeNo);

    //     $this->assertFalse($result);
    // }

    public function testBankPaymentOrder()
    {
        $faker = Factory::create();
        $tradeNo = date('YmdHis').rand(1000, 9999);
        $bank = Bank::CMBC;
        $amount = 10;
        $returnUrl = 'http://www.yahoo.com';//$faker->url;
        $notifyUrl = 'http://www.yahoo.com';//'$faker->url;

        $payment = new BankPayment($this->merchantId, $this->secretKey);
        $result = $payment->order($tradeNo, $bank, $amount, $notifyUrl);
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
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);
        
        $this->assertEquals('0000', $result['realpay_trade_query_response']['code']);
    }

    /**
     * @depends testBankPaymentOrder
     *
     * @param $tradeNo
     */
    public function testBankPaymentOrderIsPaid($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testTradeQueryFindOrderNotExist()
    {
        $faker = Factory::create();
        $tradeNo = substr($faker->uuid,0,20);

        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);

        $this->assertNull($result);
    }

    public function testTradeQueryIsPaidOrderNotExist()
    {
        $faker = Factory::create();
        $tradeNo = substr($faker->uuid,0,20);

        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testNotifyWebhookVerifyNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'Name'           => 'REP_B2CPAYMENT',
            'Version'        => 'V4.1.2.1.1',
            'Charset'        => 'utf-8',
            'TraceNo'        => '9514a7a6-8198-4222-a39a-b7d58f60d356',
            'MsgSender'      => 'REALPAY',
            'SendTime'       => '20180917154515',
            'InstCode'       => 'CCB',
            'OrderNo'        => '201809171542537092',
            'OrderAmount'    => '100.00',
            'TransNo'        => '20180917150313553722',
            'TransAmount'    => '100.00',
            'TransStatus'    => '01',
            'TransType'      => 'PT001',
            'TransTime'      => '20180917154213',
            'MerchantNo'     => '00013300000355',
            'PaymentNo'      => '20180917150313553723',
            'PayableFee'     => '0.00',
            'ReceivableFee'  => '0.00',
            'PayChannel'     => '19',
            'BankSerialNo'   => '20180917150313553723',
            'SignType'       => 'MD5',
            'SignMsg'        => '4E41D5D797BB3259BCDD21B99B33B88E',
            'Ext1'           => 'http%3A%2F%2Fwww.yahoo.com',
        ];
        
        $this->assertTrue($mock->verifyNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookParseNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'Name'           => 'REP_B2CPAYMENT',
            'Version'        => 'V4.1.2.1.1',
            'Charset'        => 'utf-8',
            'TraceNo'        => '9514a7a6-8198-4222-a39a-b7d58f60d356',
            'MsgSender'      => 'REALPAY',
            'SendTime'       => '20180917154515',
            'InstCode'       => 'CCB',
            'OrderNo'        => '201809171542537092',
            'OrderAmount'    => '100.00',
            'TransNo'        => '20180917150313553722',
            'TransAmount'    => '100.00',
            'TransStatus'    => '01',
            'TransType'      => 'PT001',
            'TransTime'      => '20180917154213',
            'MerchantNo'     => '00013300000355',
            'PaymentNo'      => '20180917150313553723',
            'PayableFee'     => '0.00',
            'ReceivableFee'  => '0.00',
            'PayChannel'     => '19',
            'BankSerialNo'   => '20180917150313553723',
            'SignType'       => 'MD5',
            'SignMsg'        => '4E41D5D797BB3259BCDD21B99B33B88E',
            'Ext1'           => 'http%3A%2F%2Fwww.yahoo.com',
        ];

        $this->assertEquals([
            'Name'           => 'REP_B2CPAYMENT',
            'Version'        => 'V4.1.2.1.1',
            'Charset'        => 'utf-8',
            'TraceNo'        => '9514a7a6-8198-4222-a39a-b7d58f60d356',
            'MsgSender'      => 'REALPAY',
            'SendTime'       => '20180917154515',
            'InstCode'       => 'CCB',
            'OrderNo'        => '201809171542537092',
            'OrderAmount'    => '100.00',
            'TransNo'        => '20180917150313553722',
            'TransAmount'    => '100.00',
            'TransStatus'    => '01',
            'TransType'      => 'PT001',
            'TransTime'      => '20180917154213',
            'MerchantNo'     => '00013300000355',
            'PaymentNo'      => '20180917150313553723',
            'PayableFee'     => '0.00',
            'ReceivableFee'  => '0.00',
            'PayChannel'     => '19',
            'BankSerialNo'   => '20180917150313553723',
            'SignType'       => 'MD5',
            'SignMsg'        => '4E41D5D797BB3259BCDD21B99B33B88E',
            'Ext1'           => 'http%3A%2F%2Fwww.yahoo.com',
        ], $mock->parseNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookSuccessNotifyResponse()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $this->assertEquals('ok', $mock->successNotifyResponse());
    }

}
