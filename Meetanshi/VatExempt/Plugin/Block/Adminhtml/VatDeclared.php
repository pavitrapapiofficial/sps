<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Plugin\Block\Adminhtml;

use Magento\Sales\Block\Adminhtml\Order\View\Info;
use Meetanshi\VatExempt\Helper\Data;

class VatDeclared
{
    protected $vatExempt;
    public function __construct(Data $vatExempt)
    {
        $this->vatExempt = $vatExempt;
    }

    public function afterToHtml(Info $subject, $result)
    {
        $block = $subject->getLayout()->getBlock('vatexempt_order');
        if ($block !== false && $this->vatExempt->isEnabled() == '1' && $subject->getOrder()->getVatExemptCustomer() != null) {
            $vatExemptCustomer = "<b>".$subject->getOrder()->getVatExemptCustomer()."</b>";
            $vatExemptReason = "<b>".$subject->getOrder()->getVatExemptReason()."</b>";

            $customerDeclaration = $this->vatExempt->getDeclaredNote();
            $customerDeclaration = str_replace("{customer_name}", $vatExemptCustomer, $customerDeclaration);
            $customerDeclaration = str_replace("{reasone_of}", $vatExemptReason, $customerDeclaration);
            $block->setCustomerDeclaration($customerDeclaration);
            $result = $result . $block->toHtml();
        }

        return $result;
    }
}
