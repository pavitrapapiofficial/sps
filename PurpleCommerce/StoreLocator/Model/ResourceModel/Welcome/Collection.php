<?php
namespace PurpleCommerce\StoreLocator\Model\ResourceModel\Welcome;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('PurpleCommerce\StoreLocator\Model\Welcome','PurpleCommerce\StoreLocator\Model\ResourceModel\Welcome');
    }
}
