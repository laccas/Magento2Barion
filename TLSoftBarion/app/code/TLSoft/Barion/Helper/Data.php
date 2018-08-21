<?php

namespace TLSoft\Barion\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends AbstractHelper{

	/**
	 * @var Resolver
	 */
	private $localeResolver;

	/**
	 * @var TimezoneInterface
	 */
	private $timezoneFactory;

	/**
	 * @var UrlInterface
	 */
	private $urlFactory;

	/**
	 * @var $directoryList
	 */
	private $directoryList;

	/**
	 * __construct
	 * @param Context $context
	 * @param StoreRepositoryInterface $store
	 * @param TimezoneInterface $timezoneFactory
	 * @param UrlInterface $urlInterface
	 */
	public function __construct(
		Context $context,
		Resolver $localeResolver,
		TimezoneInterface $timezoneFactory,
		UrlInterface $urlInterface,
		DirectoryList $directoryList
		)
	{
		parent::__construct($context);
		$this->localeResolver = $localeResolver;
		$this->timezoneFactory = $timezoneFactory;
		$this->urlFactory = $urlInterface;
		$this->directoryList = $directoryList;
	}

	/**
	 * Get allowed currencies
	 *
	 * @return array
	 */
    public function getAllowedCurrencyCodes()
    {
        return explode(",",$this->getConfig("payment/barion/allowedcurrency"));
    }

	/**
	 * Summary of getConfig
	 * @param string $path
	 * @return boolean|string
	 */
	protected function getConfig(string $path){
		if($path){
			$value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
			return $value;
		}

		return false;
	}

	/**
	 * Transform internal locale code to CIB locale code.
	 * @param int $storeId
	 * @return string
	 */
	public function getLocaleCode(int $storeId){

		$localecode=$this->localeResolver->getLocale();

		$ciblocale=substr($localecode,3);
		$enabledlocales = explode(",",$this->getConfig("payment/barion/enabledlocales"));
		$endciblocale="";
		if ($ciblocale == "US" || $ciblocale == "GB"){
			$ciblocale="EN";
		}
		foreach ($enabledlocales as $enabledlocale){
			if ($enabledlocale == $ciblocale){
				$endciblocale=$ciblocale;
			}
		}
		if (empty($endciblocale)){
			$endciblocale = "EN";
		}

		if($endciblocale=="EN")
		{
			$endciblocale = "en-US";
		}else{
			$endciblocale = strtolower($endciblocale)."-".$endciblocale;
		}

		return $endciblocale;
	}

	/**
	 * Get current timecode for CIB transactions
	 * @return string
	 */
	public function getTimecode(){
		return $this->timezoneFactory->date()->format('YmdHis');
	}

	/**
	 * Format order total to CIB's requirements.
	 * @param number $total
	 * @param string $currency
	 * @return string
	 */
	public function formatOrderTotal($total,$currency){

		if ($currency=='HUF'){
			$total = number_format($total, 0, '','');
		}
		else{
			$total = number_format($total, 2, '.','');
		}

		return $total;
	}

	/**
	 * get url from url path
	 * @param string $path
	 * @return string
	 */
	public function getUrl(string $path){
		return $this->urlFactory->getUrl($path);
	}

	/**
	 * Get Barion Start url
	 * @return \boolean|string
	 */
	public function getStartUrl(){
		$test_mode = $this->getConfig("payment/barion/test_mode");

		$url = false;

		if($test_mode == 1){
			$url = $this->getConfig("payment/barion/start_url_test");
		}
		else{
			$url = $this->getConfig("payment/barion/start_url");
		}

		if(empty($url)){
			$url = false;
		}
		return $url;
	}

	/**
	 * Get CIB Customer redirect url
	 * @return boolean|string
	 */
	public function getRedirectUrl(){
		$test_mode = $this->getConfig("payment/barion/test_mode");

		$url = false;

		if($test_mode == 1){
			$url = $this->getConfig("payment/barion/redirect_url_test");
		}
		else{
			$url = $this->getConfig("payment/barion/redirect_url");
		}

		if(empty($url)){
			$url = false;
		}
		return $url;
	}

	/**
	 * Get CIB Customer redirect url
	 * @return boolean|string
	 */
	public function getStateUrl(){
		$test_mode = $this->getConfig("payment/barion/test_mode");

		$url = false;

		if($test_mode == 1){
			$url = $this->getConfig("payment/barion/state_url_test");
		}
		else{
			$url = $this->getConfig("payment/barion/state_url");
		}

		if(empty($url)){
			$url = false;
		}
		return $url;
	}

	public function getPOSKey()
	{
		return $this->getConfig("payment/barion/poskey");
	}

	public function getEmail()
	{
		return $this->getConfig("payment/barion/email");
	}

}