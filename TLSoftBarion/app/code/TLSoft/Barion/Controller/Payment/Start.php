<?php
namespace TLSoft\Barion\Controller\Payment;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Customer\Model\Session\Storage as Session;

class Start extends Action
{

	/**
	 * @var Session
	 */
	private $customerSession;

	/**
	 * Summary of __construct
	 * @param Context $context
	 * @param Session $customerSession
	 */
	public function __construct(
	Context $context,
	Session $customerSession
	) {
		parent::__construct($context);
		$this->customerSession = $customerSession;
	}

	/**
	 * @return bool|ResultInterface
	 */
	public function execute()
	{
		$customerSession = $this->customerSession;
		$redirectUrl= $customerSession->getRedirectUrl();
		if ($redirectUrl) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($redirectUrl);
            return $resultRedirect;
        }
		return false;
	}
}