<?php
namespace Interprise\Logger\Model\Catalog\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Exception;

class SpecialPrice2 extends \Magento\Catalog\Pricing\Price\SpecialPrice
{
    public $_datahelper;
    public $_session;
    public function __construct(
        \Interprise\Logger\Helper\Data $datahelper,
        \Magento\Customer\Model\Session $session
    ) {

         $this->_datahelper       = $datahelper;
         $this->_session          = $session;
    }
    public function getSpecialPrice()
    {
        $specialPrice = $this->product->getSpecialPrice();
        if ($specialPrice !== null && $specialPrice !== false && !$this->isPercentageDiscount()) {
            $specialPrice = $this->priceCurrency->convertAndRound($specialPrice);
        }
        $interprise_item_code = $this->product->getInterpriseItemCode();
        $this->_datahelper = $this->_datahelper->create();
        $this->_session = $this->_session->create();
        if ($this->_session->isLoggedIn()) {
            $customer_code =  $this->_session->getData('interprise_customer_code');
            $calculate_special_price = $this->_datahelper->getSpecialCustomerProductPrice(
                $interprise_item_code,
                $customer_code,
                $qty = 1,
                $currency = 'GBP'
            );
            if ($calculate_special_price) {
                return $calculate_special_price;
            }
        }
        return 0;
    }
}
