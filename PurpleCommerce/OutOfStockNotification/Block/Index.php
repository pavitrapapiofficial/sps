<?php
namespace PurpleCommerce\OutOfStockNotification\Block;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\AbstractProduct;

class Index extends AbstractProduct
{
    public function __construct(Context $context, array $data)
    {
        parent::__construct($context, $data);
    }
}