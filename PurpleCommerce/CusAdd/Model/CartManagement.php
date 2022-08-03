<?php 
namespace PurpleCommerce\CusAdd\Model;


 
class CartManagement {
    protected $quoteFactory;
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->quoteFactory = $quoteFactory;
    }

	/**
	 * {@inheritdoc}
	 */
	public function updateCartItem($cartId,$itemId,$price) {

        if($price){
            //$quote = $this->quoteRepository->getActive($cartId);
            $quote = $this->quoteFactory->create()->load($cartId);
            $quoteItem = $quote->getItemById($itemId);
            // echo "<pre>";
            // var_dump($quote->getAllItems());
           // foreach($quote->getAllItems() as $item) {
             //   if($item->getProductId()==$itemId){
               //     $item->setCustomPrice((int)$price);
                    // $quoteItem->setName($price);
                 //   $item->setOriginalCustomPrice($price);
                   // $item->getProduct()->setIsSuperMode(true);
               // }
                    
            // }
            // var_dump((int)$price);
            $quoteItem->setCustomPrice((int)$price);
            $quoteItem->setOriginalCustomPrice((int)$price);
            $quoteItem->getProduct()->setIsSuperMode(true);
            $quoteItem->save();
            $quote->save();
        }
        return 'done';
    }
}
