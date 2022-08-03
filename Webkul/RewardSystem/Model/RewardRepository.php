<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_RewardSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\RewardSystem\Model;

use Magento\Framework\Session\SessionManager;
use Webkul\RewardSystem\Helper\Data as RewardSystemHelper;
use Magento\Checkout\Model\Session;

class RewardRepository implements \Webkul\RewardSystem\Api\RewardRepositoryInterface
{
    /**
     * @var Session
     */
    protected $session;

    protected $request;
    /**
     * @var RewardSystemHelper
     */
    protected $_rewardSystemHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    public function __construct(
        RewardSystemHelper $rewardSystemHelper,
        SessionManager $session,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        Session $chkoutSession
    ) {
        $this->_rewardSystemHelper = $rewardSystemHelper;
        $this->session = $session;
        $this->request = $request;
        $this->_objectManager = $objectManager;
        $this->_date = $date;
        $this->chkoutSession = $chkoutSession;
    }

    /**
     * Save Credit.
     *
     * @param \Webkul\RewardSystem\Api\RewardRepositoryInterface $rewardData
     *
     * @return array $rewardInfo
     *
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     */
    public function save($rewardData)
    {
        $fieldValues = $this->request->getParams();
        if (isset($fieldValues['cancel'])) {
            $this->session->unsRewardInfo();
            return [];
        }
        $customerRewards = $fieldValues['number_of_rewards'];
        $helper = $this->_rewardSystemHelper;
        $maxRewardUsed = $helper->getRewardCanUsed();
        if ($fieldValues['used_reward_points'] > $customerRewards) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You don\'t have sufficient Feather Points to use.')
            );
        }
        if ($fieldValues['used_reward_points'] > $maxRewardUsed) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can not use more than %1 feather points for this order purchase.', $maxRewardUsed)
            );
        }
        $quote = $this->chkoutSession->getQuote();
        $grandTotal = $quote->getGrandTotal();
        $perRewardAmount = $helper->getRewardValue();
        $perRewardAmount = $helper->currentCurrencyAmount($perRewardAmount);
        $rewardAmount = $fieldValues['used_reward_points']*$perRewardAmount;

        $rates = $quote->getShippingAddress()->getAllShippingRates();
        $out="";
        $shippingAmount=0;
        foreach($rates as $rate){
            $out .= '[' . $rate->getPrice() . '||'.  $rate->getMethod() . '||'. $rate->getMethodTitle() . ']';
            $shippingAmount = $helper->currentCurrencyAmount($rate->getPrice());
        }
        
        $totalLessShipping = $grandTotal;
        if(!empty($shippingAmount))
            $totalLessShipping = $grandTotal - $shippingAmount;

        // if(!empty($out)){
        //     throw new \Magento\Framework\Exception\LocalizedException(
        //         __('shipping rates.'.$shippingAmount.'grand total'.$grandTotal.'lessShipping'.$totalLessShipping)
        //     );
        // }
        if ($totalLessShipping >= $rewardAmount) {
            $flag = 0;
            $amount = 0;
            $availAmount = $customerRewards*$perRewardAmount;
            $rewardInfo = $this->session->getRewardInfo();
            if (!$rewardInfo) {
                $amount = $rewardAmount;
                $rewardInfo = [];
                $rewardInfo = [
                   'used_reward_points' => $fieldValues['used_reward_points'],
                   'number_of_rewards' => $customerRewards,
                   'avail_amount' => $availAmount,
                   'amount' => $amount
                ];
            } else {
                if (is_array($rewardInfo)) {
                    $rewardInfo['used_reward_points'] = $fieldValues['used_reward_points'];
                    $rewardInfo['number_of_rewards'] = $customerRewards;
                    $amount = $rewardAmount;
                    $rewardInfo['amount'] = $amount;

                    $flag = 1;
                }
                if ($flag == 0) {
                    $amount = $rewardAmount;
                    $rewardInfo= [
                       'used_reward_points' => $fieldValues['used_reward_points'],
                       'number_of_rewards' => $customerRewards,
                       'avail_amount' => $availAmount,
                       'amount' => $amount
                    ];
                }
            }
             $this->session->setRewardInfo($rewardInfo);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Feather Amount can not be greater than or equal to Order Total...')
            );
        }
        $rewardsInfo[] =$rewardInfo;
        return $rewardsInfo;
    }
}
