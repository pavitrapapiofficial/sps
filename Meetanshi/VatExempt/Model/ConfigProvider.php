<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Meetanshi\VatExempt\Helper\Data;

class ConfigProvider implements ConfigProviderInterface
{
    protected $vatHelper;
    public function __construct(Data $vatHelper)
    {
        $this->vatHelper = $vatHelper;
    }

    public function getConfig()
    {
        $config = [];
        $config['isEnable'] = $this->vatHelper->isEnabled();
        $config['isLogin'] = $this->vatHelper->getRequireLogin();
        $config['customerNotes'] = $this->vatHelper->getCustomerNotes();
        $config['isValidForVatExempt'] = $this->vatHelper->isValidForVatExempt();
        $config['acceptTerms'] = $this->vatHelper->getAcceptTerm();
        $config['productsName'] = $this->vatHelper->getQuoteProductName();
        $config['isDisableDeclaration'] = $this->vatHelper->isDisableDeclaration();
        $collection = $this->vatHelper->getReasons();
        $reasonData = [];
        foreach ($collection as $reason) {
            $reasonData[] = $reason->getReason();
        }
        $config['vatReasons'] = $reasonData;

        return $config;
    }
}
