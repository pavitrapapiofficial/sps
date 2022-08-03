<?php

namespace PurpleCommerce\StoreLocator\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StoredetailRepositoryInterface
{
    public function save(\PurpleCommerce\StoreLocator\Api\Data\StoredetailInterface $items);

    public function getById($id);

    public function delete(\PurpleCommerce\StoreLocator\Api\Data\StoredetailInterface $item);

    public function deleteById($id);
}
