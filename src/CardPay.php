<?php

namespace TBEPayment;

use TBEPayment\CardPay\CardPayRequest;
use TBEPayment\CardPay\CardPayHttpResponse;

class CardPay extends AbstractPayment
{
	public function request($mid, $secretKey, $redirectUrlBase = null)
	{
		$Pres = new CardPayRequest($mid, $secretKey);

		$Pres->AMT = $this->amount;
		$Pres->VS = $this->variableSymbol;
		$Pres->RURL = $this->returnUrl;
		$Pres->NAME = $this->customerName;

		if (!$Pres->Validate()) {
			return false;
		}

		if($redirectUrlBase) {
			$Pres->SetRedirectUrlBase($redirectUrlBase);
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

		$result = [
			'result' => $Pres->GetPaymentResponse(),
			'vs' => $Pres->GetVS()
		];

		return $result;
	}

}
