<?php
/**
 * Copyright � 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace TLSoft\Barion\Gateway\Validator;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use TLSoft\Barion\Helper\Data;
use Magento\Framework\Json\Helper\Data as JsonHelper;
class ResponseCodeValidator extends AbstractValidator
{
	/**
	 * Resutl code;
	 */
	const RESULT_CODE = "PaymentId";

	/**
	 * @var Data
	 */
    private $helper;

	/**
	 * @var ResultInterfaceFactory
	 */
    private $resultInterfaceFactory;

	/**
	 * Summary of __construct
	 * @param Data $helper
	 */
	public function __construct(
		Data $helper,
		ResultInterfaceFactory $resultFactory,
		JsonHelper $jsonHelper
		){
		$this->helper = $helper;
		$this->jsonHelper = $jsonHelper;
		parent::__construct($resultFactory);
	}

    /**
	 * Performs validation of result code
	 *
	 * @param array $validationSubject
	 * @return ResultInterface
	 */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['response']) || !is_array($validationSubject['response'])) {
            throw new \InvalidArgumentException('Response does not exist');
        }
        $response = $validationSubject['response'];
        if ($this->isSuccessfulTransaction($response)) {
            return $this->createResult(
                true,
                []
            );
        } else {
            return $this->createResult(
                false,
                [__('Gateway rejected the transaction.')]
            );
        }
    }

    /**
	 * @param array $response
	 * @return bool
	 */
    private function isSuccessfulTransaction(array $response)
    {
		$helper = $this->jsonHelper;

		$result = $helper->jsonDecode($response[0]);
		if(array_key_exists(self::RESULT_CODE,$result)){
			return true;
		}else{
			foreach($result["Errors"] as $error){
				/*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$logger = $objectManager->get('Psr\Log\LoggerInterface');
				$logger->debug(var_export($response,true));*/
			}
		}

        return false;
    }
}