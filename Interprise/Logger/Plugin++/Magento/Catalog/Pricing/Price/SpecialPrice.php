<?php


namespace Interprise\Logger\Plugin\Magento\Catalog\Pricing\Price;

class SpecialPrice
{

    public function beforegetSpecialPrice(
        \Magento\Catalog\Pricing\Price\SpecialPrice $subject,
        $special_price
    ) {
         return 30;
    }
}