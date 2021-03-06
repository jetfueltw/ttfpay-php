<?php

namespace Jetfuel\Ttfpay;

class Signature
{
    /**
     * Generate signature.
     *
     * @param array $payload
     * @param string $secretKey
     * @return string
     */
    public static function generate(array $payload, $secretKey)
    {
        $baseString = self::buildBaseString($payload).$secretKey;

        return strtoupper(md5($baseString));
    }

    /**
     * Generate Notify signature.
     *
     * @param array $payload
     * @param string $secretKey
     * @return string
     */
    public static function generateNotify(array $payload, $secretKey)
    {
        $baseString = self::buildNotifyBaseString($payload).$secretKey;

        return strtoupper(md5($baseString));
    }

    /**
     * @param array $payload
     * @param string $secretKey
     * @param string $signature
     * @return bool
     */
    public static function validate(array $payload, $secretKey, $signature)
    {
        return self::generateNotify($payload, $secretKey) === strtoupper($signature);
    }

    private static function buildBaseString(array $payload)
    {
        ksort($payload);

        $baseString = '';
        foreach ($payload as $key => $value) {
            $baseString .= $key.'='.$value.'&';
        }

        return rtrim($baseString, '&');
    }

    private static function buildNotifyBaseString(array $payload)
    {
        ksort($payload);

        $baseString = '';
        foreach ($payload as $key => $value) {
            $baseString .= urldecode($value);
        }

        return $baseString;
    }
}
