<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace TLSoft\Barion\Gateway\Response;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use TLSoft\Barion\Helper\Data;
use Magento\Customer\Model\Session\Storage as Session;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use TLSoft\Barion\Api\TransactionRepositoryInterface;
use TLSoft\Barion\Api\Data\TransactionInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
class ResponseHandler implements HandlerInterface
{
	/**
	 * Transaction ID
	 */
	const TXN_ID = "TRID";

	/**
     * @var Data
     */
    private $helper;

	/**
	 * @var Session
	 */
	private $customerSession;

	/**
	 * Summary of __construct
	 * @param Data $helper
	 */
	public function __construct(
		Data $helper,
		Session $customerSession,
		JsonHelper $jsonHelper,
		TransactionRepositoryInterface $transactionRepository
		){

		$this->helper = $helper;
		$this->customerSession = $customerSession;
		$this->jsonHelper = $jsonHelper;
		$this->transactionRepository = $transactionRepository;
	}
    /**
	 * Handles transaction id
	 *
	 * @param array $handlingSubject
	 * @param array $response
	 * @return void
	 */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();
		$helper = $this->helper;
		$jsonHelper = $this->jsonHelper;

		$result = $jsonHelper->jsonDecode($response[0]);

		try{
			$transaction = $this->transactionRepository->get($result["Transactions"][0]["POSTransactionId"]);
			$transaction->setTransactionId($result["Transactions"][0]["TransactionId"]);
			$transaction->setTransactionInfo(array("PaymentId"=>$result["PaymentId"]));
			$this->transactionRepository->save($transaction);
		}
		catch(NoSuchentityException $e){

		}
		catch(CouldNotSaveException $e){

		}

		$url = $helper->getRedirectUrl()."?id=".$result["PaymentId"];

		$customerSession = $this->customerSession;
		$customerSession->setRedirectUrl($url);
		$customerSession->setTransactionId($result["PaymentId"]);
    }
}