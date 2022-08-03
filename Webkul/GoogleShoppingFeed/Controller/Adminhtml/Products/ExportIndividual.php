<?php
/**
 * GoogleShoppingFeed Admin Product Create Controller.
 * @category  Webkul
 * @package   Webkul_GoogleShoppingFeed
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\Products;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Json\Helper\Data as JsonHelperData;
use Webkul\GoogleShoppingFeed\Helper\GoogleFeed as HelperGoogleFeed;
use Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory;

class ExportIndividual extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelperData;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Webkul\GoogleShoppingFeed\Helper\GoogleFeed
     */
    private $helperGoogleFeed;

    /**
     * @var \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory
     */
    private $feedMap;

    /**
     * @param Context $context,
     * @param JsonHelperData $jsonHelperData,
     * @param DateTime $dateTime,
     * @param ProductRepositoryInterface $productRepository,
     * @param HelperGoogleFeed $helperGoogleFeed,
     * @param GoogleFeedMapFactory $feedMap
     */
    public function __construct(
        Context $context,
        JsonHelperData $jsonHelperData,
        DateTime $dateTime,
        ProductRepositoryInterface $productRepository,
        HelperGoogleFeed $helperGoogleFeed,
        GoogleFeedMapFactory $feedMap
    ) {
        parent::__construct($context);
        $this->jsonHelperData = $jsonHelperData;
        $this->dateTime = $dateTime;
        $this->productRepository = $productRepository;
        $this->helperGoogleFeed = $helperGoogleFeed;
        $this->feedMap = $feedMap;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $logger = $objectManager->create(\Psr\Log\LoggerInterface::class);

        try {
            $accessToken = $this->helperGoogleFeed->helperData->getAccessToken();
            if ($this->getRequest()->isPost() && $accessToken) {
                $requestData = $this->getRequest()->getParams();
                $logger->info("requestData---->".json_encode($requestData));
                $mappedPro = $this->feedMap->create()->getCollection()
                                    ->addFieldToFilter('mage_pro_id', ['eq' => $requestData['product']])
                                    ->setPageSize(1)->getFirstItem();
                //$mappedPro = empty($mappedPro) ? [0] : $mappedPro;
                if (!isset($requestData['finish']) /*&& !in_array($requestData['product'], $mappedPro)*/) {
                    $product = $this->productRepository->getById($requestData['product']);
                    $storeDetailForFeed = $this->helperGoogleFeed->getStoreDetailForFeed();
                    $feedProduct = $this->helperGoogleFeed->getProductForFeed($product, $storeDetailForFeed);
                    $logger->info("feedProduct--->".json_encode($feedProduct));
                    $expireDate = $this->dateTime->gmtDate('Y-m-d H:i:s', strtotime("+30 days"));
                    $items = $this->helperGoogleFeed->insertFeedToGoogleShop($feedProduct);
                    if ($items['error'] == 0) {
                        $syncsFeeds = [
                            'feed_id' => $items['product']['id'],
                            'mage_pro_id' => $product->getEntityId(),
                            'expired_at' => $expireDate
                        ];
                        if ($mappedPro->getEntityId()) {
                            $mappedPro->setExpiredAt($expireDate);
                            $mappedPro->setFeedId($items['product']['id']);
                        } else {
                            $mappedPro->setData($syncsFeeds)->save();
                        }
                        $mappedPro->save();
                        $result = ['error' => 0, 'total' => 1];
                    } else {
                        /** $this->jsonHelperData->JsonDecode($items['message']) */
                        $errorMessage = json_decode($items['message']) != null ?
                                        json_decode($items['message'], true) : $items['message'];
                        $errorMessage = is_array($errorMessage) ? $errorMessage['error']['message'] : $errorMessage;
                        $result = [
                            'error' => 1,
                            'total' => 1,
                            'message' => $errorMessage.__('; Store Product ID :- %1', $requestData['product'])
                        ];
                    }
                } elseif (isset($requestData['finish'])) {
                    $total = (int) $requestData['count'] - (int) $requestData['skip'];
                    $msg = '<div class="wk-mu-success wk-mu-box">'
                                .__('Total %1 Product(s) Created.', $total)
                                .'</div>';
                    $msg .= '<div class="wk-mu-note wk-mu-box">'.__('Finished Execution.').'</div>';
                    $result = ['error' => 0, 'message' => $msg];
                }
            } else {
                $result = ['error' => 1, 'message' => __('Invalid request / Google feed account not authenticated.')];
            }
            $this->getResponse()->representJson($this->jsonHelperData->jsonEncode($result));
        } catch (\Exception $e) {
            $result = ['error' => 1, 'message' => $e->getMessage()];
            $this->getResponse()->representJson($this->jsonHelperData->jsonEncode($result));
        }
    }

    /**
     * Check product import permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_GoogleShoppingFeed::product_export');
    }
}
