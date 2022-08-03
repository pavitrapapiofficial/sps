<?php

namespace PurpleCommerce\StoreLocator\Model\Store\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Locators implements OptionSourceInterface
{
    protected $locators;
    //Here you can __construct Model

    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Store')],
            ['value' => 2, 'label' => __('Chiropodist')]
        ];
    }
}