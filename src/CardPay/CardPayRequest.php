<?php

namespace TBEpayment\CardPay;

class CardPayRequest extends AbstractSha256SignedMessage implements HttpRedirectPaymentRequestInterface
{
    const CardPay_EPayment_URL_Base = "https://moja.tatrabanka.sk/cgi-bin/e-commerce/start/e-commerce.jsp";
    private $redirectUrlBase = self::CardPay_EPayment_URL_Base;

    protected $MID;
    protected $secretKey;

    public function SetRedirectUrlBase($url) {
        $this->redirectUrlBase = $url;
    }

    public function __construct($mid, $secretkey) {
        $this->MID = $mid;
        $this->secretkey = $secretkey;

        $this->readOnlyFields = ['HMAC'];
        $this->requiredFields = ['MID', 'AMT', 'CURR', 'VS', 'RURL', 'IPC', 'NAME', 'TIMESTAMP'];
        $this->optionalFields = ['TXN', 'REM', 'TPAY', 'CID', 'AREDIR', 'LANG'];

        $this->PT = 'CardPay';
    }

    protected function setDefaults()
    {
        if(isempty($this->CURR)) {
            $this->CURR = '978';
        }

        if(isempty($this->IPC)) {
            $this->IPC = $_SERVER['REMOTE_ADDR'];
        }

        if(isempty($this->TIMESTAMP)) {
            $this->TIMESTAMP = date('dmYHis');
        }

        if(isempty($this->AREDIR)) {
            $this->AREDIR = '1';
        }
    }

    protected function getSignatureBase() {
        return "{$this->MID}{$this->AMT}{$this->CURR}{$this->VS}{$this->TXN}{$this->RURL}{$this->IPC}{$this->NAME}{$this->REM}{$this->TPAY}{$this->CID}{$this->TIMESTAMP}";
    }

    // nedokoncene
    protected function validateData() {
        try {
            $this->setDefaults();

            if (!is_string($this->AMT))
                $this->AMT = sprintf("%01.2F", $this->AMT);

            if (!preg_match('/^[0-9a-z]{3,4}$/', $this->MID)) throw new Exception('Merchant ID is in wrong format');
            if (!preg_match('/^[0-9]+(\\.[0-9]+)?$/', $this->AMT)) throw new Exception('Amount is in wrong format');
            if (strlen($this->VS) > 10) throw new Exception('Variable Symbol is in wrong format');
            if (!preg_match('/^[0-9]+$/', $this->VS)) throw new Exception('Variable Symbol is in wrong format');
            if (isempty($this->RURL)) throw new Exception('Return URL is in wrong format');
            $urlRestrictedChars = array('&', '?', ';', '=', '+', '%');
            foreach ($urlRestrictedChars as $char)
                if (false !== strpos($this->RURL, $char)) throw new Exception('Return URL contains restricted character: "'.$char.'"');

            // nepovinne
            if (!isempty($this->PT))
                if ($this->PT != 'CardPay') throw new Exception('Payment Type parameter must be "CardPay"');

            if (!isempty($this->REM))
                if (!preg_match('/^[0-9a-z_]+(\.[0-9a-z_]+)*@([12]?[0-9]{0,2}(\.[12]?[0-9]{0,2}){3}|([a-z][0-9a-z\-]*\.)+[a-z]{2,6})$/', $this->REM)) throw new Exception('Return e-mail address in wrong format');
            if (!isempty($this->LANG)) {
                $validLanguages = array('SK', 'EN', 'DE', 'HU', 'CZ', 'ES', 'FR', 'IT', 'PL');
                if (!in_array($this->LANG, $validLanguages)) throw new Exception('Unknown language, known languages are: '.implode(',', $validLanguages));
            }
            return true;

        } catch (Exception $e) {
            if (defined('DEBUG') && DEBUG) {
                throw $e;
            }
            return false;
        }
    }

    protected function computeHmacSign()
    {
        $this->HMAC = $this->computeSign($this->secretKey);
    }

    public function GetRedirectUrl() {
        $this->computeHmacSign();

        $url = $this->redirectUrlBase;

        $url .= '?';

        foreach ($this->requiredFields as $field) {
            $url .= $field . '=' . $this->$field . '&';
        }

        foreach ($this->readOnlyFields as $field) {
            $url .= $field . '=' . $this->$field . '&';
        }

        foreach ($this->optionalFields as $field) {
            if (!isempty($this->$field)) {
                $url .= $field . '=' . $this->$field . '&';
            }
        }

        $url .= 'HMAC=' . $this->HMAC;

        return $url;
    }
}
