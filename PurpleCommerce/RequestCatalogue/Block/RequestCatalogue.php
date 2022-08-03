<?php

namespace PurpleCommerce\RequestCatalogue\Block;

use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Template\Context;

class RequestCatalogue extends Template
{

    protected $directoryBlock;
    protected $_isScopePrivate;

    public function __construct(Context $context,
        \Magento\Directory\Block\Data $directoryBlock,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->directoryBlock = $directoryBlock;
    }

    public function getFormAction()
        {
        return $this->getUrl('requestCatalogue/index/submit', ['_secure' => true]);  
    }

    public function getCountries()
    {
        $country = $this->directoryBlock->getCountryHtmlSelect();
        return $country;
    }

    public function getRegion()
    {
        $region = $this->directoryBlock->getRegionHtmlSelect();
        return $region;
    }

    public function getCountryAction()
    {
        return $this->getUrl('requestCatalogue/index/country', ['_secure' => true]);
    }

}
