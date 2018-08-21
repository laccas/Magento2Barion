<?php
namespace TLSoft\Barion\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

interface TransactionRepositoryInterface {

	/**
	 * Save transaction.
	 * @param Data\TransactionInterface $transaction
	 * @return Data\TransactionInterface
	 * @throws CouldNotSaveException
	 */
	public function save(Data\TransactionInterface $transaction);

	/**
	 * Retrieve transaction.
	 * @param string $transaction_id
	 * @return Data\TransactionInterface
	 * @throws InputException
	 * @throws NoSuchEntityException
	 */
	public function get($transaction_id);

	/**
	 * Retrieve transactions matching the specified criteria.
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return Data\TransactionSearchResultInterface
	 */
	public function getList(SearchCriteriaInterface $searchCriteria);

}
