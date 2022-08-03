<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Block;

use Magento\Checkout\Block\Cart as CoreCart;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Checkout\Helper\Cart as HelperCart;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Catalog\Model\ProductFactory;
use Meetanshi\VatExempt\Helper\Data;
use Meetanshi\VatExempt\Model\ResourceModel\Grid\CollectionFactory as ReasonFactory;

class Cart extends CoreCart
{
    private $helper;
    private $productFactory;
    private $reasonFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        CheckoutSession $checkoutSession,
        Url $catalogUrlBuilder,
        HelperCart $cartHelper,
        HttpContext $httpContext,
        Data $helper,
        ProductFactory $productFactory,
        ReasonFactory $reasonFactory,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->productFactory = $productFactory;
        $this->reasonFactory = $reasonFactory;
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $catalogUrlBuilder,
            $cartHelper,
            $httpContext,
            $data
        );
    }

    public function getCustomerNotes()
    {
        if ($this->helper->isValidForVatExempt()) {
            return $this->helper->getCustomerNotes();
        }
        return '';
    }

    public function isEnable()
    {
        return $this->helper->isEnabled();
    }

    public function getReasons()
    {
        return $this->helper->getReasons();
    }
}
