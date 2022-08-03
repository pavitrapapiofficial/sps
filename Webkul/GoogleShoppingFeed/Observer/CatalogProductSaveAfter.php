<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Observer;

use Magento\Framework\Event\ObserverInterface;

class CatalogProductSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory
     */
    private $googleFeedMap;

    /**
     * @var \Webkul\GoogleShoppingFeed\Helper\GoogleFeed $helperGoogleFeed
     */
    private $helperGoogleFeed;

    /**
     * @var \Webkul\GoogleShoppingFeed\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager,
     * @param \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory $googleFeedMap,
     * @param \Webkul\GoogleShoppingFeed\Helper\GoogleFeed $helperGoogleFeed
     * @param \Webkul\GoogleShoppingFeed\Logger\Logger $logger,
     */

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory $googleFeedMap,
        \Webkul\GoogleShoppingFeed\Helper\GoogleFeed $helperGoogleFeed,
        \Webkul\GoogleShoppingFeed\Logger\Logger $logger
    ) {
        $this->messageManager = $messageManager;
        $this->googleFeedMap = $googleFeedMap;
        $this->helperGoogleFeed = $helperGoogleFeed;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $accessToken = $this->helperGoogleFeed->helperData->getAccessToken();
            if ($accessToken) {
                $product = $observer->getProduct();
                $googleFeedMap = $this->googleFeedMap->create()->getCollection()
                                        ->addFieldToFilter('mage_pro_id', ['eq'=> $product->getId()])
                                        ->setPageSize(1)->getFirstItem();
                // insert/update feed on google shop
                $storeDetailForFeed = $this->helperGoogleFeed->getStoreDetailForFeed();
                $feedProduct = $this->helperGoogleFeed->getProductForFeed($product, $storeDetailForFeed);
                $items = $this->helperGoogleFeed->insertFeedToGoogleShop($feedProduct);
                if (($items['error'] == 0) && !$googleFeedMap->getEntityId() && ($product->getVisibility() != 1)) {
                    $syncsFeeds = ['feed_id' => $items['product']['id'], 'mage_pro_id' => $product->getEntityId()];
                    $mappedFeed = $this->googleFeedMap->create();
                    $mappedFeed->setData($syncsFeeds);
                    $mappedFeed->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->addError('CatalogProductSaveAfter- : '. $e->getMessage());
            $this->messageManager->addNotice($e->getMessage());
        }
    }
}
