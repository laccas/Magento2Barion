<?php
namespace TLSoft\Barion\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface TransactionSearchResultInterface extends SearchResultsInterface
{
	/**
	 * Get items.
	 *
	 * @return TransactionInterface[] Array of collection items.
	 */
    public function getItems();

    /**
	 * Set items.
	 *
	 * @param TransactionInterface[] $items
	 * @return $this
	 */
    public function setItems(array $items = null);
}