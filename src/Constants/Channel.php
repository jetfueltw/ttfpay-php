<?php

namespace Jetfuel\Ttfpay\Constants;

class Channel
{
    /**
     * 微信支付 
     */
    const WECHAT = 'WEIXIN';

    /**
     * 支付寶 
     */
    const ALIPAY = 'ALIPAY';

    /**
     * QQ錢包
     */
    const QQ = 'QQ';

    /**
     * 銀聯
     */
    const UNIONPAY = 'UNIONPAY';

    const CHANNEL_PAYTYPE = [
        'WEIXIN' => 'NATIVE',
        'ALIPAY' => 'AliPayScan',
        'QQ' => 'QqScan',
        'UNIONPAY' => 'UnionpayH5',
    ];

}