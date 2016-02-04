<?php

namespace TBEPayment;

abstract class AbstractPayment
{
	/**
	 * amount
	 *
	 * @var	float
	 */
	protected $amount;

	/**
	 * variable symbol
	 * @var	string
	 */
	protected $variableSymbol;

	/**
	 * specific symbol
	 * 
	 * @var	string
	 */
	protected $specificSymbol;
	
	/**
	 * return url
	 * @var	string
	 */
	protected $returnUrl;

	/**
	 * Customer name or other identificator like email atc.
	 * @var	string
	 */
	protected $customerName;

	public function __construct($amount = null, $variableSymbol = null, $returnUrl = NULL)
	{
		$this->amount = $amount;
		$this->variableSymbol = $variableSymbol;
		$this->returnUrl = $returnUrl;
	}

	public function setAmount($amount)
	{
		$this->amount = $amount;
	}

	public function setVariableSymbol($variableSymbol)
	{
		$this->variableSymbol = $variableSymbol;
	}

	public function setSpecificSymbol($specificSymbol)
	{
		$this->specificSymbol = $specificSymbol;
	}
	
	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl = $returnUrl;
	}

	public function setCustomerName($customerName)
	{
		$this->customerName = $customerName;
	}
}
