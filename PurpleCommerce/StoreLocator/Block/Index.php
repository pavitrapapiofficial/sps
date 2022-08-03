<?php
namespace PurpleCommerce\StoreLocator\Block;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
         * @var \PurpleCommerce\StoreLocator\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
    * @param \PurpleCommerce\StoreLocator\Helper\Data $dataHelper
    * @param array $data
    */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \PurpleCommerce\StoreLocator\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    public function canShowBlock()
    {
        return $this->_dataHelper->isModuleEnabled();
    }

    public function getLocatorData($path){
        return $this->_dataHelper->getconfig($path);
    }
    public function getstaticdata()
    {
      $arr=$this->getBaseUrl();
      return $arr.'/pub/test.txt';
    }
}
