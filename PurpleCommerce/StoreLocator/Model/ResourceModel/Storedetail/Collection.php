<?php


namespace PurpleCommerce\StoreLocator\Model\ResourceModel\Storedetail;

use \PurpleCommerce\StoreLocator\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     *
     * @return void
     */
    protected $_idFieldName = 'id';
    protected function _construct()
    {
        $this->_init(
            \PurpleCommerce\StoreLocator\Model\Storedetail::class,
            \PurpleCommerce\StoreLocator\Model\ResourceModel\Storedetail::class
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
