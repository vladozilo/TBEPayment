<?php

namespace TBEpayment;

use TBEpayment\CardPay\CardPayRequest;
use TBEpayment\CardPay\CardPayHttpResponse;


class CardPay extends AbstractPayment
{
	public function request($mid, $secretKey)
	{
		$Pres = new CardPayRequest($mid, $secretKey);

		$Pres->AMT = $this->amount;
		$Pres->VS = $this->variableSymbol;
		$Pres->RURL = $this->returnUrl;
		$Pres->NAME = $this->customerName;

		if (!$Pres->Validate()) {
			return false;
		}

		$paymentRequestUrl = $Pres->GetRedirectUrl();

		return $paymentRequestUrl;
	}

	public function response($secretKey)
	{
		$Pres = new CardPayHttpResponse();

		if (!$Pres->Validate() || !$Pres->VerifyAuthenticationCode($secretKey) || !$Pres->VerifySignature()) {
			return false;
		}

		$result = $Pres->GetPaymentResponse();
		return $result;
	}
}
