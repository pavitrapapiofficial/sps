<?php

namespace PurpleCommerce\StoreLocator\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StorerecordRepositoryInterface
{
    public function save(\PurpleCommerce\StoreLocator\Api\Data\StorerecordInterface $items);

    public function getById($id);

    public function delete(\PurpleCommerce\StoreLocator\Api\Data\StorerecordInterface $item);

    public function deleteById($id);
}
