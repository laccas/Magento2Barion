<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace TLSoft\Barion\Gateway\Validator;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use TLSoft\Barion\Helper\Data;
class CurrencyValidator extends AbstractValidator
{
	/**
	 * @var Data
	 */
	protected $helper;

	/**
	 * @param Data $helper
	 * @param \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory
	 */
	public function __construct(
		Data $helper,
		\Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory
   ) {
		$this->helper = $dataHelper;
        parent::__construct($resultFactory);
    }

    /**
     * Validate currency
     * @param array $validationSubject 
     * @return mixed
     */
    public function validate(array $validationSubject)
    {
		$currencyCode = $validationSubject["currency"];
        if (!in_array($currencyCode, $this->getAcceptedCurrencyCodes())) {
            return $this->createResult(
                false,
                [__('Currency not accepted.')]
            );
        }else{
			return $this->createResult(
                true,
                []
            );
		}
    }

	/**
	 * Return enabled currency codes for payment
	 * @return array
	 */
	private function getAcceptedCurrencyCodes(){
		$helper = $this->helper;
		return $helper->getAllowedCurrencyCodes();
	}
}