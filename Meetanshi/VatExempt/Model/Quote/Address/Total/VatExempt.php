<?php

namespace Meetanshi\VatExempt\Model\Quote\Address\Total;

use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;


class VatExempt extends AbstractTotal
{
    private $catalogSession;
    private $checkoutSession;
    private $totalDedactedTax;
    private $totalNotDedactedTax;
    private $product;

    public function __construct(
        Session $checkoutSession,
        Product $product,
        CatalogSession $catalogSession

    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->catalogSession = $catalogSession;
        $this->totalDedactedTax = 0;
        $this->totalNotDedactedTax = 0;
        $this->product = $product;
    }

    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Quote\Address\Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);
        try {
            $products = explode(',', $this->catalogSession->getExemptProduct());
            $flag = false;
            $items = $quote->getAllVisibleItems();
            if ( $quote->getData('vat_exempt_reason') != '' ) {
                foreach ($items as $item) {
                    if ( $item->getId() ) {
                        $product = $this->product->load($item->getProductId());
                        if ( $product->getIsVatexempt() ) {
                            $this->totalDedactedTax = $this->totalDedactedTax + $item->getTaxAmount();
                            $item->setVatExempted($item->getBaseTaxAmount());
                            $item->setBaseTaxAmount(0.00);
                            $item->setTaxAmount(0.00);
                            $flag = true;
                        } else {
                            $item->setVatExempted(0);
                        }
                    }
                }
            }


            if ( $flag ) {
                // $quote->setData('vat_exempt_customer', $this->catalogSession->getExemptName());
                //  $quote->setData('vat_exempt_reason', $this->catalogSession->getExemptReason());
                $quote->save();
                $total->setTaxAmount($total->getTaxAmount() - $this->totalDedactedTax);
                $total->setBaseTaxAmount($total->getBaseTaxAmount() - $this->totalDedactedTax);
                $total->setBaseGrandTotal($total->getBaseGrandTotal() - $this->totalDedactedTax);
                $total->setGrandTotal($total->getGrandTotal() - $this->totalDedactedTax);
            }

        } catch (NoSuchEntityException $e) {

        } catch (LocalizedException $e) {

        }
        return $this;
    }
}