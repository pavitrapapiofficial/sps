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

namespace Webkul\RewardSystem\Helper;

use Magento\Sales\Model\OrderFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Webkul\RewardSystem\Api\RewardrecordRepositoryInterface;
use Webkul\RewardSystem\Api\RewarddetailRepositoryInterface;
use Webkul\RewardSystem\Api\RewardproductRepositoryInterface;
use Webkul\RewardSystem\Api\RewardproductSpecificRepositoryInterface;
use Webkul\RewardSystem\Api\RewardcategoryRepositoryInterface;
use Webkul\RewardSystem\Api\Data\RewardrecordInterfaceFactory;
use Webkul\RewardSystem\Api\Data\RewarddetailInterfaceFactory;
use Webkul\RewardSystem\Api\Data\RewardproductInterfaceFactory;
use Webkul\RewardSystem\Api\Data\RewardproductSpecificInterfaceFactory;
use Webkul\RewardSystem\Api\Data\RewardcategoryInterfaceFactory;
use Webkul\RewardSystem\Api\Data\RewardcategorySpecificInterfaceFactory;
use Webkul\RewardSystem\Api\RewardcategorySpecificRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Webkul\RewardSystem\Model\ResourceModel\Rewardrecord\CollectionFactory as RewardRecordCollection;
use Webkul\RewardSystem\Model\ResourceModel\Rewardproduct\CollectionFactory as RewardProductCollection;
use Webkul\RewardSystem\Model\ResourceModel\RewardproductSpecific\CollectionFactory as RewardproductSpecificCollection;
use Webkul\RewardSystem\Model\ResourceModel\Rewardcart\CollectionFactory as RewardcartCollection;
use Webkul\RewardSystem\Model\ResourceModel\Rewardcategory\CollectionFactory as RewardcategoryCollection;
use Webkul\RewardSystem\Model\ResourceModel\RewardcategorySpecific\CollectionFactory
as RewardcategorySpecificCollection;
use Webkul\RewardSystem\Model\ResourceModel\Rewardattribute\CollectionFactory as RewardattributeCollection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Session
     */
    protected $_customerSession;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    /**
     * @var Magento\Framework\Pricing\Helper\Data
     */
    protected $_pricingHelper;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    /**
     * @var Magento\Sales\Model\OrderFactory;
     */
    protected $_orderModel;
    /**
     * @var \Webkul\RewardSystem\Helper\Mail
     */
    protected $_mailHelper;
    /**
     * @var Magento\Customer\Model\CustomerFactory
     */
    protected $_customerModel;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cartModel;
    /**
     * @var \Webkul\RewardSystem\Api\RewardrecordRepositoryInterface;
     */
    protected $_rewardRecordRepository;
    /**
     * @var \Webkul\RewardSystem\Api\RewarddetailRepositoryInterface;
     */
    protected $_rewardDetailRepository;
    /**
     * @var \Webkul\RewardSystem\Api\RewardproductRepositoryInterface;
     */
    protected $_rewardProductRepository;
    /**
     * @var \Webkul\RewardSystem\Api\RewardproductSpecificRepositoryInterface;
     */
    protected $_rewardproductSpecificRepository;
    /**
     * @var \Webkul\RewardSystem\Api\RewardcategoryRepositoryInterface;
     */
    protected $_rewardCategoryRepository;
    /**
     * @var \Webkul\RewardSystem\Api\Data\RewardrecordInterfaceFactory;
     */
    protected $_rewardRecordInterface;
    /**
     * @var \Webkul\RewardSystem\Api\RewardrecordRepositoryInterface;
     */
    protected $_rewardDetailInterface;
    /**
     * @var \Webkul\RewardSystem\Api\Data\RewardproductInterfaceFactory;
     */
    protected $_rewardProductInterface;
    /**
     * @var \Webkul\RewardSystem\Api\Data\RewardproductSpecificInterfaceFactory;
     */
    protected $_rewardproductSpecificInterface;
    /**
     * @var \Webkul\RewardSystem\Api\Data\RewardcategoryInterfaceFactory;
     */
    protected $_rewardCategoryInterface;
    /**
     * @var \Webkul\RewardSystem\Api\Data\RewardcategorySpecificInterfaceFactory;
     */
    protected $_rewardcategorySpecificInterface;
    /**
     * @var DataObjectHelper
     */
    protected $_dataObjectHelper;
    /**
     * @var RewardProductCollection;
     */
    protected $_rewardProductCollection;
    /**
     * @var RewardRecordCollection;
     */
    protected $_rewardRecordCollection;
    /**
     * @var RewardcartCollection;
     */
    protected $_rewardcartCollection;
    /**
     * @var RewardattributeCollection;
     */
    protected $_rewardattributeCollection;
    /**
     * @var RewardcategoryCollection;
     */
    protected $_rewardcategoryCollection;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface;
     */
    protected $timezone;
    /**
     * @var \Magento\Framework\App\Http\Context;
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\App\Helper\Context             $context
     * @param \Magento\Customer\Model\Session                   $customerSession
     * @param \Magento\Framework\Locale\CurrencyInterface       $localeCurrency
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Magento\Directory\Model\Currency                 $currency
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Pricing\Helper\Data            $pricingHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime       $date
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        CustomerSession $customerSession,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        OrderFactory $orderModel,
        \Webkul\RewardSystem\Helper\Mail $mailHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Checkout\Model\Cart $cartModel,
        RewardrecordRepositoryInterface $rewardRecordRepository,
        RewarddetailRepositoryInterface $rewardDetailRepository,
        RewardproductRepositoryInterface $rewardProductRepository,
        RewardproductSpecificRepositoryInterface $rewardproductSpecificRepository,
        RewardcategoryRepositoryInterface $rewardCategoryRepository,
        RewardcategorySpecificRepositoryInterface $rewardcategorySpecificRepository,
        RewardrecordInterfaceFactory $rewardRecordInterface,
        RewarddetailInterfaceFactory $rewardDetailInterface,
        RewardproductInterfaceFactory $rewardProductInterface,
        RewardproductSpecificInterfaceFactory $rewardproductSpecificInterface,
        RewardcategoryInterfaceFactory $rewardCategoryInterface,
        RewardcategorySpecificInterfaceFactory $rewardcategorySpecificInterface,
        DataObjectHelper $dataObjectHelper,
        RewardRecordCollection $rewardRecordCollection,
        RewardProductCollection $rewardProductCollection,
        RewardproductSpecificCollection $rewardproductSpecificCollection,
        RewardcartCollection $rewardcartCollection,
        RewardattributeCollection $rewardattributeCollection,
        RewardcategorySpecificCollection $rewardcategorySpecificCollection,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ProductFactory $productModel,
        RewardcategoryCollection $rewardcategoryCollection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_localeCurrency = $localeCurrency;
        $this->_currency = $currency;
        $this->_storeManager = $storeManager;
        $this->_priceCurrency = $priceCurrency;
        $this->_pricingHelper = $pricingHelper;
        $this->_date = $date;
        $this->_orderModel = $orderModel;
        $this->_mailHelper = $mailHelper;
        $this->cartModel = $cartModel;
        $this->_customerModel = $customerFactory;
        $this->_rewardRecordRepository = $rewardRecordRepository;
        $this->_rewardDetailRepository = $rewardDetailRepository;
        $this->_rewardProductRepository = $rewardProductRepository;
        $this->_rewardproductSpecificRepository = $rewardproductSpecificRepository;
        $this->_rewardCategoryRepository = $rewardCategoryRepository;
        $this->_rewardcategorySpecificRepository = $rewardcategorySpecificRepository;
        $this->_rewardRecordInterface = $rewardRecordInterface;
        $this->_rewardDetailInterface = $rewardDetailInterface;
        $this->_rewardProductInterface = $rewardProductInterface;
        $this->_rewardproductSpecificInterface = $rewardproductSpecificInterface;
        $this->_rewardCategoryInterface = $rewardCategoryInterface;
        $this->_rewardcategorySpecificInterface = $rewardcategorySpecificInterface;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_rewardRecordCollection = $rewardRecordCollection;
        $this->_rewardProductCollection = $rewardProductCollection;
        $this->_rewardproductSpecificCollection = $rewardproductSpecificCollection;
        $this->_rewardcartCollection = $rewardcartCollection;
        $this->_rewardattributeCollection = $rewardattributeCollection;
        $this->_rewardcategorySpecificCollection = $rewardcategorySpecificCollection;
        $this->eavConfig = $eavConfig;
        $this->productModel = $productModel;
        $this->httpContext = $httpContext;
        $this->_rewardcategoryCollection = $rewardcategoryCollection;
        $this->timezone = $timezone;
    }
    // return customer id from customer session
    public function getCustomerId()
    {
        return $this->httpContext->getValue('customer_id');
    }
    /**
     * get Reward configurations value
     */
    public function getConfigData($field)
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/'.$field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableRewardSystem()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    // return reward points value
    public function getRewardValue()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/reward_value',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    // return maximum reward points can assign
    public function getRewardCanAssign()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/max_reward_assign',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    // return maximum reward points can Use
    public function getRewardCanUsed()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/max_reward_used',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    // return either reward points allowed on registration or not
    public function getAllowRegistration()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/allow_registration',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    // return reward points on registraion
    public function getRewardOnRegistration()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/registration_reward',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    // return either reward points allowed on registration or not
    public function getAllowReview()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/allow_review',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    // return reward points on review
    public function getRewardOnReview()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/review_reward',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    // get reward priority set in system config
    public function getrewardPriority()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/priority',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //get status of product quantity wise reward
    public function getrewardQuantityWise()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/activeproduct',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getDefaultTransEmailId()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getDefaultTransName()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_general/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getRewardApprovedOn()
    {
        return $this->scopeConfig->getValue(
            'rewardsystem/general_settings/order_reward_approved_on',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * [formatDate]
     * @param  string $date
     * @return string Formatted Date
     */
    public function formatDate($date)
    {
        if ($date) {
            return $this->timezone->formatDate(
                $date,
                \IntlDateFormatter::FULL,
                false
            );
        } else {
            return __("-");
        }
    }

    /**
     * getTimeAccordingToTimeZone Magento Locale Timezone
     *
     * @param $datetime
     */
    public function getTimeAccordingToTimeZone($dateTime)
    {
        // for get current time according to time zone
        $today = $this->timezone->date()->format('h:i A');

        // for convert date time according to magento time zone
        $dateTimeAsTimeZone = $this->timezone
                                        ->date(new \DateTime($dateTime))
                                       ->format('h:i A');
        return $dateTimeAsTimeZone;
    }

    public function saveDataRewardRecord($completeDataObject)
    {
        try {
            $this->_rewardRecordRepository->save($completeDataObject);
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    $e->getMessage()
                )
            );
        }
    }

    public function saveDataRewardDetail($completeDataObject)
    {
        try {
            $this->_rewardDetailRepository->save($completeDataObject);
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    $e->getMessage()
                )
            );
        }
    }

    // return currency currency code
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    // get base currency code
    public function getBaseCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    // get all allowed currency in system config
    public function getConfigAllowCurrencies()
    {
        return $this->_currency->getConfigAllowCurrencies();
    }

    // get currency rates
    public function getCurrencyRates($currency, $toCurrencies = null)
    {
        return $this->_currency->getCurrencyRates($currency, $toCurrencies); // give the currency rate
    }

    // get currency symbol of an currency code
    public function getCurrencySymbol($currencycode)
    {
        $currency = $this->_localeCurrency->getCurrency($currencycode);

        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }

    public function getformattedPrice($price)
    {
        return $this->_pricingHelper
            ->currency($price, true, false);
    }

    // get currenct store
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    //get currenct currency amount from base
    public function currentCurrencyAmount($amount, $store = null)
    {
        if ($store == null) {
            $store = $this->_storeManager->getStore()->getStoreId();
        }
        $returnAmount = $this->_priceCurrency->convert($amount, $store);

        return round($returnAmount, 4);
    }

    //get amount in base currency amount from current currency
    public function baseCurrencyAmount($amount, $store = null)
    {
        if ($store == null) {
            $store = $this->_storeManager->getStore()->getStoreId();
        }
        if ($amount == 0) {
            return $amount;
        }
        $rate = $this->_priceCurrency->convert($amount, $store) / $amount;
        $amount = $amount / $rate;

        return round($amount, 4);
    }

    //get customer's Remaining Reward Points
    public function getCurrentRewardOfCustomer($customerId)
    {
        $reward = 0;
        $rewardRecordCollection = $this->_rewardRecordCollection->create()
          ->addFieldToFilter('customer_id', $customerId);
        if ($rewardRecordCollection->getSize()) {
            foreach ($rewardRecordCollection as $recordData) {
                $reward = $recordData->getRemainingRewardPoint();
            }
        }
        return $reward;
    }
    /**
     * [sendPointsExpireEmail]
     * @param  \Webkul\RewardSystem\Model\Rewarddetail $transaction
     * @return
     */
    public function sendPointsExpireEmail($transaction)
    {
        $customerId = $transaction->getCustomerId();
        $remainingPoints = $transaction->getRewardPoint() - $transaction->getRewardUsed();
        $msg = __(
            "Please, note that your reward points %1 will expire on %2",
            $remainingPoints,
            $transaction->getExpiresAt()
        );
        $customer = $this->_customerModel
          ->create()
          ->load($customerId);
        $receiverInfo = [
          'name' => $customer->getName(),
          'email' => $customer->getEmail(),
        ];
        $adminEmail= $this->getDefaultTransEmailId();
        $adminUsername = $this->getDefaultTransName();
        $senderInfo = [
          'name' => $adminUsername,
          'email' => $adminEmail,
        ];
        $this->_mailHelper->sendExpireEmail($receiverInfo, $senderInfo, $msg, $remainingPoints);
    }

    public function setDataFromAdmin(
        $msg,
        $adminMsg,
        $rewardData,
        $isorder=0
    ) {
        $assignStatus = true;
        $maxRewardCanAssign = $this->getRewardCanAssign();
        $customerReward = $this->getCurrentRewardOfCustomer($rewardData['customer_id']);
        if ($rewardData['type'] == "credit" && $maxRewardCanAssign < ($customerReward + $rewardData['points'])) {
            $assignStatus = false;
        }
        if ($assignStatus) {
            $status = $rewardData['status'];
            $rewardValue = $this->getRewardValue();
            $baseCurrencyCode = $this->getBaseCurrencyCode();
            $amount = $rewardValue * $rewardData['points'];
            $isRevert = isset($rewardData['is_revert']) ? $rewardData['is_revert']: 0;
            $isExpired = 0;
            if (isset($rewardData['is_expired'])) {
                $isExpired = $rewardData['is_expired'];
            }
            $recordDetail = [
              'customer_id' => $rewardData['customer_id'],
              'reward_point' => $rewardData['points'],
              'amount' => $amount,
              'status' => $status,
              'action' => $rewardData['type'],
              'order_id' => $rewardData['order_id'],
              'transaction_at' => $this->_date->gmtDate(),
              'currency_code' => $baseCurrencyCode,
              'curr_amount' => $amount,
              'review_id' => $rewardData['review_id'],
              'transaction_note' => $rewardData['note'],
              'is_expired' => $isExpired,
              'is_revert' => $isRevert
            ];
            $dataObjectRecordDetail = $this->_rewardDetailInterface->create();

            $this->_dataObjectHelper->populateWithArray(
                $dataObjectRecordDetail,
                $recordDetail,
                \Webkul\RewardSystem\Api\Data\RewarddetailInterface::class
            );
            $this->saveDataRewardDetail($dataObjectRecordDetail);

            if ($status==1) {
                if ($rewardData['type'] == 'debit') {
                    $this->updateExpiryRecordData($rewardData);
                }
                $this->updateRewardRecordData($msg, $adminMsg, $rewardData,0,$isorder);
            }
        }
    }

    public function updateExpiryRecordData($rewardData)
    {
        try {
            $points = $rewardData['points'];
            $customerId = $rewardData['customer_id'];
            $transactions = $this->_rewardDetailInterface->create()
                          ->getCollection()
                          ->addFieldToFilter('customer_id', $customerId)
                          ->addFieldToFilter('is_expired', 0)
                          ->addFieldToFilter('action', 'credit')
                          ->setOrder('expires_at', 'ASC');
            $transactions->getSelect()->where('reward_point > reward_used OR reward_used IS NULL');
            if ($transactions->getSize()) {
                foreach ($transactions as $transaction) {
                    $remainingPoints = $transaction->getRewardPoint() - $transaction->getRewardUsed();
                    if ($points) {
                        if ($points <= $remainingPoints) {
                            $updatedPoints = $transaction->getRewardUsed() + $points;
                            $points = 0;
                        } else {
                            $updatedPoints = $transaction->getRewardUsed() + $remainingPoints;
                            $points -= $remainingPoints;
                        }
                        $transaction->setRewardUsed($updatedPoints)->save();
                    } else {
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    $e->getMessage()
                )
            );
        }
    }

    public function updateRewardRecordData($msg, $adminMsg, $rewardData, $storeId = 0,$isorder=0)
    {
        try {
            $points = $rewardData['points'];
            $customerId = $rewardData['customer_id'];
            $entityId = $this->checkAlreadyExists($customerId);
            $remainingPoints = 0;
            $usedPoints = 0;
            $totalPoints = 0;
            $id = '';
            if ($entityId) {
                $rewardRecord = $this->_rewardRecordRepository->getById($entityId);
                $remainingPoints = $rewardRecord->getRemainingRewardPoint();
                $usedPoints = $rewardRecord->getUsedRewardPoint();
                $totalPoints = $rewardRecord->getTotalRewardPoint();
                $id = $entityId;
            }
            if ($rewardData['type']=='credit') {
                $remainingPoints += $points;
                $totalPoints += $points;
            } else {
                $usedPoints += $points;
                $remainingPoints -= $points;
            }
            
            $recordData = [
                'customer_id' => $customerId,
                'total_reward_point' => $totalPoints,
                'remaining_reward_point' => $remainingPoints,
                'used_reward_point' => $usedPoints,
                'updated_at' => $this->_date->gmtDate()
            ];
            if ($id) {
                $recordData['entity_id'] = $id;
            }

            $dataObjectRewardRecord = $this->_rewardRecordInterface->create();

            $customer = $this->_customerModel
                ->create()
                ->load($customerId);
            $receiverInfo = [
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
            ];
            $adminEmail= $this->getDefaultTransEmailId();
            $adminUsername = $this->getDefaultTransName();
            $senderInfo = [
                'name' => $adminUsername,
                'email' => $adminEmail,
            ];
            $this->_dataObjectHelper->populateWithArray(
                $dataObjectRewardRecord,
                $recordData,
                \Webkul\RewardSystem\Api\Data\RewardrecordInterface::class
            );
            $this->saveDataRewardRecord($dataObjectRewardRecord);
            $expiresDays = (int)$this->getconfigData('expires_after_days');
            if (isset($rewardData['reward_id']) && $expiresDays) {
                $date = $this->_date->gmtDate(
                    'Y-m-d',
                    $this->_date->gmtTimestamp() + $expiresDays * 24 * 60 * 60
                );
                $transaction = $this->_rewardDetailInterface->create()
                            ->load($rewardData['reward_id']);
                $transaction->setExpiresAt($date)->save();
            }
            $this->_mailHelper->sendMail($receiverInfo, $senderInfo, $msg, $remainingPoints, $storeId,$isorder);
            $this->_mailHelper->sendAdminMail($receiverInfo, $senderInfo, $adminMsg, $remainingPoints, $storeId,$isorder);
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    $e->getMessage()
                )
            );
        }
    }

    public function checkAlreadyExists($customerId)
    {
        $rowId = 0;
        $rewardRecordCollection = $this->_rewardRecordCollection->create()
            ->addFieldToFilter('customer_id', $customerId);
        if ($rewardRecordCollection->getSize()) {
            foreach ($rewardRecordCollection as $rewardRecord) {
                $rowId = $rewardRecord->getEntityId();
            }
        }
        return $rowId;
    }
    public function setProductRewardData($rewardProductData)
    {
        $dataObjectProductDetail = $this->_rewardProductInterface->create();
        $this->_dataObjectHelper->populateWithArray(
            $dataObjectProductDetail,
            $rewardProductData,
            \Webkul\RewardSystem\Api\Data\RewardproductInterface::class
        );
        try {
            $this->_rewardProductRepository->save($dataObjectProductDetail);
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    $e->getMessage()
                )
            );
        }
    }
    public function setCategoryRewardData($rewardCategoryData)
    {
        $dataObjectCategoryDetail = $this->_rewardCategoryInterface->create();
        $this->_dataObjectHelper->populateWithArray(
            $dataObjectCategoryDetail,
            $rewardCategoryData,
            \Webkul\RewardSystem\Api\Data\RewardcategoryInterface::class
        );
        try {
            $this->_rewardCategoryRepository->save($dataObjectCategoryDetail);
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    $e->getMessage()
                )
            );
        }
    }

    public function setProductSpecificRewardData($rewardProductData)
    {
        $dataObjectProductDetail = $this->_rewardproductSpecificInterface->create();
        $this->_dataObjectHelper->populateWithArray(
            $dataObjectProductDetail,
            $rewardProductData,
            \Webkul\RewardSystem\Api\Data\RewardproductSpecificInterface::class
        );
        try {
            $this->_rewardproductSpecificRepository->save($dataObjectProductDetail);
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    $e->getMessage()
                )
            );
        }
    }

    public function setCategorySpecificRewardData($rewardCategoryData)
    {
        $dataObjectCategoryDetail = $this->_rewardcategorySpecificInterface->create();
        $this->_dataObjectHelper->populateWithArray(
            $dataObjectCategoryDetail,
            $rewardCategoryData,
            \Webkul\RewardSystem\Api\Data\RewardcategorySpecificInterface::class
        );
        try {
            $this->_rewardcategorySpecificRepository->save($dataObjectCategoryDetail);
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    $e->getMessage()
                )
            );
        }
    }

   /**
    * calculateCreditAmountforOrder for Priority Based
    * Priority Product Based   = 0
    * Priority Cart Based      = 1
    * Priority Category Based  = 2
    */
    public function calculateCreditAmountforOrder($orderId = 0)
    {
        $rewardPoint = 0;
        $priority = $this->getrewardPriority();
        // if ($priority==0) {
        //     //product based
        //     $quantityWise = $this->getrewardQuantityWise();
        //     if ($orderId!=0) {
        //         $order = $this->_orderModel->create()->load($orderId);
        //         $cartData = $order->getAllVisibleItems();
        //     } else {
        //         $cartData = $this->cartModel->getQuote()->getAllVisibleItems();
        //     }
        //     foreach ($cartData as $item) {
        //         $rewardPoint += $this->getProductData($item, $quantityWise);
        //     }
        // } elseif ($priority==1) {
        //     //cart based
        //     $amount = $this->getSubTotal($orderId);
        //     $rewardPoint = $this->getRewardBasedOnRules($amount);
        // } elseif ($priority==2) {
        //   //category based
        //     if ($orderId!=0) {
        //         $order = $this->_orderModel->create()->load($orderId);
        //         $cartData = $order->getAllVisibleItems();
        //     } else {
        //         $cartData = $this->cartModel->getQuote()->getAllVisibleItems();
        //     }
        //     foreach ($cartData as $item) {
        //         $rewardPoint += $this->getCategoryData($item);
        //     }
        // } else {
        //     if ($orderId!=0) {
        //         $order = $this->_orderModel->create()->load($orderId);
        //         $cartData = $order->getAllItems();
        //     } else {
        //         $cartData = $this->cartModel->getQuote()->getAllItems();
        //     }
        //     foreach ($cartData as $item) {
        //         $rewardPoint += $this->getAttributeData($item);
        //     }
        // }
        if ($orderId!=0) {
            $amount = $this->getSubTotal($orderId);
            $orderData = $this->_orderModel->create()->load($orderId);
            $shippingAmount = (float)$orderData->getShippingAmount();
            // $amount = $amount-$shippingAmount;
        }
        $rewardPoint = round($amount);
        if (!$rewardPoint) {
            return 0;
        }
        return $rewardPoint;
    }

    public function getAttributeData($item)
    {
        $productId = $item->getProduct()->getId();
        $rewardpoint = 0;
        $product = $this->productModel->create()->load($productId);
        $optionId = $product->getData($this->getAttributeCode());
        $attributeCode = $this->getAttributeCode();
        $collection = $this->_rewardattributeCollection->create()
                    ->addFieldToFilter('option_id', ['eq'=>$optionId])
                    ->addFieldToFilter('attribute_code', ['eq'=>$attributeCode])
                    ->addFieldToFilter('status', ['eq'=>1]);
        if ($collection->getSize()) {
            foreach ($collection as $attributeData) {
                $rewardpoint = $attributeData->getPoints();
            }
        }
        return $rewardpoint;
    }

    public function getCategoryData($item)
    {
        $rewardpoint = $this->getCategorySpecificData($item);
        $categoryIds = $item->getProduct()->getCategoryIds();
        $categoryReward = [];
        if (is_array($categoryIds) && !$rewardpoint) {
            $categoryRewardCollection = $this->_rewardcategoryCollection
                                    ->create()
                                    ->addFieldToFilter('status', ['eq'=>1])
                                    ->addFieldToFilter('category_id', ['in'=>$categoryIds]);
            if ($categoryRewardCollection->getSize()) {
                foreach ($categoryRewardCollection as $categoryRule) {
                    $categoryReward[] = $categoryRule->getPoints();
                }
                if (!empty($categoryReward)) {
                    $rewardpoint = max($categoryReward);
                }
            }
        }
        return $rewardpoint;
    }

    public function getCategorySpecificData($item)
    {
        $categoryIds = $item->getProduct()->getCategoryIds();
        $rewardpoint = 0;
        $categoryReward = [];
        if (is_array($categoryIds)) {
            $categoryRewardCollection = $this->_rewardcategorySpecificCollection
                                  ->create()
                                  ->addFieldToFilter('status', ['eq'=>1])
                                  ->addFieldToFilter('category_id', ['in'=>$categoryIds]);
            if ($categoryRewardCollection->getSize()) {
                $cur_Time = $this->_date->gmtDate('H:i');
                $currentTime = $this->getTimeAccordingToTimeZone($cur_Time);
                foreach ($categoryRewardCollection as $categoryRule) {
                    $categoryStartTime = $this->getTimeAccordingToTimeZone($categoryRule->getStartTime());
                    $categoryEndTime = $this->getTimeAccordingToTimeZone($categoryRule->getEndTime());
                    if ((strtotime($currentTime) >= strtotime($categoryStartTime)) &&
                    (strtotime($currentTime) <= strtotime($categoryEndTime))) {
                        $categoryReward[] = $categoryRule->getPoints();
                    }
                }
                if (!empty($categoryReward)) {
                    $rewardpoint = max($categoryReward);
                }
            }
        }
        return $rewardpoint;
    }

    public function getSubTotal($orderId)
    {
        $subTotal = 0;
        $order = $this->_orderModel->create()->load($orderId);
        $subTotal = $order->getSubtotal();

        return $subTotal;
    }

    public function getRewardBasedOnRules($amount)
    {
        $today = $this->_date->gmtDate('Y-m-d');
        $reward = 0;
        $rewardCartruleCollection = $this->_rewardcartCollection
            ->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('start_date', ['lteq' => $today])
            ->addFieldToFilter('end_date', ['gteq' => $today])
            ->addFieldToFilter('amount_from', ['lteq'=>$amount])
            ->addFieldToFilter('amount_to', ['gteq'=>$amount]);
        if ($rewardCartruleCollection->getSize()) {
            foreach ($rewardCartruleCollection as $cartRule) {
                $reward = $cartRule->getPoints();
            }
        }
        return $reward;
    }

    public function getProductData($item, $quantityWise)
    {
        $productId = $item->getProduct()->getId();
        $rewardpoint = 0;
        $qty = 0;
        $reward = $this->getProductReward($item->getProductId());
        if ($item->getOrderId() && $item->getOrderId()!=0) {
            $qty = $item->getQtyOrdered();
        } else {
            $qty = $item->getQty();
        }
        if ($quantityWise) {
            $rewardpoint = $reward * $qty;
        } else {
            $rewardpoint = $reward;
        }
        return $rewardpoint;
    }

    public function getProductReward($productId)
    {
        $reward = $this->getProductSpecificData($productId);
        if (!$reward) {
            $productCollection = $this->_rewardProductCollection->create()
                               ->addFieldToFilter('product_id', ['eq'=>$productId])
                               ->addFieldToFilter('status', ['eq'=>1]);
            if ($productCollection->getSize()) {
                foreach ($productCollection as $productData) {
                    if ($productData->getPoints()) {
                        $reward = $productData->getPoints();
                    }
                }
            }
        }
        return $reward;
    }

    public function getCategoryRewardToShow($categoryId)
    {
        list($reward, $status, $message) = $this->getCategorySpecificDataToShow($categoryId);
        if (!$status) {
            $categoryCollection = $this->_rewardcategoryCollection->create()
                           ->addFieldToFilter('category_id', ['eq'=>$categoryId])
                           ->addFieldToFilter('status', ['eq'=>1]);
            if ($categoryCollection->getSize()) {
                foreach ($categoryCollection as $categoryData) {
                    if ($categoryData->getPoints()) {
                        $reward = $categoryData->getPoints();
                        $status = false;
                        $message = '';
                    }
                }
            }
        }
        return [$reward, $status, $message];
    }

    public function getCategorySpecificDataToShow($categoryId)
    {
        $reward = 0;
        $status = false;
        $message = '';
        $categoryCollection = $this->_rewardcategorySpecificCollection->create()
                           ->addFieldToFilter('category_id', ['eq'=>$categoryId])
                           ->addFieldToFilter('status', ['eq'=>1]);
        $curTime = $this->_date->gmtDate('H:i');
        $currentTime = $this->getTimeAccordingToTimeZone($curTime);
        if ($categoryCollection->getSize()) {
            foreach ($categoryCollection as $categoryData) {
                if ($categoryData->getPoints()) {
                    $startTime = $this->getTimeAccordingToTimeZone($categoryData->getStartTime());
                    $endTime = $this->getTimeAccordingToTimeZone($categoryData->getEndTime());
                    if ((strtotime($currentTime) >= strtotime($startTime)) &&
                     (strtotime($currentTime) <= strtotime($endTime))) {
                        $reward = $productData->getPoints();
                        $status = true;
                        $message = $this->_date->gmtDate(
                            'h:i A',
                            strtotime($startTime)
                        ).' - '.$this->_date->gmtDate('h:i A', strtotime($endTime));
                    }
                }
            }
        }
        return [$reward, $status, $message];
    }

    public function getProductRewardToShow($productId)
    {
        list($reward, $status, $message) = $this->getProductSpecificDataToShow($productId);
        if (!$status) {
            $productCollection = $this->_rewardProductCollection->create()
                             ->addFieldToFilter('product_id', ['eq'=>$productId])
                             ->addFieldToFilter('status', ['eq'=>1]);
            if ($productCollection->getSize()) {
                foreach ($productCollection as $productData) {
                    if ($productData->getPoints()) {
                        $reward = $productData->getPoints();
                        $status = false;
                        $message = '';
                    }
                }
            }
        }
        return [$reward, $status, $message];
    }

    public function getProductSpecificDataToShow($productId)
    {
        $reward = 0;
        $status = false;
        $message = '';
        $productCollection = $this->_rewardproductSpecificCollection->create()
                           ->addFieldToFilter('product_id', ['eq'=>$productId])
                           ->addFieldToFilter('status', ['eq'=>1]);
        $curTime = $this->_date->gmtDate('H:i');
        $currentTime = $this->getTimeAccordingToTimeZone($curTime);
        if ($productCollection->getSize()) {
            foreach ($productCollection as $productData) {
                if ($productData->getPoints()) {
                    $startTime = $this->getTimeAccordingToTimeZone($productData->getStartTime());
                    $endTime = $this->getTimeAccordingToTimeZone($productData->getEndTime());
                    if ((strtotime($currentTime) >= strtotime($startTime)) &&
                     (strtotime($currentTime) <= strtotime($endTime))) {
                        $reward = $productData->getPoints();
                        $status = true;
                        $message = $this->_date->gmtDate(
                            'h:i A',
                            strtotime($startTime)
                        ).' - '.$this->_date->gmtDate('h:i A', strtotime($endTime));
                    }
                }
            }
        }
        return [$reward, $status, $message];
    }

    public function getProductSpecificData($productId)
    {
        $reward = 0;
        $productCollection = $this->_rewardproductSpecificCollection->create()
                           ->addFieldToFilter('product_id', ['eq'=>$productId])
                           ->addFieldToFilter('status', ['eq'=>1]);
        if ($productCollection->getSize()) {
            $cur_time = $this->_date->gmtDate('H:i');
            $currentTime = $this->getTimeAccordingToTimeZone($cur_time);
            foreach ($productCollection as $productData) {
                $startTime = $this->getTimeAccordingToTimeZone($productData->getStartTime());
                $endTime = $this->getTimeAccordingToTimeZone($productData->getEndTime());
                if ((strtotime($currentTime) >= strtotime($startTime)) &&
                 (strtotime($currentTime) <= strtotime($endTime)) &&
                $productData->getPoints()) {
                    $reward = $productData->getPoints();
                }
            }
        }
        return $reward;
    }

     /**
      * Get Attribute Options List
      *
      * @return array
      */
    public function getOptionsList()
    {
        $optionsList = ['' => 'Please Select'];
        $attribute = $this->eavConfig->getAttribute('catalog_product', $this->getAttributeCode());
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            if (isset($option['value']) && $option['value']) {
                $optionsList[$option['value']] = $option['label'];
            }
        }
        return $optionsList;
    }
    /**
     * getAttributeCode for Attribute Rule
     * @return string
     */
    public function getAttributeCode()
    {
        return  $this->scopeConfig->getValue(
            'rewardsystem/general_settings/attribute_reward',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get reward points for the cart
     * @return mixed
     */
    public function getCartReward()
    {
        $today = $this->_date->gmtDate('Y-m-d');
        $amountFrom = 0;
        $amonutTo = 0;
        $reward = 0;
        $cartAmount = $this->getCartTotal();
        $rewardCartruleCollection = $this->_rewardcartCollection
          ->create()
          ->addFieldToFilter('status', 1)
          ->addFieldToFilter('start_date', ['lteq' => $today])
          ->addFieldToFilter('end_date', ['gteq' => $today])
          ->addFieldToFilter('amount_from', ['lteq' => $cartAmount])
          ->addFieldToFilter('amount_to', ['gteq' => $cartAmount]);
        if ($rewardCartruleCollection->getSize()) {
            foreach ($rewardCartruleCollection as $cartRule) {
                $reward = $cartRule->getPoints();
                $amountFrom = $cartRule->getAmountFrom();
                $amonutTo = $cartRule->getAmountTo();
            }
        }
        return [
        'reward' => $reward,
        'amount_from' => $amountFrom,
        'amount_to' => $amonutTo
        ];
    }

    /**
     * getCartData cart Quantity for show Message on Cart Page
     *
     * @return int
     */
    public function getCartData()
    {
        return $this->cartModel->getQuote()->getItemsCount();
    }

    /**
     * getCartTotal Cart Grand Total For Show Reward Ponit on Cart Page
     *
     * @return float
     */
    public function getCartTotal()
    {
        return $this->cartModel->getQuote()->getGrandTotal();
    }
    /**
     * getOrderUrl by Order Id
     * @param  integer $orderId
     * @return string Order view Url
     */
    public function getOrderUrl($orderId = 0)
    {
        return $this->_getUrl(
            'sales/order/view',
            ['order_id'=> $orderId]
        );
    }
}
