<?php

namespace TLSoft\Barion\Gateway\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use TLSoft\Barion\Helper\Data;
use Magento\Customer\Model\Session\Storage as Session;
use TLSoft\Barion\Model\Config\Source\ResultCodes;
use TLSoft\Barion\Api\TransactionRepositoryInterface;
use TLSoft\Barion\Api\Data\TransactionInterfaceFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use TLSoft\Barion\Model\Ui\ConfigProvider;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
class Communication extends AbstractHelper
{
	/**
	 * @var DataHelper
	 */
	private $dataHelper;

	/**
	 * @var Session
	 */
	private $customerSession;

	/**
	 * @var string
	 */
	private $responseCode;


	public function __construct(
		Context $context,
		Data $helper,
		Session $customerSession,
		TransactionRepositoryInterface $transactionRepository,
		ManagerInterface $messageManager,
		OrderInterface $orderRepository,
		ConfigProvider $configProvider,
		OrderManagementInterface $orderManagement,
		InvoiceService $invoiceService,
		JsonHelper $jsonHelper,
		TransactionFactory $transactionFactory,
		BuilderInterface $transactionBuilder
		){
		parent::__construct($context);
		$this->helper = $helper;
		$this->customerSession = $customerSession;
		$this->transactionRepository = $transactionRepository;
		$this->messageManager = $messageManager;
		$this->orderRepository = $orderRepository;
		$this->configProvider = $configProvider;
		$this->orderManagement = $orderManagement;
		$this->invoiceService = $invoiceService;
		$this->jsonHelper = $jsonHelper;
		$this->transactionFactory = $transactionFactory;
		$this->transactionBuilder = $transactionBuilder;
	}

	/**
	 * Get response code from the Gateway
	 * @param array $params
	 * @return string
	 */
	public function processResponse($params = array())
	{
		$jsonHelper = $this->jsonHelper;
		$session = $this->customerSession;
		$transactionId = $session->getTransactionId();
		$helper = $this->helper;
		$methodCode = "barion";
		$orderRepository = $this->orderRepository;
		$transactionRepository = $this->transactionRepository;
		$result = "";

		if(array_key_exists("paymentId",$params)){
			$transactionId = $params["paymentId"];
		}

		$params = "?POSKey=".$helper->getPOSKey()."&PaymentId=".$transactionId;
		$response = $this->cURL($helper->getStateUrl().$params);

		$this->responseCode = ResultCodes::RESULT_PENDING;

		if ($response!=false)
		{
			$result = $jsonHelper->jsonDecode($response);
			if(count($result["Errors"]<1)){
				try{
					$transaction = $transactionRepository->get($result["Transactions"][0]["TransactionId"]);
				}
				catch(NoSuchEntityException $e){
					return $this;
				}

				$transaction->setTransactionInfo($result);
				try
				{
					$transactionRepository->save($transaction);
				}
				catch(CouldNotSaveException $e)
				{
					return $this;
				}

				$this->order = $orderRepository->loadByIncrementId($transaction->getOrderId());
				$orderManagement = $this->orderManagement;

				if ($result['Status']=="Succeeded"){
					$resulttext= __('Authorization number').": ".$transaction->getTransactionId();
					$this->responseCode = ResultCodes::RESULT_SUCCESS;
					$this->messageManager->addSuccess($resulttext);
					if($this->order->canInvoice()){
						$payment = $this->order->getPayment();
						$payment->setIsTransactionClosed(1);

						$orderTransactionId = $transaction->getTransactionId();
						$payment->setParentTransactionId($this->order->getId());
						$payment->setIsTransactionPending(false);
						$payment->setIsTransactionApproved(true);


						$this->transactionBuilder->setPayment($payment)
							->setOrder($this->order)
							->setTransactionId($orderTransactionId)
							->build(Transaction::TYPE_CAPTURE);
						//$payment->addTransactionCommentsToOrder($transaction);
						$invoice = $this->invoiceService->prepareInvoice($this->order);
						$invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
						$invoice->setTransactionId($orderTransactionId);
						$invoice->register()->pay();
						$invoice->getOrder()->setIsInProcess(true);
						$invoice->getOrder()->setCustomerNoteNotify(false);
						$transactionSave = $this->transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
						$transactionSave->save();
					}
				}elseif($result['Status']=="CANCELED"){//returned by user - cancel transaction
					$this->responseCode = ResultCodes::RESULT_USER_CANCEL;
					//$this->messageManager->addError($resulttext);
					$orderManagement->cancel($this->order->getId());
				}elseif($result['Status']=="Prepared"||$result['Status']=="Started"){
					$this->responseCode = ResultCodes::RESULT_PENDING;
				}else{
					$this->responseCode = ResultCodes::RESULT_ERROR;
					$orderManagement->cancel($this->order->getId());
					//$this->messageManager->addError($resulttext);
				}
			}else{
				$this->responseCode = ResultCodes::RESULT_PENDING;
				$this->messageManager->addError($result["Errors"][0]["Description"]."-".$result["Errors"][0]["ErrorCode"]);
			};
		}else{
			$orderManagement->cancel($this->order->getId());
			$this->responseCode = ResultCodes::RESULT_ERROR;
		}

		return $this;
	}

	protected function cURL($url)
	{
		$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER         => 0,    // don't return headers
			CURLOPT_FOLLOWLOCATION => 0,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			CURLOPT_USERAGENT      => "spider", // who am i
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 30,      // timeout on connect
			CURLOPT_TIMEOUT        => 30,      // timeout on response
			CURLOPT_MAXREDIRS      => 5,       // stop after 10 redirects
			CURLOPT_SSL_VERIFYPEER => false,
			CURLINFO_HEADER_OUT    => 1,
			CURLOPT_SSL_VERIFYHOST => false
		);
		$ch = curl_init($url);
		curl_setopt_array($ch,$options);
		$retValue = curl_exec($ch);
		if($retValue === FALSE){
			$error=curl_error($ch);
			curl_close($ch);
			return false;
		}
		else{
			curl_close($ch);
			return $retValue;
		}
	}

	/**
	 * Transaction response code.
	 * @return string
	 */
	public function getCode(){
		return $this->responseCode;
	}

	protected function getProviderConfig(string $payment)
	{
        return $this->configProvider->getProviderConfig($payment);
    }

	protected function getPid(array $config)
	{
		$currency = $this->order->getCurrencyCode();
		if($currency=="HUF"){
			return $this->getConfig("pid_huf",$config);
		}else{
			return $this->getConfig("pid_eur",$config);
		}
	}

	/**
	 * Summary of getConfig
	 * @param string $path
	 * @return boolean|string
	 */
	protected function getConfig(string $path, array $config)
	{
		if($path){
			$value = $config[$path];
			return $value;
		}

		return false;
	}


}