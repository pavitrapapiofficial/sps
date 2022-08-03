<?php

namespace Meetanshi\VatExempt\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;
use Meetanshi\VatExempt\Helper\Data;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product;

class CheckOutCartObserver implements ObserverInterface
{
    protected $customerSession;
    protected $helper;
    protected $messageManager;
    private $responseFactory;
    private $url;
    protected $cart;
    protected $request;
    protected $product;

    public function __construct(
        Session $session,
        ResponseFactory $responseFactory,
        ManagerInterface $messageManager,
        UrlInterface $url,
        Data $helper,
        Cart $cart,
        RequestInterface $request,
        Product $product
    )
    {
        $this->customerSession = $session;
        $this->responseFactory = $responseFactory;
        $this->messageManager = $messageManager;
        $this->url = $url;
        $this->helper = $helper;
        $this->cart = $cart;
        $this->request = $request;
        $this->product = $product;
    }

    public function execute(Observer $observer)
    {
        $postValues = $this->request->getPostValue();
        $cartItemsCount = $this->cart->getQuote()->getItemsCount();
        $isVatExemptProductInCart = 0;

        $cartItemsAll = $this->cart->getQuote()->getAllItems();
        foreach ($cartItemsAll as $item) {
            $product = $this->product->load($item->getProductId());
            if ($product->getIsVatexempt()) {
                $isVatExemptProductInCart = 1;
            }
        }
        if ($cartItemsCount > 0) {
            if ($this->helper->isEnabled() && $isVatExemptProductInCart == 1) {

                if ($this->helper->getRequireLogin()) {
                    
                    if (!$this->customerSession->isLoggedIn()) {
                        $event = $observer->getEvent();
                        $RedirectUrl = $this->url->getUrl('customer/account/login');
                        $this->messageManager->addNoticeMessage(__('Please Login to Place Your Order.'));
                        $this->responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();
                        return $this;
                    }
                }
            }
        }
        
        return $this;
    }
}
