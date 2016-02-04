<?php

namespace TBEPayment\Payment;

use TBEPayment\Exception\Exception;

abstract class AbstractSha256SignedMessage extends AbstractPaymentMessage
{
	public function computeSign($sharedSecret)
    {
		if (!$this->isValid)
			throw new Exception(__METHOD__.": Message was not validated.");

		try {
			if (strlen($sharedSecret) == 128) {
				$sharedSecret = pack('H*', $sharedSecret);
			}

			$base = $this->GetSignatureBase();
			$sign = hash_hmac("sha256", $base, $sharedSecret);
			
		} catch (Exception $e) {
			return false;
		}

		return $sign;
	}

    public function verifySign($sharedSign, $publicKey)
    {
        if (!$this->isValid)
            throw new Exception(__METHOD__.": Message was not validated.");

        try {
            if (strlen($sharedSign) == 128) {
                $sharedSecret = pack('H*', $sharedSign);
            }
            $base = $this->GetSignatureBase();

            $verified = openssl_verify($base, $sharedSign, $publicKey, "sha256");

        } catch (Exception $e) {
            return false;
        }

        return $verified;
    }
}