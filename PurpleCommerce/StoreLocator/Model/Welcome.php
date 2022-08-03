<?php
namespace PurpleCommerce\StoreLocator\Model;

class Welcome extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init('PurpleCommerce\StoreLocator\Model\ResourceModel\Welcome');
    }
}
