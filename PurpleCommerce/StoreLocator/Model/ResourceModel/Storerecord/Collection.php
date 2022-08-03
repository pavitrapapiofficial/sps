<?php


namespace PurpleCommerce\StoreLocator\Model\ResourceModel\Storerecord;

use \PurpleCommerce\StoreLocator\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \PurpleCommerce\StoreLocator\Model\Storerecord::class,
            \PurpleCommerce\StoreLocator\Model\ResourceModel\Storerecord::class
        );
    }
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
}
