<?php

namespace TLSoft\Barion\Gateway\Http;
use TLSoft\Barion\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use TLSoft\Barion\Helper\Data;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class TransferFactory implements TransferFactoryInterface
{
    /**
	 * @var TransferBuilder
	 */
    private $transferBuilder;

	/**
	 * @var Data
	 */
	private $helper;

    /**
	 * Summary of __construct
	 * @param TransferBuilder $transferBuilder
	 * @param Data $helper
	 */
    public function __construct(
        TransferBuilder $transferBuilder,
		Data $helper,
		JsonHelper $jsonHelper
    ) {
        $this->transferBuilder = $transferBuilder;
		$this->helper = $helper;
		$this->jsonHelper = $jsonHelper;
    }
    /**
	 * Builds gateway transfer object
	 *
	 * @param array $request
	 * @return TransferInterface
	 */
    public function create(array $request)
    {
		$url = "";
		$ending = "";
		$helper = $this->helper;

		$json= $this->jsonHelper->jsonEncode($request);
		$url = $helper->getStartUrl();

        return $this->transferBuilder
            ->setMethod("POST")
			->setUri($url)
			->setBody($json)
			->shouldEncode(true)
            ->build();
    }
}