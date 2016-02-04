<?php

namespace TBEPayment\Payment;

use TBEPayment\Exception\Exception;

abstract class AbstractPaymentMessage
{
    protected $fields = [];
    protected $readOnlyFields = [];
    protected $requiredFields = [];
    protected $optionalFields = [];
    protected $isValid = false;
    protected $PT;

    public function __GET($name)
    {
        if (!isset($this->fields[$name])) {
            return null;
        }

        return $this->fields[$name];
    }

    public function __SET($name, $value)
    {
        if (in_array($name, $this->readOnlyFields)) {
            throw new Exception("Trying to change a read only field '$name'.");
        }

        if (!in_array($name, $this->requiredFields) && !in_array($name, $this->optionalFields)) {
            throw new Exception("Trying to set unknown field '$name'.");
        }

        $this->fields[$name] = $value;
        $this->isValid = false;
    }

    protected function checkRequiredFields()
    {
        foreach ($this->requiredFields as $requiredField) {
            if (!isset($this->fields[$requiredField])) {
                throw new Exception("Required field '" . $requiredField . "' missing");
                return false;
            }
        }

        return true;
    }

    public function Validate()
    {
        if (!$this->checkRequiredFields()) {
            return false;
        }

        if (!$this->validateData()) {
            return false;
        }

        $this->isValid = true;

        return true;
    }

    public abstract function computeSign($sharedSecret);

    protected abstract function validateData();

    protected abstract function getSignatureBase();
}