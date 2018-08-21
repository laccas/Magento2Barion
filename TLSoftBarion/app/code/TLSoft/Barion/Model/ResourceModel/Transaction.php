<?php

namespace TLSoft\Barion\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use TLSoft\Barion\Model\Tri\TransactionResourceInterface;
use TLSoft\Barion\Api\Data\TransactionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\EntityManager;

class Transaction extends AbstractDb implements TransactionResourceInterface
{
	/**
	 * Model initialization.
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('tlsoft_payment_transaction','entity_id');
	}

	/**
	 * @param Context $context
	 * @param string $connectionName
	 */
	public function __construct(
		Context $context,
		$connectionName = null,
		MetadataPool $metaDataPool,
		EntityManager $entityManager
	){
		$this->metadataPool = $metaDataPool;
		$this->entityManager = $entityManager;
		parent::__construct($context,$connectionName);
	}

	public function load(AbstractModel $object, $value, $field = null) {
		$entityId = $this->getEntityId ( $object, $value, $field );
		if ($entityId) {
			$this->entityManager->load ( $object, $entityId );
		}
		return $this;
	}

	private function getEntityId(AbstractModel $object, $value, $field = null) {
		$entityMetadata = $this->metadataPool->getMetadata ( TransactionInterface::class );
		if (! is_numeric ( $value ) && $field === null) {
			$field = 'transaction_id';
		} elseif (! $field) {
			$field = $entityMetadata->getIdentifierField ();
		}
		$entityId = $value;
		if ($field != $entityMetadata->getIdentifierField () || $object->getStoreId ()) {
			$select = $this->_getLoadSelect ( $field, $value, $object );
			$select->reset ( Select::COLUMNS )->columns ( $this->getMainTable () . '.' . $entityMetadata->getIdentifierField () )->limit ( 1 );
			$result = $this->getConnection ()->fetchCol ( $select );
			$entityId = count ( $result ) ? $result [0] : false;
		}
		return $entityId;
	}


}