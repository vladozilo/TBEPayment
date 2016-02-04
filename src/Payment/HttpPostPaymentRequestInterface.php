<?php

namespace TBEpayment\Payment;

interface HttpPostPaymentRequestInterface extends SignedPaymentRequestInterface
{
    public function SetUrlBase($url);
    public function GetPaymentRequestFields();
    public function GetUrlBase();
}
