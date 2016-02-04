<?php

namespace TBEpayment\CardPay;

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

        $this->fields['VS'] = isset($fields['VS']) ? $fields['VS'] : null;
        $this->fields['AC'] = isset($fields['AC']) ? $fields['AC'] : null;
        $this->fields['RES'] = isset($fields['RES']) ? $fields['RES'] : null;
        $this->fields['SIGN'] = isset($fields['SIGN']) ? $fields['SIGN'] : null;
    }

    protected function validateData()
    {
        if (isempty($this->VS)) return false;
        if (!($this->RES == "FAIL" || $this->RES == "OK" || $this->RES == "TOUT")) return false;

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