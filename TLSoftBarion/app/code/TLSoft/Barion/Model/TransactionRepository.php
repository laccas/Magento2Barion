<?php
namespace TLSoft\Barion\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use TLSoft\Barion\Api\Data\TransactionSearchResultInterfaceFactory as SearchResultFactory;
use TLSoft\Barion\Model\ResourceModel\Transaction as Resource;
use TLSoft\Barion\Api\Data;
use TLSoft\Barion\Api\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{

	/**
	 * @var Resource
	 */
	private $resource;

	/**
	 * @var StoreManagerInterface
	 */
	private $storeManager;

	/**
	 * @var TransactionFactory
	 */
	private $transactionFactory;

	/**
	 * @var TransactionInterface[]
	 */
	private $registry = [];

	/**
	 * @var SearchResultFactory
	 */
	private $searchResultFactory=null;

	/**
	 * @var CollectionProcessorInterface
	 */
	private $collectionProcessor;

	/**
	 * Summary of __construct
	 * @param StoreManagerInterface $storeManager
	 * @param Resource $resource
	 * @param TransactionFactory $transactionFactory
	 * @param SearchResultFactory $searchResultFactory
	 * @param CollectionProcessorInterface $collectionProcessor
	 */
	public function __construct(
		StoreManagerInterface $storeManager,
		Resource $resource,
		TransactionFactory $transactionFactory,
		SearchResultFactory $searchResultFactory,
		CollectionProcessorInterface $collectionProcessor
	){
		$this->resource = $resource;
		$this->storeManager = $storeManager;
		$this->transactionFactory = $transactionFactory;
		$this->searchResultFactory = $searchResultFactory;
		$this->collectionProcessor = $collectionProcessor;
	}

	/**
	 * Save transaction.
	 * @param Data\TransactionInterface $transaction
	 * @throws CouldNotSaveException
	 * @return Data\TransactionInterface
	 */
	public function save(Data\TransactionInterface $transaction)
	{
		if(empty($transaction->getStoreId())){
			$transaction->setStoreId($this->storeManager->getStore()->getId());
		}

		try{
			$this->resource->save($transaction);
		}
		catch(\Exception $exception){
			throw new CouldNotSaveException (__($exception->getMessage()));
		}

		return $transaction;
	}

	/**
	 * Retrieve transaction.
	 * @param string $transaction_id
	 * @return Data\TransactionInterface
	 * @throws InputException
	 * @throws NoSuchEntityException
	 */
	public function get($transaction_id)
	{
		if(!$transaction_id){
			throw new InputException(__('Id and payment method required.'));
		}

		if(!isset($this->registry[$transaction_id])){
			$transaction = $this->transactionFactory->create();
			$this->resource->load($transaction,$transaction_id);
			if(!$transaction->getId()){
				throw new NoSuchEntityException (__('Your transaction with transaction id "%1" does not exist.', $transaction_id));
			}else{
				$this->registry[$transaction_id]=$transaction;
			}
		}

		return $this->registry[$transaction_id];
	}

	/**
	 * Find entities by criteria.
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return Data\TransactionSearchResultInterface
	 */
	public function getList(SearchCriteriaInterface $searchCriteria)
	{
		/** @var \Magento\Sales\Api\Data\TransactionSearchResultInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
	}

}