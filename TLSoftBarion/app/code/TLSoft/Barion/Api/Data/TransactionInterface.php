<?php
namespace TLSoft\Barion\Api\Data;

interface TransactionInterface {

	/**
	 * Entity id.
	 */
	const ENTITY_ID = "entity_id";

	/**
	 * Transaction id.
	 */
	const TRANSACTION_ID = "transaction_id";

	/**
	 * Store id.
	 */
	const STORE_ID = "store_id";

	/**
	 * Order id.
	 */
	const ORDER_ID = "order_id";

	/**
	 * Amount.
	 */
	const AMOUNT = "amount";

	/**
	 * Currency code.
	 */
	const CURRENCY_CODE = "currency_code";

	/**
	 * Language code.
	 */
	const LANGUAGE_CODE = "language_code";

	/**
	 * Status.
	 */
	const STATUS = "status";

	/**
	 * Method code.
	 */
	const METHOD_CODE = "method_code";

	/**
	 * Transaction info.
	 */
	const TRANSACTION_INFO = "transaction_info";

	/**
	 * Created at.
	 */
	const CREATED_AT = "created_at";

	/**
	 * Updated at.
	 */
	const UPDATED_AT = "updated_at";
	/**
	 * Get entity_id
	 * @return int|null
	 */
	public function getId();

	/**
	 * Get transaction id
	 * @return string
	 */
	public function getTransactionId();

	/**
	 * Get store id
	 * @return int
	 */
	public function getStoreId();

	/**
	 * Get order entity_id
	 * @return int
	 */
	public function getOrderId();

	/**
	 * Get transaction amount
	 * @return float
	 */
	public function getAmount();

	/**
	 * Get transaction currency code
	 * @return string
	 */
	public function getCurrencyCode();

	/**
	 * Get transaction language code
	 * @return string
	 */
	public function getLanguageCode();

	/**
	 * Get transaction status
	 * @return int
	 */
	public function getStatus();

	/**
	 * Get transaction payment method code
	 * @return string
	 */
	public function getMethodCode();

	/**
	 * Get other transaction informations
	 * @return array|null
	 */
	public function getTransactionInfo();

	/**
	 * Get created at timestamp for transaction
	 * @return string|null
	 */
	public function getCreatedAt();

	/**
	 * Get updated at timestamp for transaction
	 * @return string|null
	 */
	public function getUpdatedAt();

	/**
	 * Set the id for the transaction
	 * @param string $transactionId
	 * @return $this
	 */
	public function setTransactionId($transactionId);

	/**
	 * Set the store id for the transaction
	 * @param int $storeId
	 * @return $this
	 */
	public function setStoreId($storeId);

	/**
	 * Set the order id for the transaction
	 * @param int $orderId
	 * @return $this
	 */
	public function setOrderId($orderId);

	/**
	 * Set the order amount for the transaction
	 * @param float $amount
	 * @return $this
	 */
	public function setAmount($amount);

	/**
	 * Set the currency code for the transaction
	 * @param string $currencyCode
	 * @return $this
	 */
	public function setCurrencyCode($currencyCode);

	/**
	 * Set the language code for the transaction
	 * @param string $languageCode
	 * @return $this
	 */
	public function setLanguageCode($languageCode);

	/**
	 * Set the status code for transaction
	 * @param int $status
	 * @return $this
	 */
	public function setTransactionStatus($status);

	/**
	 * Set the payment method code for transaction
	 * @param string $methodCode
	 * @return $this
	 */
	public function setMethodCode($methodCode);

	/**
	 * Set other informations for the transaction
	 * @param array $transactionInfo
	 * @return $this
	 */
	public function setTransactionInfo($transactionInfo);

	/**
	 * Set the created at timestamp for the transaction
	 * @param string $timestamp
	 * @return $this
	 */
	public function setCreatedAt($timestamp);

	/**
	 * Set the updated at timestamp for the transaction
	 * @param string $timestamp
	 * @return $this
	 */
	public function setUpdatedAt($timestamp);
}
