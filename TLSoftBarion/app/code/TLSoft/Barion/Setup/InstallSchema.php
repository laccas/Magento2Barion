<?php

namespace TLSoft\Barion\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\DB\Adapter\AdapterInterface;
use \Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

	/**
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup ();
		$tablename = "tlsoft_payment_transaction";
		if (! $installer->tableExists ( $tablename )) {
			$table = $installer->getConnection ()->newTable (
				$installer->getTable ( $tablename )
			)->addColumn (
				"entity_id",
				Table::TYPE_INTEGER,
				null,
				['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true],
				"Transaction Entity Id"
			)->addColumn (
				'transaction_id',
				Table::TYPE_TEXT,
				255,
				['nullable' => false],
				'Transaction Id from payment provider'
			)->addColumn (
				'store_id',
				Table::TYPE_SMALLINT,
				null,
				['unsigned' => true],
				'Store Id'
			)->addColumn(
				'order_id',
				Table::TYPE_TEXT,
				32,
				['nullable' => false],
				'Transaction Order Id'
			)->addColumn(
				'amount',
				Table::TYPE_DECIMAL,
				'12,4',
				['nullable' => false, 'default' => '0.0000'],
				'Transaction amount'
			)->addColumn(
				'currency_code',
				Table::TYPE_TEXT,
				3,
				[],
				'Transaction currency code'
			)->addColumn(
				'language_code',
				Table::TYPE_TEXT,
				2,
				[],
				'Transaction currency code'
			)->addColumn(
				'status',
				Table::TYPE_SMALLINT,
				null,
				[],
				'Transaction status'
			)->addColumn(
				'method_code',
				Table::TYPE_TEXT,
				255,
				['nullable' => false],
				'Transaction payment method code'
			)->addColumn(
				'transaction_info',
				Table::TYPE_TEXT,
				null,
				[],
				'Additional transaction info'
			)->addColumn(
				'created_at',
				Table::TYPE_TIMESTAMP,
				null,
				[],
				'Transaction Created At'
			)->addColumn(
				'updated_at',
				Table::TYPE_TIMESTAMP,
				null,
				[],
				'Transaction Updated At'
			)->addIndex (
				$setup->getIdxName ( $tablename, ['transaction_id'] ),
				['transaction_id']
			)->addIndex(
				$setup->getIdxName ( $tablename, ['store_id'] ),
				['store_id']
			)->addIndex(
				$setup->getIdxName ( $tablename, ['order_id'] ),
				['order_id']
			)->addIndex(
				$setup->getIdxName ( $tablename, ['amount'] ),
				['amount']
			)->addIndex(
				$setup->getIdxName ( $tablename, ['status'] ),
				['status']
			)->addIndex(
				$setup->getIdxName ( $tablename, ['created_at'] ),
				['created_at']
			)->addIndex(
				$setup->getIdxName ( $tablename, ['updated_at'] ),
				['updated_at']
			)->addIndex (
				$setup->getIdxName (
					$tablename,
					['method_code'],
					AdapterInterface::INDEX_TYPE_FULLTEXT
				),
				['method_code'],
				['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
			)->addIndex(
				$installer->getIdxName(
					$tablename,
					['transaction_id', 'store_id', 'method_code'],
					AdapterInterface::INDEX_TYPE_UNIQUE
				),
				['transaction_id', 'store_id', 'method_code'],
				['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
			)->addForeignKey(
				$installer->getFkName($tablename, 'store_id', 'store', 'store_id'),
				'store_id',
				$installer->getTable('store'),
				'store_id',
				Table::ACTION_SET_NULL
			)->setComment (
				'TLSoft Payment Transactions'
			);

			$installer->getConnection ()->createTable ( $table );
		}
	}
}

?>