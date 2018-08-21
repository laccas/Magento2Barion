<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace TLSoft\Barion\Gateway\Request;

use TLSoft\Barion\Helper\Data;
use TLSoft\Barion\Model\Ui\ConfigProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use TLSoft\Barion\Api\TransactionRepositoryInterface;
use TLSoft\Barion\Api\Data\TransactionInterfaceFactory;

class InitializeRequest implements BuilderInterface
{
	/**
	 * @var Data
	 */
	private $helper;

	/**
	 * @var OrderFactory
	 */
	private $order;

	/**
	 * @var ConfigProvider
	 */
    private $configProvider;

    public function __construct(
		ConfigProvider $config,
		Data $helper,
		TransactionRepositoryInterface $transactionRepository,
		TransactionInterfaceFactory $transactionFactory
    ) {
		$this->helper = $helper;
		$this->configProvider = $config;
		$this->transactionRepository = $transactionRepository;
		$this->transactionFactory = $transactionFactory;
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


    /**
	 * Builds CIB request
	 *
	 * @param array $buildSubject
	 * @return array
	 */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

		/** @var PaymentDataObjectInterface $payment */
        $payment = $buildSubject['payment'];

		/** @var OrderAdapterInterface $order */
        $order = $payment->getOrder();
		$this->order = $order;

		$providerConfig = $this->getProviderConfig($payment);

        if (empty($providerConfig)) {
            throw new \UnexpectedValueException('Payment parameter array should be provided');
        }

		$helper = $this->helper;
		$message = array();
		$id=10;
		$storeId = $order->getStoreId();

		$message["store_id"] = $storeId;
		$message["transaction_status"] = "06";//before processing

		$products = array();

		$items = $order->getItems();

		$i = 0;
		foreach($items as $item){
			$products[$i]["Name"]=$item->getName();
			$products[$i]["Description"]=$item->getName();
			$products[$i]["Quantity"]=number_format($item->getQtyOrdered(),0);
			$products[$i]["Unit"]="db";
			$products[$i]["UnitPrice"]=$helper->formatOrderTotal($item->getPriceInclTax(),$order->getCurrencyCode());
			$products[$i]["ItemTotal"]=$helper->formatOrderTotal($item->getRowTotalInclTax(),$order->getCurrencyCode());
			$i++;
		}

		$message["POSKey"]=$helper->getPOSKey();
		$message["PaymentType"]="Immediate";
		$message["PaymentWindow"]="00:30:00";
		$message["GuestCheckout"]=true;
		$message["FundingSources"]=array("All");
		$message["PaymentRequestId"]=$order->getOrderIncrementId();
		$message["RedirectUrl"]=$helper->getUrl("barion/payment/response");
		$message["OrderNumber"]=$order->getOrderIncrementId();
		$message["Currency"]=$order->getCurrencyCode();
		$message["Locale"]=$helper->getLocaleCode($storeId);
		$message["Transactions"]=array(array(
			"POSTransactionId"=>$order->getOrderIncrementId(),
			"Payee"=>$helper->getEmail(),
			"Total"=>$helper->formatOrderTotal($order->getGrandTotalAmount(),$order->getCurrencyCode()),
			"Items"=>$products
			));

		$this->saveTransaction($message);

		unset($message["transaction_status"]);
		unset($message["store_id"]);
        return $message;
    }

	protected function saveTransaction(array $message)
	{
		$transactionRepository = $this->transactionRepository;

		$transactionFactory = $this->transactionFactory->create();
		$transactionFactory
			->setTransactionId($message['Transactions'][0]['POSTransactionId'])
			->setStoreId($message['store_id'])
			->setOrderId($message['OrderNumber'])
			->setAmount($message['Transactions'][0]['Total'])
			->setCurrencyCode($message['Currency'])
			->setLanguageCode($message['Locale'])
			->setTransactionStatus($message['transaction_status'])
			->setMethodCode("barion");

		$transactionRepository->save($transactionFactory);

	}

	/**
	 * @param PaymentDataObjectInterface $payment
	 * @return array
	 * @throws LocalizedException
	 */
    protected function getProviderConfig(PaymentDataObjectInterface $payment)
    {
        $methodCode = $payment->getPayment()->getMethodInstance()->getCode();

        return $this->configProvider->getProviderConfig($methodCode);
    }
}