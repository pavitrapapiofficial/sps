<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_RewardSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\RewardSystem\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Webkul\RewardSystem\Helper\Data as HelperData;

/**
 * Check is reward allowed on category page.
 */
class CategoryPage implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var HelperData
     */
    protected $helperData;
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param HelperData $helperData
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        HelperData $helperData
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helperData = $helperData;
    }
    /**
     * getrewardPriority is used to get the priority
     * @return int
     */
    public function getrewardPriority()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/priority',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * enableRewardSystem is used to check module is enabled or not from configuration.
     * @return bool
     */
    public function enableRewardSystem()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * getCustomerId is used to get the current customer id
     * @return int||null
     */
    public function getCustomerId()
    {
        return $this->helperData->getCustomerId();
    }
    /**
     * getCategoryRewardToShow is used to get the rewards based on the category id.
     * @return mixed
     */
    public function getCategoryRewardToShow($categoryId = 0)
    {
        return $this->helperData->getCategoryRewardToShow($categoryId);
    }
}
