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
            'TraceNo'        => '5d4c39a7-1d10-4ed7-a88d-5886c599f88f',
            'MsgSender'      => 'REALPAY',
            'SendTime'       => '20180301114000',
            'InstCode'       => 'CCB',
            'OrderNo'        => '201807021717589354',
            'OrderAmount'    => '10',
            'TransNo'        => '2018030111114833314',
            'TransAmount'    => '10',
            'TransStatus'    => '01',
            'TransType'      => 'PT001',
            'TransTime'      => '20180301110651',
            'PaymentNo'      => '2018030111114822011',
            'PayableFee'     => '0.00',
            'ReceivableFee'  => '0.00',
            'BankSerialNo'   => '2018030111114822011',
            'PayChannel'     => '19',
            'MerchantNo'     => '00013300000355',
            'SignType'       => 'MD5',
            'SignMsg'        => '7604FA442546C9231113E62D5875B032',
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
            'TraceNo'        => '5d4c39a7-1d10-4ed7-a88d-5886c599f88f',
            'MsgSender'      => 'REALPAY',
            'SendTime'       => '20180301114000',
            'InstCode'       => 'CCB',
            'OrderNo'        => '201807021717589354',
            'OrderAmount'    => '10',
            'TransNo'        => '2018030111114833314',
            'TransAmount'    => '10',
            'TransStatus'    => '01',
            'TransType'      => 'PT001',
            'TransTime'      => '20180301110651',
            'PaymentNo'      => '2018030111114822011',
            'PayableFee'     => '0.00',
            'ReceivableFee'  => '0.00',
            'BankSerialNo'   => '2018030111114822011',
            'PayChannel'     => '19',
            'MerchantNo'     => '00013300000355',
            'SignType'       => 'MD5',
            'SignMsg'        => '7604FA442546C9231113E62D5875B032',
        ];

        $this->assertEquals([
            'Name'           => 'REP_B2CPAYMENT',
            'Version'        => 'V4.1.2.1.1',
            'Charset'        => 'utf-8',
            'TraceNo'        => '5d4c39a7-1d10-4ed7-a88d-5886c599f88f',
            'MsgSender'      => 'REALPAY',
            'SendTime'       => '20180301114000',
            'InstCode'       => 'CCB',
            'OrderNo'        => '201807021717589354',
            'OrderAmount'    => '10',
            'TransNo'        => '2018030111114833314',
            'TransAmount'    => '10',
            'TransStatus'    => '01',
            'TransType'      => 'PT001',
            'TransTime'      => '20180301110651',
            'PaymentNo'      => '2018030111114822011',
            'PayableFee'     => '0.00',
            'ReceivableFee'  => '0.00',
            'BankSerialNo'   => '2018030111114822011',
            'PayChannel'     => '19',
            'MerchantNo'     => '00013300000355',
            'SignType'       => 'MD5',
            'SignMsg'        => '7604FA442546C9231113E62D5875B032',
        ], $mock->parseNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookSuccessNotifyResponse()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $this->assertEquals('ok', $mock->successNotifyResponse());
    }

}
