<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Customer\Model\CustomerFactory;
use Magento\Catalog\Model\Product;

class SaveVatExemptData implements ObserverInterface
{
    private $quoteRepository;
    private $customerFactory;
    private $product;

    public function __construct(QuoteRepository $quoteRepository, Product $product, CustomerFactory $customerFactory)
    {
        $this->customerFactory = $customerFactory;
        $this->quoteRepository = $quoteRepository;
        $this->product = $product;
    }

    public function execute(EventObserver $observer)
    {

        $order = $observer->getOrder();
        $quoteRepository = $this->quoteRepository;

        $quote = $quoteRepository->get($order->getQuoteId());

        $order->setVatExemptCustomer($quote->getVatExemptCustomer());
        $order->setVatExemptReason($quote->getVatExemptReason());
        try {
            if (!empty($quote->getVatExemptReason()) && !empty($quote->getVatExemptCustomer())) :
                $orderItems = $order->getAllItems();
                $totalDedactedTax = 0;
                foreach ($orderItems as $item) {
                    $product = $this->product->load($item->getProductId());
                    if ($product->getIsVatexempt()) {
                        $totalDedactedTax = $totalDedactedTax + $item->getBaseTaxAmount();
                        $item->setVatExempted($item->getBaseTaxAmount());
                        $item->setBaseTaxAmount(0.00);
                        $item->setTaxAmount(0.00);
                    }
                }
                $order->setBaseGrandTotal($order->getBaseGrandTotal() - $totalDedactedTax);
                $order->setGrandTotal($order->getGrandTotal() - $totalDedactedTax);
                $order->setBaseTaxAmount($order->getBaseTaxAmount() - $totalDedactedTax);
                $order->setSubtotalInclTax($order->getSubtotalInclTax() - $totalDedactedTax);
                $order->setTaxAmount($order->getTaxAmount() - $totalDedactedTax);
            endif;
            return $this;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
