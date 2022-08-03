<?php
   
    namespace Interprise\Logger\Observer\Price;
 
    use Magento\Framework\Event\ObserverInterface;
    use Magento\Framework\App\RequestInterface;
    use Magento\Catalog\Model\Product\Type;
 
class Afteraddtocart implements ObserverInterface
{
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Interprise\Logger\Helper\Data $helper
    ) {
        $this->_productfactory = $productFactory;
        $this->_helper = $helper;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
        if ($item->getProduct()->getTypeId() == Type::TYPE_BUNDLE) {
            foreach ($item->getQuote()->getAllItems() as $bundleitems) {
                /** @var $bundleitems\Magento\Quote\Model\Quote\Item */
                //Skip the bundle product
                if ($bundleitems->getProduct()->getTypeId() == Type::TYPE_BUNDLE) {
                    continue;
                }
                    $set_product = $this->_productfactory->create()->load($bundleitems->getProductId());
                    $final_price = $set_product->getFinalPrice();
                $bundleitems->setCustomPrice($final_price);
                $bundleitems->setOriginalCustomPrice($final_price);
                $bundleitems->getProduct()->setIsSuperMode(true);

            }
        } else {
            $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
            $qty = $item->getQty();
            $set_product = $this->_productfactory->create()->load($item->getProductId());
            $final_price = $set_product->getFinalPrice();
            $itemcode = $set_product->getInterpriseItemCode();
            $unitmeasurecode = $set_product->getIsUnitmeasurecode();
            $helper_class = $this->_helper->create();
            $special_pr = $helper_class->getSpecialPriceForFrontend($itemcode, '', $qty, 'GBP', $unitmeasurecode);
            $price = min($special_pr, $final_price); //set your price here
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        }
    }
}
