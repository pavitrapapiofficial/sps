<?php

/**
 * @category   Webkul
 * @package    Webkul_EtsyMagentoConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Locale\Resolver;
use Webkul\GoogleShoppingFeed\Helper\Data as HelperData;
use Webkul\GoogleShoppingFeed\Logger\Logger;
use DOMDocument;
class GoogleFeed extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    
    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Magento\Framework\Locale\Resolver
     */
    private $resolver;

    /**
     * @var Webkul\GoogleShoppingFeed\Helper\Data
     */
    public $helperData;

    /**
     * @var Webkul\GoogleShoppingFeed\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Framework\App\Helper\Context $context,
     * @param StoreManagerInterface $storeManager,
     * @param Resolver $resolver,
     * @param HelperData $helperData,
     * @param Logger $logger
     * @param ProductRepositoryInterface $productRepository,
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager,
        Resolver $resolver,
        HelperData $helperData,
        Logger $logger,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->resolver = $resolver;
        $this->helperData = $helperData;
        $this->logger = $logger;
        $this->helperData->getAccessToken();
        $this->productRepository = $productRepository;

    }

    /**
     * insertFeedToGoogleShop
     * @param Google_Service_ShoppingContent_Product $productFeed
     * @return Google_Service_ShoppingContent_Product
     */
    public function insertFeedToGoogleShop($productFeed)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->create(\Psr\Log\LoggerInterface::class);
        // $logger->info('inside insertFeedToGoogleShop');
        try {
            // $logger->info('inside insertFeedToGoogleShop try');
            $accessToken = $this->helperData->getAccessToken();
            if ($accessToken) {
                // $logger->info('inside insertFeedToGoogleShop if');
                
                $config = $this->helperData->getConfigDetails();
                $client = new \Google_Client();
                $client->setAccessToken($config['oauth2_access_token']);
                $serviceShoppingContent =  new \Google_Service_ShoppingContent($client);
                $product = $serviceShoppingContent->products->insert($config['merchant_id'], $productFeed);
                $this->logger->addError('product inserted.'.$product->getId());
                return ['error' => 0, 'product' => $product];
            } else {
                // $logger->info('inside insertFeedToGoogleShop else');
                $this->logger->addError('Google feed account not authenticated.');
                return ['error' => 1, 'product' => null, 'message' => __('Google feed account not authenticated.')];
            }
        } catch (\Exception $e) {
            // $logger->info('inside insertFeedToGoogleShop catch');
            $this->logger->addError('postShopData : '.$e->getMessage());
            return ['error' => 1, 'product' => null, 'message' => $e->getMessage()];
        }
    }

    /**
     * getStoreDetailForFeed
     * @return array
     */
    public function getStoreDetailForFeed()
    {
        try {
            $store = $this->storeManager->getStore();
            $locale =  $this->resolver->getLocale();
            $locale = explode('_', $locale);
            $storeDetails = [
                'base_media_url' => $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA),
                'currency_code' => $store->getBaseCurrencyCode(),
                'language' => $locale[0],
                'country' => $locale[1]
            ];
            return $storeDetails;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
    /**
     * getPriceForConfigProduct
     * @param Magento\Catalog\Model\product $product
     * @return float
     */


    private function getPriceForFeed($product)
    {
        try {
            $price = $product->getPrice();
	  //$price = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
            if (in_array($product->getTypeId(), ['configurable', 'bundle', 'grouped'])) {
                $price = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
            }
            return $price;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    


    private function getSpecialPriceForFeed($product)
    {
        try {
            $price = $product->getPrice();
            $specialprice = 0;
	        $specialprice = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
            $specialprice = $product->getSpecialPrice();
            $this->logger->addError('sppp--'.$specialprice.'--product--'.$product->getId());
            if ($specialprice<$price) return $specialprice;
            else return 0;
            if (in_array($product->getTypeId(), ['configurable', 'bundle', 'grouped'])) {
             //   $specialprice = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
		    return 0;
        }
  	   
           
			

        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    private function getJLretailPrice($price)
    { 
      return round(2.25*$price);
    }


    /**
     * getProductForFeed
     * @param Magento\Catalog\Model\product $product
     * @param array $storeDetail
     * @return \Google_Service_ShoppingContent_Product
     */
    public function getProductForFeed($product, $storeDetail)
    {
        $this->logger->addError('suspend->'.$product->getAttributeText('suspend_google_feed'));
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $StockState = $objectManager->get('\Magento\CatalogInventory\Model\Stock\StockItemRepository');
        $logger = $objectManager->create(\Psr\Log\LoggerInterface::class);
        // $logger->info("inside helper feedproducts");
        if (in_array($product->getTypeId(), ['configurable', 'bundle', 'grouped']) || $product->getAttributeText('suspend_google_feed')=='Yes') {
            $this->logger->addError('getProductForFeed exception '.$product->getId());
             throw new LocalizedException(__("Configurable items not allowed in Google Feed. Select associated simple products only."));
            } else 
        {
            $child=$product;
            $productID = $product->getId();
            $prodStock=$StockState->get($child->getId());
            
            $productparentIDs = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($productID);
            if(isset($productparentIDs[0])){
                $product = $this->productRepository->getById($productparentIDs[0]);
            }

            // $logger->info("feedproductids---->Parent:".$product->getSku()."|Child:".$child->getSku());


            try {
                $productAttributeRepository =$objectManager->get('\Magento\Catalog\Api\ProductAttributeRepositoryInterface');
                $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
                $googleProductCategory = $this->helperData->getMappedCategory($product->getCategoryIds());
                $cost = $this->getPriceForFeed($child);
                $store = $this->storeManager->getStore();
                $productData = $this->helperData->getProductDataAsFieldMap($product);
                $childproductData = $this->helperData->getProductDataAsFieldMap($child);
                // $logger->info("ParentproductData---->Parent:".json_encode($productData));
                $logger->info("childproductData---->:".json_encode($childproductData));
                $feedProduct = new \Google_Service_ShoppingContent_Product();
                $feedProduct->setChannel("online");
                $feedProduct->setContentLanguage($storeDetail['language']);
                $feedProduct->setOfferId($childproductData['offerId']);
                $feedProduct->setTargetCountry($storeDetail['country']);
                $feedProduct->setTitle($productData['title']);
                $string = str_replace('"', '', $productData['description']);
                $this->logger->addError('');
                $expireDate = $objDate->gmtDate('Y-m-d', strtotime("+7 days"));
                $feedProduct->setExpirationDate($expireDate);
                // Nitin Edits
                
                // $str = substr($string, strpos($string, 'SELECTED FEATURES') + 1);
                // $string =$str;
                $string=html_entity_decode($string,ENT_QUOTES,'UTF-8');
                $dom = new DOMDocument();
                @$dom->loadHTML($string);
                $images = $dom->getElementsByTagName('img');
                $i=0;
                foreach ($images as $k=>$image) {
                $old_src = $image->getAttribute('src');
                
                if($k==0){
                    $new_src = 'https://www.sandpipershoes.com/pub/media/FeatureLogos/Touch-Fastening-100.png';
                }else if($k==1){
                    $new_src = 'https://www.sandpipershoes.com/pub/media/FeatureLogos/2in1-Fitting-100.png';
                }else if($k==2){
                    $new_src = 'https://www.sandpipershoes.com/pub/media/FeatureLogos/Suitable-For-Orthotics-100.png';
                }else if($k==3){
                    $new_src = 'https://www.sandpipershoes.com/pub/media/FeatureLogos/Sanitised-For-Extra-Hygiene-100.png';
                }
                
                $image->setAttribute('src', $new_src);
                $image->setAttribute('data-src', $old_src);
                }
                
                $string = $dom->saveHTML();
                $feedProduct->setDescription($string);

                

                
                if($prodStock->getIsInStock()){
                    $availability = "in stock";
                }else{
                    $availability = "out of stock";
                }

                // end edit

                // $availability = $child->getQuantityAndStockStatus() ? "in stock" : "out of stock";
                $feedProduct->setAvailability($availability);
                $link = $store->getBaseUrl().$product->getUrlKey().'.html';
                
                //-------->size/color attribute to append in url


                $sizename=$childproductData['sizes'];
                $colorname = $childproductData['color'];


                $attr = $product->getResource()->getAttribute('color');
                if ($attr->usesSource()) {
                      $color_option_id = $attr->getSource()->getOptionId($colorname);
                }

                $attrs = $product->getResource()->getAttribute('Sizes');
                if ($attrs->usesSource()) {
                      $size_option_id = $attrs->getSource()->getOptionId($sizename);
                }
                

                $attribute = $productAttributeRepository->get('Sizes');
                $sizeId = $attribute->getAttributeId();

                $attributeColor = $productAttributeRepository->get('color');
                $colorId = $attributeColor->getAttributeId();

                // $this->logger->addError("sizeid".$sizeId.'--colorid--'.$colorId);

                // $this->logger->addError("sizename".$sizename.'--id--'.$size_option_id);
                // $this->logger->addError('$colorname'.$colorname.'--id--'.$color_option_id);

                $query_string = '?s=g#';
                if(!empty($color_option_id) && !empty($size_option_id)){
                    $query_string .= $colorId.'='.$color_option_id.'&'.$sizeId.'='.$size_option_id;
                } else if(!empty($size_option_id) && empty($color_option_id)){
                    $query_string .= $sizeId.'='.$size_option_id;
                } else if(empty($size_option_id) && !empty($color_option_id)){
                    $query_string .= $colorId.'='.$color_option_id;
                } else{
                    $query_string='';
                }

                $link = $store->getBaseUrl().$product->getUrlKey().'.html'.$query_string;
                $this->logger->addError('querystring->'.$query_string);
                $this->logger->addError('link->'.$link);
                //----------->




                // if($product->getSku()=='FORTON-IVORY.PR-6'){
                //     $link = $store->getBaseUrl().$product->getUrlKey().'.html#93=5615&180=5447';
                // } else if ($product->getSku()=='FORTON-IVORY.PR-6.5') {
                //     $link = $store->getBaseUrl().$product->getUrlKey().'.html#93=5615&180=5448';
                // } else if($product->getSku()=='FORTON-CHER.PRI-8') {
                //     $link = $store->getBaseUrl().$product->getUrlKey().'.html#93=5617&180=5450';
                // } else if($product->getSku()=='FORTON-CHER.PRI-7') {
                //     $link = $store->getBaseUrl().$product->getUrlKey().'.html#93=5617&180=5449';
                // } else if($product->getSku()=='FORTON-NAVY.PRI-4') {
                //     $link = $store->getBaseUrl().$product->getUrlKey().'.html#93=6002&180=5445';
                // } else if($product->getSku()=='FORTON-NAVY.PRI-3') {
                //     $link = $store->getBaseUrl().$product->getUrlKey().'.html#93=6002&180=5444';
                // }
                
                $feedProduct->setLink($link);
                if ($productData['brand']) {
                    $feedProduct->setBrand($productData['brand']);
                }
                if ($productData['color']) {
                    $feedProduct->setColor($productData['color']);
                }else{
                    $feedProduct->setColor($childproductData['color']);
                }

                $feedProduct->setGender($productData['gender']);
                $customProduct = $product->getGfCustomProduct();
                if (isset($childproductData['gtin']) && !$customProduct) {
                    $feedProduct->setGtin($childproductData['gtin']);
                    /** $feedProduct->setMpn($productData['mpn']); */
                } 
                $feedProduct->setAgeGroup($productData['ageGroup']);
                if (isset($childproductData['sizes'])) {
                    $a=[];
                    array_push($a,$childproductData['sizes']);
                    // $feedProduct->setSizes($a);
                    $feedProduct->setSizes($childproductData['sizes']);
                }
                if (isset($childproductData['sizeType'])) {
                    $feedProduct->setSizes($childproductData['sizeType']);
                }
                if (isset($productData['sizeSystem'])) {
                    $feedProduct->setSizeSystem($productData['sizeSystem']);
                }

                if(isset($productparentIDs[0])){ 
                    $feedProduct->setItemGroupId($product->getSku());
                }

                if(isset($childproductData['imageLink'])){
                    $imageLink =  $childproductData['imageLink'] == 'no_selection' ?
                    '/placeholder/image.jpg' : $childproductData['imageLink'];
                }else{
                    $imageLink =  $productData['imageLink'] == 'no_selection' ?
                    '/placeholder/image.jpg' : $productData['imageLink'];
                }
                $feedProduct->setImageLink($storeDetail['base_media_url'] . 'catalog/product' . $imageLink);
                $feedProduct->setCondition($productData['condition']);

                $feedProduct->setTitle($product->getName());

                if (isset($productData['title'])){
                        $feedProduct->setTitle($productData['title']);
                }

                $taxShip = $productData['taxShip'] == 'Yes'  ? true : false;
                $shipTaxGlobal = $this->scopeConfig->getValue('googleshoppingfeed/default_config/tax_on_ship');
                $shipApplied = $taxShip || ($productData['taxShip'] != 'No' && $shipTaxGlobal) ? true : false;
                $tax = new \Google_Service_ShoppingContent_ProductTax();
                $tax->setCountry($storeDetail['country']);
                $productData['taxRate'] = $productData['taxRate'] ? $productData['taxRate'] :
                    $this->scopeConfig->getValue('googleshoppingfeed/default_config/tax_rate');
                $tax->setRate($productData['taxRate']);
                $tax->setTaxShip($shipApplied);
                //$feedProduct->setTaxes([$tax]);
                if ($googleProductCategory) {
                    $feedProduct->setGoogleProductCategory($googleProductCategory);
                    /** $feedProduct->setProductTypes($googleProductCategory); */
                }
                if (isset($productData['shippingWeight']) && $productData['shippingWeight']) {
                    $shippingWeight = new \Google_Service_ShoppingContent_ProductShippingWeight();
                    $shippingWeight->setValue($productData['shippingWeight']);
                    $shippingWeight->setUnit($this->scopeConfig->getValue('googleshoppingfeed/default_config/weight_unit'));
                    $feedProduct->setShippingWeight($shippingWeight);
                }

                $price = new \Google_Service_ShoppingContent_Price();
                $price->setCurrency($storeDetail['currency_code']);
                
                
                
                if($product->getIsVatexempt()==0){
                    $this->logger->addError('actual price->'.$cost);
                    $cost = $cost*1.2;
                }
                $price->setValue($cost);
                $feedProduct->setPrice($price);
                // $this->logger->addError('actual price->'.$cost);
                $this->logger->addError('vat->'.$product->getIsVatexempt());
                
                $special=$this->getSpecialPriceForFeed($child);
                $this->logger->addError('<-sp->'.$special);
                
                if ($special>0) {
                    $special=$child->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount();
                    $pricespecial = new \Google_Service_ShoppingContent_Price();
                    
                    $pricespecial->setCurrency($storeDetail['currency_code']);
                    if($product->getIsVatexempt()==0){
                        $special = $special*1.2;
                    }
                    $pricespecial->setValue($special);
                    $feedProduct->setSalePrice($pricespecial);
                    $this->logger->addError('special price->'.$special);
                }
                
                // $this->logger->addError('without tax->'.$withoutvat);
                $this->logger->addError('success product pa->'.$product->getId());
                $this->logger->addError('success product ch->'.$child->getId());
                return $feedProduct;
            } catch (\Exception $e) {
                $this->logger->addError($e->getMessage().'--for product->'.$child->getId());
                throw new LocalizedException(__($e->getMessage()));
            }
        }
    }

    /**
     * deleteFeedFromGoogleShop
     * @param string $feedId
     * @param array $shopConfig
     */
    public function deleteFeedFromGoogleShop($feedId, $shopConfig = null)
    {
        try {
            $config = $shopConfig ? $shopConfig : $this->helperData->getConfigDetails();
            $client = new \Google_Client();
            $client->setAccessToken($config['oauth2_access_token']);
            $serviceShoppingContent =  new \Google_Service_ShoppingContent($client);
            $product = $serviceShoppingContent->products->delete($config['merchant_id'], $feedId);
            return $product;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
