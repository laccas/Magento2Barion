<?php
namespace TLSoft\Barion\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session\Storage as CustomerSession;

/**
 * AddOrderIdToSession
 *
 */
class AddOrderIdToSession implements ObserverInterface
{
    /**
	 * Customer Session
	 *
	 * @var CustomerSession
	 */
    private $customerSession;

    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
	 * Execute
	 *
	 * @param Observer $observer Observer
	 *
	 * @return void
	 */
    public function execute(Observer $observer)
    {
        $customerSession = $this->customerSession;
        $order = $observer->getEvent()->getOrder();
        $customerSession->setOrderIncrementId($order->getIncrementId());
    }
}
