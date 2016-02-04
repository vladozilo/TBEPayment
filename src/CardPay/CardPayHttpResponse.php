<?php

namespace TBEPayment\CardPay;

use TBEPayment\Payment\AbstractSha256SignedMessage;
use TBEPayment\Payment\HttpPaymentResponseInterface;
use TBEPayment\Exception\Exception;

class CardPayHttpResponse extends AbstractSha256SignedMessage implements HttpPaymentResponseInterface
{
    const CardPay_PublicKeys_URL = "https://moja.tatrabanka.sk/e-commerce/ecdsa_keys.txt";
    private $publicKeysUrl = self::CardPay_PublicKeys_URL;

    public function SetPublicKeysUrl($url) {
        $this->publicKeysUrl = $url;
    }
    protected $isVerified = false;

    public function __construct($fields = null)
    {
        $this->readOnlyFields = ['AMT', 'CURR', 'VS', 'RES', 'AC', 'TID', 'TIMESTAMP', 'HMAC', 'ECDSA_KEY', 'ECDSA'];

        if ($fields == null) {
            $fields = $_GET;
        }

        foreach ($this->readOnlyFields as $field) {
            $this->fields[$field] = isset($fields[$field]) ? $fields[$field] : null;
        }
    }

    protected function validateData()
    {
        return true;
    }

    protected function getSignatureBase()
    {
        return "{$this->AMT}{$this->CURR}{$this->VS}{$this->RES}{$this->AC}{$this->TID}{$this->TIMESTAMP}";
    }

    protected function getSignatureEcdsaBase()
    {
        return "{$this->AMT}{$this->CURR}{$this->VS}{$this->RES}{$this->AC}{$this->TID}{$this->TIMESTAMP}{$this->HMAC}";
    }

    protected function getPublicKey($id)
    {
        $keys = file_get_contents($this->publicKeysUrl);

        if(preg_match("~KEY_ID: $id\s+STATUS: VALID\s+(-{5}BEGIN PUBLIC KEY-{5}.+?-{5}END PUBLIC KEY-{5})~s", $keys, $match)){
            $result = $match[1];

            return $result;
        }
        return null;

    }

    public function VerifyAuthenticationCode($password)
    {
        if ($this->MID != $this->computeSign($password)) {
            throw new Exception('VerifyAuthenticationCode');
            return false;
        }

        $this->isVerified = true;
        return true;
    }

    public function VerifySignature()
    {

        $this->getSignatureBase();

        $publicKey = $this->getPublicKey($this->ECDSA_KEY);

        if ($this->verifySign($this->ECDSA, $publicKey) !== 1) {
            throw new Exception('VerifySignature');
            return false;
        }

        $this->isVerified = true;
        return true;
    }

    public function GetPaymentResponse()
    {
        if (!$this->isVerified)
            throw new Exception(__METHOD__.": Message was not verified yet.");

        if ($this->RES == "FAIL")
            return IEPaymentHttpPaymentResponse::RESPONSE_FAIL;
        else if ($this->RES == "OK")
            return IEPaymentHttpPaymentResponse::RESPONSE_SUCCESS;
        else
            return null;
    }
}