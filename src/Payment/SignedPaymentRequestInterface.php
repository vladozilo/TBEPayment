<?php

namespace TBEPayment\Payment;

interface SignedPaymentRequestInterface
{
    public function GetRedirectUrl();
}