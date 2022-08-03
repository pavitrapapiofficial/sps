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
namespace Webkul\RewardSystem\Controller\Checkout;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Session\SessionManager;
use Webkul\RewardSystem\Model\RewardrecordFactory as RewardRecordCollection;
use Webkul\RewardSystem\Helper\Data as RewardHelper;

class ApplyRewards extends Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var RewardRecordCollection;
     */
    protected $rewardRecordCollection;

    /**
     * @var RewardHelper;
     */
    protected $helper;

    /**
     * @param Context                                    $context
     * @param SessionManager                             $session
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Checkout\Model\Session            $customerSession
     * @param RewardRecordCollection                     $rewardRecordCollection
     * @param RewardHelper                               $helper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        SessionManager $session,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        RewardRecordCollection $rewardRecordCollection,
        RewardHelper $helper,
        array $data = []
    ) {
        $this->session = $session;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->rewardRecordCollection = $rewardRecordCollection;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $helper = $this->helper;
        $fieldValues = $this->getRequest()->getParams();
        $quote = $this->checkoutSession->getQuote();
        $totalRewards = $this->getRewardData();
        $customerId = $helper->getCustomerId();
        $customerRewards = $fieldValues['number_of_rewards'];
        //if ($fieldValues['used_reward_points'] > $totalRewards['remaining_rewards']) {
        if ($fieldValues['used_reward_points'] > $customerRewards) {
            $this->messageManager->addError(__('Feather points can\'t be greater than customer\'s feather point(s). '));
            return $this->resultRedirectFactory
                ->create()
                ->setPath('checkout/cart', ['_secure' => $this->getRequest()->isSecure()]);
        }
        /**
         * How much reward point can be used of customer
         */
        $maxRewardUsed = $helper->getRewardCanUsed();
        if ($fieldValues['used_reward_points'] > $maxRewardUsed) {
            $this->messageManager->addError(
                __(
                    'You can not use more than %1 feather points for this order purchase.',
                    $maxRewardUsed
                )
            );
            return $this->resultRedirectFactory
                ->create()
                ->setPath('checkout/cart', ['_secure' => $this->getRequest()->isSecure()]);
        }
        $grandTotal = $quote->getGrandTotal();
        $perRewardAmount = $helper->getRewardValue();
        $perRewardAmount = $helper->currentCurrencyAmount($perRewardAmount);
        $rewardAmount = $fieldValues['used_reward_points']*$perRewardAmount;
        $shippingAmount = $quote->getShippingAddress()->setCollectShippingRates(true);
        $rates = $quote->getShippingAddress()->getAllShippingRates();
        $out="";
        $shippingAmount=0;
        foreach($rates as $rate){
            $out .= '[' . $rate->getPrice() . '||'.  $rate->getMethod() . '||'. $rate->getMethodTitle() . ']';
            $shippingAmount = $helper->currentCurrencyAmount($rate->getPrice());
        }
        $totalLessShipping = $grandTotal;
        if(!empty($shippingAmount))
            $totalLessShipping = round($grandTotal) - $shippingAmount;
        if ($totalLessShipping >= $rewardAmount) {
            $flag = 0;
            $amount = 0;
            $availAmount = $customerRewards*$perRewardAmount;
            $rewardInfo = $this->session->getRewardInfo();
            if (!$rewardInfo) {
                $amount = $rewardAmount;
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
            $this->messageManager->addError(__('Feather Amount can not be greater than or equal to Order Total...'));
            return $this->resultRedirectFactory
                ->create()
                ->setPath('checkout/cart', ['_secure' => $this->getRequest()->isSecure()]);
        }
        $this->messageManager->addSuccess('Feather point has been applied successfully.');
        return $this->resultRedirectFactory
            ->create()
            ->setPath('checkout/cart', ['_secure' => $this->getRequest()->isSecure()]);
    }
    /**
     * Get Reward Data of customer
     */
    public function getRewardData()
    {
        $customerId = $this->helper->getCustomerId();
        $quote = $this->checkoutSession->getQuote();
        $options = [];
        $collection = $this->rewardRecordCollection->create()
                        ->getCollection()
                        ->addFieldToFilter(
                            'customer_id',
                            ['eq' => $customerId]
                        )->getFirstItem();
        $remainingRewards = $collection->getRemainingRewardPoint();
        $options['remaining_rewards'] = $remainingRewards * $this->helper->getRewardValue();
        $options['amount'] = $remainingRewards;
        $options['customer_id'] = $customerId;
        return $options;
    }
}
