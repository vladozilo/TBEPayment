<?php

namespace TBEpayment\Payment;

interface HttpPaymentResponseInterface
{
    public function VerifyAuthenticationCode($password);
    public function VerifySignature();

    public function GetPaymentResponse();

    const RESPONSE_SUCCESS = 1;
    const RESPONSE_FAIL    = 2;
    const RESPONSE_TIMEOUT = 3;
}
