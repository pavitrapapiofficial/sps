<?php

namespace Interprise\Logger\Observer\Price;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Updatecart
 * @package VendorName\Changeprice\Observer
 */
class Updatecart implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    /**
     * Updatecart constructor.
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Interprise\Logger\Helper\Data $helper
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_productfactory = $productFactory;
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer->getEvent()->getData('info');

        $cart = $observer->getEvent()->getData('cart');
        $price ='';

        $convert_data = (array)$data;
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        foreach ($convert_data as $itemsdata => $datainfo) {
            foreach ($datainfo as $itemId => $itemInfo) {
                $item = $this->_checkoutSession->getQuote()->getItemById($itemId);

                if (!$item) {
                    continue;
                }
                
                $qty = $item->getQty();
            
                $set_product = $this->_productfactory->create()->load($item->getProductId());
                $itemcode = $set_product->getInterpriseItemCode();
                $unitmeasurecode = $set_product->getIsUnitmeasurecode();
                $final_price = $set_product->getFinalPrice();
                $helper_class = $this->_helper->create();
                $special_pr = $helper_class->getSpecialPriceForFrontend($itemcode, '', $qty, 'GBP', $unitmeasurecode);
                // add your logic for custom price
                $special_pr = min($special_pr, $final_price);
                $item->setOriginalCustomPrice($special_pr);
                $item->setCustomPrice($special_pr);
            }
        }
    }
}
