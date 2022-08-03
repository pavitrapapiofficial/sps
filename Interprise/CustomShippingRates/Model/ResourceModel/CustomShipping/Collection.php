<?php

namespace Interprise\CustomShippingRates\Model\ResourceModel\CustomShipping;

class Collection
    extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            'Interprise\CustomShippingRates\Model\CustomShipping',
            'Interprise\CustomShippingRates\Model\ResourceModel\CustomShipping'
        );
    }
}