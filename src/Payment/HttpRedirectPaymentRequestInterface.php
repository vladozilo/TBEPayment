<?php

namespace TBEpayment\Payment;

interface HttpRedirectPaymentRequestInterface extends SignedPaymentRequestInterface
{
    public function SetRedirectUrlBase($url);
}