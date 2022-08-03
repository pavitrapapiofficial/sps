<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Data;

class Index extends Action
{
    private $resultPageFactory;
    private $jsonHelper;
    private $quoteRepository;
    private $resultJsonFactory;
    private $checkoutSession;
    private $total;
    private $product;
    private $taxHelper;
    private $totalDedactedTax;
    private $totalNotDedactedTax;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CartRepositoryInterface $quoteRepository,
        ResultJsonFactory $resultJsonFactory,
        Session $checkoutSession,
        Total $total,
        Product $product,
        Data $taxHelper,
        JsonHelper $jsonHelper
    )
    {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->quoteRepository = $quoteRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->product = $product;
        $this->total = $total;
        $this->totalDedactedTax = 0;
        $this->totalNotDedactedTax = 0;
        $this->taxHelper = $taxHelper;
    }

    public function execute()
    {
        try {
            $data = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());

            if ($data['apply']) {
                $quote = $this->checkoutSession->getQuote();
                $quote = $this->quoteRepository->get($quote->getEntityId());

                $items = $quote->getAllVisibleItems();
                foreach ($items as $item) {
                    if ($item->getId()) {
                        $product = $this->product->load($item->getProductId());
                        if ($product->getIsVatexempt()) {
                            $this->totalDedactedTax = $this->totalDedactedTax + $item->getBaseTaxAmount();
                            $item->setVatExempted($item->getBaseTaxAmount());
                            $item->setBaseTaxAmount(0.00);
                            $item->setTaxAmount(0.00);
                        } else {
                            $item->setVatExempted(0);
                        }
                    }
                }

                $shippingAddress = $quote->getShippingAddress();
                $shippingAddress->setBaseTaxAmount($shippingAddress->getBaseTaxAmount() - $this->totalDedactedTax);
                $shippingAddress->setTaxAmount($shippingAddress->getTaxAmount() - $this->totalDedactedTax);

                $shippingAddress->setBaseSubtotalTotalInclTax($shippingAddress->getBaseSubtotalTotalInclTax() - $this->totalDedactedTax);
                $shippingAddress->setSubtotalInclTax($shippingAddress->getSubtotalInclTax() - $this->totalDedactedTax);
                $shippingAddress->setBaseGrandTotal($shippingAddress->getBaseGrandTotal() - $this->totalDedactedTax);
                $shippingAddress->setGrandTotal($shippingAddress->getGrandTotal() - $this->totalDedactedTax);
                $quote->setBaseGrandTotal($quote->getBaseGrandTotal() - $this->totalDedactedTax);
                $quote->setGrandTotal($quote->getGrandTotal() - $this->totalDedactedTax);

                $quote->setData('vat_exempt_customer', $data['exemptname']);
                $quote->setData('vat_exempt_customer', $data['exemptname']);
                $quote->setData('vat_exempt_reason', $data['exemptreason']);
                $quote->save();
                $response = [
                    'message' => 'Applied for VAT Exemption Successfully.'
                ];
            } else {
                $quote = $this->checkoutSession->getQuote();

                $items = $quote->getAllVisibleItems();
                foreach ($items as $item) {
                    if ($item->getId()) {
                        $product = $this->product->load($item->getProductId());
                        if ($product->getIsVatexempt()) {
                            $this->totalDedactedTax = $this->totalDedactedTax + $item->getVatExempted();
                            $item->setBaseTaxAmount($item->getVatExempted());
                            $item->setTaxAmount($item->getVatExempted());
                            $item->setVatExempted(0);
                        }
                    }
                }

                $shippingAddress = $quote->getShippingAddress();
                $shippingAddress->setBaseTaxAmount($shippingAddress->getBaseTaxAmount() + $this->totalDedactedTax);
                $shippingAddress->setTaxAmount($shippingAddress->getTaxAmount() + $this->totalDedactedTax);

                $shippingAddress->setBaseSubtotalTotalInclTax($shippingAddress->getBaseSubtotalTotalInclTax() + $this->totalDedactedTax);
                $shippingAddress->setSubtotalInclTax($shippingAddress->getSubtotalInclTax() + $this->totalDedactedTax);
                $shippingAddress->setBaseGrandTotal($shippingAddress->getBaseGrandTotal() + $this->totalDedactedTax);
                $shippingAddress->setGrandTotal($shippingAddress->getGrandTotal() + $this->totalDedactedTax);
                $quote->setBaseGrandTotal($quote->getBaseGrandTotal() + $this->totalDedactedTax);
                $quote->setGrandTotal($quote->getGrandTotal() + $this->totalDedactedTax);

                $quote->setData('vat_exempt_customer', '');
                $quote->setData('vat_exempt_reason', '');
                $quote->save();
                $response = [
                    'message' => 'VAT Exemption Cancelled Successfully.'
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
