<?php
namespace TLSoft\Barion\Model\ResourceModel\Transaction;

use TLSoft\Barion\Api\Data\TransactionSearchResultInterface;
use TLSoft\Barion\Model;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;

class Collection extends AbstractCollection implements TransactionSearchResultInterface
{
	/**
	 * @var string
	 */
    protected $_idFieldName = 'entity_id';

    /**
	 * Event prefix
	 *
	 * @var string
	 */
    protected $_eventPrefix = 'tlsoft_transaction_collection';

    /**
	 * Event object
	 *
	 * @var string
	 */
    protected $_eventObject = 'transaction_collection';

    /**
	 * @var \Magento\Framework\DB\Helper
	 */
    protected $_coreResourceHelper;

    /**
	 * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
	 * @param \Magento\Framework\Event\ManagerInterface $eventManager
	 * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
	 * @param \Magento\Framework\DB\Helper $coreResourceHelper
	 * @param string|null $connection
	 * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
	 */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DB\Helper $coreResourceHelper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $connection,
            $resource
        );
        $this->_coreResourceHelper = $coreResourceHelper;
    }

    /**
	 * Model initialization
	 *
	 * @return void
	 */
    protected function _construct()
    {
        $this->_init(Model\Transaction::class, Model\ResourceModel\Transaction::class);
    }
}