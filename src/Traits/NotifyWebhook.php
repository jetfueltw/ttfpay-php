<?php

namespace Jetfuel\Ttfpay\Traits;

use Jetfuel\Ttfpay\Signature;

trait NotifyWebhook
{
    use ConvertMoney;
    /**
     * Verify notify request's signature.
     *
     * @param array $payload
     * @param $secretKey
     * @return bool
     */
    public function verifyNotifyPayload(array $payload, $secretKey)
    {
        if (!isset($payload['SignMsg'])) {
            return false;
        }

        $signature = $payload['SignMsg'];

        unset($payload['SignMsg']);
        
        return Signature::validate($payload, $secretKey, $signature);

    }

    /**
     * Verify notify request's signature and parse payload.
     *
     * @param array $payload
     * @param string $secretKey
     * @return array|null
     */
    public function parseNotifyPayload(array $payload, $secretKey)
    {
        if (!$this->verifyNotifyPayload($payload, $secretKey)) {
            return null;
        }
        
        return $payload;
    }

    /**
     * Response content for successful notify.
     *
     * @return string
     */
    public function successNotifyResponse()
    {
        return 'ok';
    }
}
