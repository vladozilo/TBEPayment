<?php

namespace TBEPayment\Payment;

interface HttpRedirectPaymentRequestInterface extends SignedPaymentRequestInterface
{
    public function SetRedirectUrlBase($url);
}