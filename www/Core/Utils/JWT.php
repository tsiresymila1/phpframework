<?php

namespace Core\Utils;

use DateTime;

class JWT
{

    protected $secret;
    protected $expiration;

    public function __construct($secret = '7c32d31dbdd39f2111da0b1dea59e94f3ed715fd8cdf0ca3ecf354ca1a2e3e30', $expiration = 172800)
    {
        $this->secret = $secret;
        $this->expiration = $expiration;
    }

    public function base64UrlEncode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    public function generate($id = null, $roles = ['ROLE_ADMIN'])
    {
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $timestamp = (new DateTime('now'))->getTimestamp();
        $expired = $timestamp + $this->expiration;

        $payload = json_encode([
            'user_id' => $id,
            'role' => $roles,
            'exp' => $expired
        ]);

        $base64UrlPayload = $this->base64UrlEncode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function verify($jwt)
    {
        $tokenParts = explode('.', $jwt);
        if (sizeof($tokenParts) === 3) {
            $header = base64_decode($tokenParts[0]);
            $payload = base64_decode($tokenParts[1]);
            $expiration = json_decode($payload)->exp;
            $signatureProvided = $tokenParts[2];
            $isExpired = (new DateTime('now'))->getTimestamp() > (int)$expiration;
            $base64UrlHeader = $this->base64UrlEncode($header);
            $base64UrlPayload = $this->base64UrlEncode($payload);
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
            $base64UrlSignature = $this->base64UrlEncode($signature);
            $signatureValid = ($base64UrlSignature === $signatureProvided);
            return $signatureValid && !$isExpired;
        } else {
            return false;
        }
    }
}
