<?php

namespace TBEpayment\Payment;

interface SignedPaymentRequestInterface
{
    public function GetRedirectUrl();
}