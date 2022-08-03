<?php
namespace PurpleCommerce\StoreLocator\Model\ResourceModel;

class Welcome extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('locations', 'id');
    }
}
