<?php
namespace TLSoft\Barion\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use TLSoft\Barion\Api\Data\TransactionInterface;
use TLSoft\Barion\Model\ResourceModel\Transaction as ResourceTransaction;

class Transaction extends AbstractModel implements TransactionInterface
{
	/**
	 * @var string
	 */
    protected $_eventPrefix = 'tlsoft_transaction';

    /**
	 * @var string
	 */
    protected $_eventObject = 'transaction';

	/**
	 * @var JsonHelper
	 */
	protected $jsonHelper;

	/**
	 * Summary of __construct
	 * @param Context $context 
	 * @param Registry $registry 
	 * @param mixed $resource 
	 * @param mixed $resourceCollection 
	 * @param array $data 
	 * @param JsonHelper $jsonHelper 
	 */
	public function __construct(
		Context $context,
		Registry $registry,
		$resource = null,
		$resourceCollection = null,
		JsonHelper $jsonHelper,
		array $data = []
	){
		$this->jsonHelper = $jsonHelper;
		parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
	}

	/**
	 * Initialize resource model.
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init(ResourceTransaction::class);
	}

	/**
	 * Get entity_id
	 * @return int|null
	 */
	public function getId()
	{
		return $this->getData(TransactionInterface::ENTITY_ID);
	}

	/**
	 * Get transaction id
	 * @return string
	 */
	public function getTransactionId()
	{
		return $this->getData(TransactionInterface::TRANSACTION_ID);
	}

	/**
	 * Get store id
	 * @return int
	 */
	public function getStoreId()
	{
		return $this->getData(TransactionInterface::STORE_ID);
	}

	/**
	 * Get order entity_id
	 * @return int
	 */
	public function getOrderId()
	{
		return $this->getData(TransactionInterface::ORDER_ID);
	}

	/**
	 * Get transaction amount
	 * @return float
	 */
	public function getAmount()
	{
		return $this->getData(TransactionInterface::AMOUNT);
	}

	/**
	 * Get transaction currency code
	 * @return string
	 */
	public function getCurrencyCode()
	{
		return $this->getData(TransactionInterface::CURRENCY_CODE);
	}

	/**
	 * Get transaction language code
	 * @return string
	 */
	public function getLanguageCode()
	{
		return $this->getData(TransactionInterface::LANGUAGE_CODE);
	}

	/**
	 * Get transaction status
	 * @return int
	 */
	public function getStatus()
	{
		return $this->getData(TransactionInterface::STATUS);
	}

	/**
	 * Get transaction payment method code
	 * @return string
	 */
	public function getMethodCode()
	{
		return $this->getData(TransactionInterface::METHOD_CODE);
	}

	/**
	 * Get other transaction informations
	 * @return array|null
	 */
	public function getTransactionInfo()
	{
		$data = $this->getData(TransactionInterface::TRANSACTION_INFO);
		if(!empty($data)){
			$data = $this->jsonHelper->jsonDecode($data);
		}
		return $data;
	}

	/**
	 * Get created at timestamp for transaction
	 * @return string|null
	 */
	public function getCreatedAt()
	{
		return $this->getData(TransactionInterface::CREATED_AT);
	}

	/**
	 * Get updated at timestamp for transaction
	 * @return string|null
	 */
	public function getUpdatedAt()
	{
		return $this->getData(TransactionInterface::UPDATED_AT);
	}

	/**
	 * Set the id for the transaction
	 * @param string $transactionId
	 * @return $this
	 */
	public function setTransactionId($transactionId)
	{
		return $this->setData(TransactionInterface::TRANSACTION_ID,$transactionId);
	}

	/**
	 * Set the store id for the transaction
	 * @param int $storeId
	 * @return $this
	 */
	public function setStoreId($storeId)
	{
		return $this->setData(TransactionInterface::STORE_ID,$storeId);
	}

	/**
	 * Set the order id for the transaction
	 * @param int $orderId
	 * @return $this
	 */
	public function setOrderId($orderId)
	{
		return $this->setData(TransactionInterface::ORDER_ID,$orderId);
	}

	/**
	 * Set the order amount for the transaction
	 * @param float $amount
	 * @return $this
	 */
	public function setAmount($amount)
	{
		return $this->setData(TransactionInterface::AMOUNT,$amount);
	}

	/**
	 * Set the currency code for the transaction
	 * @param string $currencyCode
	 * @return $this
	 */
	public function setCurrencyCode($currencyCode)
	{
		return $this->setData(TransactionInterface::CURRENCY_CODE,$currencyCode);
	}

	/**
	 * Set the language code for the transaction
	 * @param string $languageCode
	 * @return $this
	 */
	public function setLanguageCode($languageCode)
	{
		return $this->setData(TransactionInterface::LANGUAGE_CODE,$languageCode);
	}

	/**
	 * Set the status code for transaction
	 * @param int $status
	 * @return $this
	 */
	public function setTransactionStatus($status)
	{
		return $this->setData(TransactionInterface::STATUS,$status);
	}

	/**
	 * Set the payment method code for transaction
	 * @param string $methodCode
	 * @return $this
	 */
	public function setMethodCode($methodCode)
	{
		return $this->setData(TransactionInterface::METHOD_CODE,$methodCode);
	}

	/**
	 * Set other informations for the transaction
	 * @param array $transactionInfo
	 * @return $this
	 */
	public function setTransactionInfo($transactionInfo = [])
	{
		$data = $this->jsonHelper->jsonEncode($transactionInfo);
		return $this->setData(TransactionInterface::TRANSACTION_INFO,$data);
	}

	/**
	 * Set the created at timestamp for the transaction
	 * @param string $timestamp
	 * @return $this
	 */
	public function setCreatedAt($timestamp)
	{
		return $this->setData(TransactionInterface::CREATED_AT,$timestamp);
	}

	/**
	 * Set the updated at timestamp for the transaction
	 * @param string $timestamp
	 * @return $this
	 */
	public function setUpdatedAt($timestamp)
	{
		return $this->setData(TransactionInterface::UPDATED_AT,$timestamp);
	}
}