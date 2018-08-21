<?php

namespace TLSoft\Barion\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session\Storage as Session;
use Magento\Framework\Exception\LocalizedException;
use TLSoft\Barion\Model\Config\Source\ResultCodes;
use TLSoft\Barion\Gateway\Helper\Communication;
use Magento\Framework\App\Action\Context;

class Response extends Action
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
	Communication $helper,
	Session $customerSession
	) {
		parent::__construct($context);
		$this->customerSession = $customerSession;
		$this->helper = $helper;
	}

	public function execute(){
		$urlParams = $this->getRequest()->getParams();

        $result = $this->helper->processResponse($urlParams);

        switch ($result->getCode()) {
            case ResultCodes::RESULT_TIMEOUT:
            case ResultCodes::RESULT_ERROR:
				$this->_redirect('checkout/onepage/failure', ['_secure' => true]);
            case ResultCodes::RESULT_USER_CANCEL:
                $this->_redirect('checkout/onepage/failure', ['_secure' => true]);
                break;
            case ResultCodes::RESULT_PENDING:
				$this->_redirect('checkout/onepage/success', ['_secure' => true]);
            case ResultCodes::RESULT_SUCCESS:
                $this->_redirect('checkout/onepage/success', ['_secure' => true]);
                break;
            default:
                throw new LocalizedException(__('Missing or invalid result code.'));
        }
	}

}