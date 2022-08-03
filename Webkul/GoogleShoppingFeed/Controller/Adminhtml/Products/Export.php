<?php
/**
 * GoogleShoppingFeed Admin Product Create Controller.
 * @category  Webkul
 * @package   Webkul_GoogleShoppingFeed
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\Products;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Json\Helper\Data as JsonHelperData;
use Webkul\GoogleShoppingFeed\Helper\GoogleFeed as HelperGoogleFeed;
use Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory;
use Webkul\GoogleShoppingFeed\Model\Storage\DbStorage;

class Export extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelperData;

    /**
     * @var \Webkul\GoogleShoppingFeed\Logger\Logger
     */
    private $logger;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

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
     * @var \Webkul\GoogleShoppingFeed\Model\Storage\DbStorage
     */
    private $dbStorage;

    /**
     * @param Context $context,
     * @param JsonHelperData $jsonHelperData,
     * @param SearchCriteriaBuilder $searchCriteriaBuilder,
     * @param ProductRepositoryInterface $productRepository,
     * @param HelperGoogleFeed $helperGoogleFeed,
     * @param \Webkul\GoogleShoppingFeed\Logger\Logger $logger,
     * @param GoogleFeedMapFactory $feedMap,
     * @param DbStorage $dbStorage
     */
    public function __construct(
        
        Context $context,
        JsonHelperData $jsonHelperData,
        DateTime $dateTime,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository,
        HelperGoogleFeed $helperGoogleFeed,
        \Webkul\GoogleShoppingFeed\Logger\Logger $logger,
        GoogleFeedMapFactory $feedMap,
        \Magento\Framework\App\State $appstate,
        DbStorage $dbStorage
    ) {
        parent::__construct($context);
        
        $this->jsonHelperData = $jsonHelperData;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->helperGoogleFeed = $helperGoogleFeed;
        $this->feedMap = $feedMap;
        $this->dbStorage = $dbStorage;
        $this->state                    = $appstate;
    }

    public function execute(){
        // init_set('max_execution_time' , 0);
        $this->logger->addError('custom cron inside execute');
        if(isset($_GET['master_id']) && $_GET['master_id']==4){
            // echo '<br/>Only order post';
            // $this->logger->addError('inside 1st If');
            $this->state->emulateAreaCode(
                \Magento\Framework\App\Area::AREA_FRONTEND,
                [$this, "executeCallBack"]
            );
        }
        // $this->logger->addError('outside 1st If');
        $this->state->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            [$this, "executeCallBack"]
        );
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function executeCallBack()
    {
        // $this->logger->addError('inside executeCallBack');
        try {
            $accessToken = $this->helperGoogleFeed->helperData->getAccessToken();
            // $this->logger->addError('inside executeCallBack try '. $accessToken);
            if ($accessToken) {
                // $this->logger->addError('inside executeCallBack If');
                $mappedPro = $this->feedMap->create()->getCollection()->getColumnValues('mage_pro_id');
                $mappedPro = empty($mappedPro) ? [0] : $mappedPro;
                $data = $this->getRequest()->getParams();
                $searchCriteria = $this->searchCriteriaBuilder->addFilter('visibility', 1, 'eq')
                                            ->addFilter('type_id','simple', 'eq')
                                            ->addFilter('status', 1, 'eq')
                                            ->addFilter('attribute_set_id',4,'eq')->create();
                                            // ->setCurrentPage(1)->setPageSize(99999)->create();//// ->addFilter('entity_id', $mappedPro, 'nin')
                $productsList = $this->productRepository->getList($searchCriteria)->getItems();
                $this->logger->addError('product count '.count($productsList));
                $syncsFeeds = [];
                $total = 0;
                $c=0;
                $logmsg='';
                if (!empty($productsList) && !isset($data['finish'])) {
                    // $this->logger->addError('inside executeCallBack 2nd If');
                    //$expireDate = $this->dateTime->gmtDate('Y-m-d H:i:s', strtotime("+30 days"));
                    $items = [];
                    $storeDetailForFeed = $this->helperGoogleFeed->getStoreDetailForFeed();
                    
                    // $this->logger->addError('inside executeCallBack - storedetail '.count($storeDetailForFeed));
                    foreach ($productsList as $product) {
                        // $this->logger->addError('inside executeCallBack loop '.$product->getSku().'->');
                        // $this->logger->addError('should skip '.$product->getAttributeText('suspend_google_feed'));
                        if($product->getAttributeText('suspend_google_feed')=='No'){
                            $this->logger->addError('before getProductForFeed'.$product->getId());
                            $feedProduct = $this->helperGoogleFeed->getProductForFeed($product, $storeDetailForFeed);
                            $this->logger->addError('after getProductForFeed'.$product->getId());
                            $expireDate = $this->dateTime->gmtDate('Y-m-d H:i:s', strtotime("+7 days"));
                            // echo "<pre>";
                            // print_r($feedProduct);
                            // echo "</pre>";
                            // die;
                            $this->logger->addError('before insertFeedToGoogleShop'.$product->getId());
                            $items = $this->helperGoogleFeed->insertFeedToGoogleShop($feedProduct);
                            $this->logger->addError('after insertFeedToGoogleShop'.$product->getId());
                            if ($items['error'] == 0) {
                                $logmsg = 'SN->'.$c.'->SKU->'.$product->getSku().'->pID->'.$product->getId().'->isSuspend->'.$product->getAttributeText('suspend_google_feed').'->Success->';
                                $total++;
                                $syncsFeeds[] = [
                                    'feed_id' => $items['product']['id'],
                                    'mage_pro_id' => $product->getEntityId(),
                                    'expired_at' => $expireDate
                                ];
                                $result = ['error' => 0, 'total' => $total];
                            } else {
                                // $this->logger->addError('inside executeCallBack loop else');
                                $errorMessage = $this->jsonHelperData->JsonDecode($items['message']);
                                $logmsg = 'SN->'.$c.'->SKU->'.$product->getSku().'->pID->'.$product->getId().'->isSuspend->'.$product->getAttributeText('suspend_google_feed').'->Failed->'.'->ErrorMsg->'.$errorMessage['error']['message'];
                                $total++;
                                
                                $result = ['error' => 1, 'total' => $total, 'message' => $errorMessage['error']['message']];
                            }
                        }
                        $c++;
                        $this->logger->addError($logmsg);
                    }
                    
                    if (!empty($syncsFeeds)) {
                        $this->dbStorage->insertMultiple($syncsFeeds, 'google_feed_product_map');
                    }
                } else {
                    $this->logger->addError('inside executeCallBack else');
                    $total = (int) $data['count'] - (int) $data['skip'];
                    $msg = '<div class="wk-mu-success wk-mu-box">'.__('Total Product(s) Created.').'</div>';
                    $msg .= '<div class="wk-mu-note wk-mu-box">'.__('Finished Execution.').'</div>';
                    $result['message'] = $msg;
                }
            } else {
                $this->logger->addError('Invalid request Google feed account not authenticated.');
                $result = ['error' => 1, 'message' => __('Invalid request / Google feed account not authenticated.')];
            }
            $this->getResponse()->representJson($this->jsonHelperData->jsonEncode($result));
        } catch (\Exception $e) {
            $this->logger->addError('inside executeCallBack catch');
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
