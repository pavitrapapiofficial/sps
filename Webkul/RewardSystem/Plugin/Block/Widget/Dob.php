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

namespace Webkul\RewardSystem\Plugin\Block\Widget;

use Webkul\RewardSystem\Helper\Data as RewardSystemHelper;

class Dob
{
    /**
     * @var \Webkul\RewardSystem\Helper\Data
     */
    protected $helper;

    /**
     * @param RewardSystemHelper $helper
     */
    public function __construct(
        RewardSystemHelper $helper
    ) {
        $this->helper = $helper;
    }

    public function afterIsEnabled(
        \Magento\Customer\Block\Widget\Dob $subject,
        $result
    ) {
        $helper = $this->helper;
        $enableRewardSystem = $helper->enableRewardSystem();
        if ($helper->getConfigData('reward_on_birthday') && $enableRewardSystem) {
            return true;
        }
        return $result;
    }
}
