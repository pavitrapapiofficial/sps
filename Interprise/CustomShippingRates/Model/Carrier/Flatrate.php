<?php
namespace Interprise\CustomShippingRates\Model\Carrier;

use Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;

/**
 * Flat rate shipping model
 *
 * @api
 * @since 100.0.2
 */
class Flatrate extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'flatrate';
    protected $quoteRepository;
    protected $_coreSession;
    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface CookieManagerInterface
     */
    private $cookieManager;
 
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var ItemPriceCalculator
     */
    private $itemPriceCalculator;
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param ItemPriceCalculator $itemPriceCalculator
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator $itemPriceCalculator,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->_coreSession = $coreSession;
        $this->itemPriceCalculator = $itemPriceCalculator;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // echo '<pre>';
        // var_dump($request);

        $freeBoxes = $this->getFreeBoxesCount($request);
        $this->setFreeBoxes($freeBoxes);

        /** @var Result $result */
        $result = $this->_rateResultFactory->create();

        $shippingPrice = $this->getShippingPrice($request, $freeBoxes);

        //++++++++++++ custom code - Nitin edit

        
        #---->getting customer related date
        $customerId=$this->_customerSession->getCustomer()->getId();
        $customerGroup=$this->_customerSession->getCustomer()->getGroupId();
        $customerRepository = $objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface');
        $addressFactory = $objectManager->get('Magento\Customer\Api\AddressRepositoryInterface');
        $customerData = $customerRepository->getById($customerId);
        $pricetype=$customerData->getCustomAttribute('interprise_defaultprice')->getValue();
        $shippingAddressId = $customerData->getDefaultShipping();
        $checkoutsession = $objectManager->get('Magento\Checkout\Model\Session');
        $this->_coreSession->start();
        
        $addressId =  $this->_coreSession->getCustomerAddressCurrentId();
        if($addressId == 0){
            $shppingvalue = '*';
        }else{
            $this->_coreSession->unsCustomerAddressCurrentId();
            $addressId=explode('-',$addressId);
            $addressId=$addressId[0];
            $shippingAddress = $addressFactory->getById($addressId);
            $shppingvalue = $shippingAddress->getCustomAttribute('interprise_shippingmethod')->getValue();
            if(strtolower($shppingvalue)!='rm-free' || $shppingvalue=='' || $shppingvalue==null){
                $shppingvalue = '*';
            }
        }
        

        
        


        #----->Getting cart item data
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $items = $cart->getQuote()->getAllItems();
        $weight = 0;
        $pairs = $cart->getQuote()->getItemsQty();
        $currentquoteid = $checkoutsession->getQuote()->getId();
        $quote = $objectManager->create('Magento\Quote\Model\Quote')->load($currentquoteid);


        // echo '<pre>';
        // var_dump($quote->getData());
        // die;
        // sleep(5);
        
        foreach($items as $item) {
            $weight += ($item->getWeight() * $item->getQty()) ;        
        }
        
        if($weight<=2){
            $tbweight=2;
        }else{
            $tbweight=999999;
        }

        if($pairs<=5){
            $tbpairs=5;
        }else{
            $tbpairs=99999;
        }

        #-----> fetch data from custom table
        $model=$objectManager->create('Interprise\CustomShippingRates\Model\CustomShipping');
        $datacollection=$model->getCollection()
        ->addFieldToFilter('customerid', $customerId)
        ->addFieldToFilter('defaultshipping', $shppingvalue)
        ->addFieldToFilter('weight', $tbweight)
        ->setPageSize(10);
        $ids='';


        #------>Applying charges and method according to above data
        if(count($datacollection)>0){
            foreach ($datacollection as $item) {
                $ids .='val-'.$item->getMagentoprice().'-';
                $shippingPrice = $item->getMagentoprice();
                $titlecustom = $item->getMagentomethod();
                $ipmethod = $item->getIpmethod();
                $carriertitle = 'IF-shipping->'.$shppingvalue.'<-addressid->'.$addressId.'<-tbpairs->'.$tbpairs.'<-tbweight->'.$tbweight.'<-pricing->'.$pricetype.'<-ipmethod->'.$ipmethod;
            }
        }else{
            if(strtolower($pricetype)=='wholesale'){
                $datacollection=$model->getCollection()
                ->addFieldToFilter('pricing', $pricetype)
                ->addFieldToFilter('defaultshipping', $shppingvalue)
                ->addFieldToFilter('weight', $tbweight)
                ->setPageSize(10);
            }else{
                $datacollection=$model->getCollection()
                ->addFieldToFilter('pricing', $pricetype)//pairs
                ->addFieldToFilter('weight', $tbweight)
                ->addFieldToFilter('pairs', $tbpairs)
                ->setPageSize(10);
            }
            
            foreach ($datacollection as $item) {
                $shippingPrice = $item->getMagentoprice();
                $titlecustom = $item->getMagentomethod();
                $ipmethod = $item->getIpmethod();
                $carriertitle = 'ELSE-shipping->'.$shppingvalue.'<-addressid->'.$addressId.'<-tbpairs->'.$tbpairs.'<-tbweight->'.$tbweight.'<-pricing->'.$pricetype.'<-ipmethod->'.$ipmethod;
            }
        }
        
        
        
        

        
        if ($shippingPrice !== false) {
            // $carriertitle='';
            $quote->setIsShippingmethod($ipmethod);
            $quote->save();
            // $quote->setData('is_shippingmethod', $ipmethod); // Fill data
            // $this->quoteRepository->save($quote); // Save quote
            $method = $this->createResultMethod($shippingPrice);
            $method->setCarrierTitle($carriertitle);
            $method->setMethodTitle($titlecustom);
            $result->append($method);
        }
        
        //++++++++++++ custom code - Nitin edit Ends


        return $result;
    }

    /**
     * Get count of free boxes
     *
     * @param RateRequest $request
     * @return int
     */
    private function getFreeBoxesCount(RateRequest $request)
    {
        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    $freeBoxes += $this->getFreeBoxesCountFromChildren($item);
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }
        return $freeBoxes;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * Returns shipping price
     *
     * @param RateRequest $request
     * @param int $freeBoxes
     * @return bool|float
     */
    private function getShippingPrice(RateRequest $request, $freeBoxes)
    {
        $shippingPrice = false;

        $configPrice = $this->getConfigData('price');
        if ($this->getConfigData('type') === 'O') {
            // per order
            $shippingPrice = $this->itemPriceCalculator->getShippingPricePerOrder($request, $configPrice, $freeBoxes);
        } elseif ($this->getConfigData('type') === 'I') {
            // per item
            $shippingPrice = $this->itemPriceCalculator->getShippingPricePerItem($request, $configPrice, $freeBoxes);
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false && $request->getPackageQty() == $freeBoxes) {
            $shippingPrice = '0.00';
        }
        return $shippingPrice;
    }

    /**
     * Creates result method
     *
     * @param int|float $shippingPrice
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function createResultMethod($shippingPrice)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier('flatrate');
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod('flatrate');
        $method->setMethodTitle($this->getConfigData('name'));
        // $shippingPrice = 80;
        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);
        return $method;
    }

    /**
     * Returns free boxes count of children
     *
     * @param mixed $item
     * @return mixed
     */
    private function getFreeBoxesCountFromChildren($item)
    {
        $freeBoxes = 0;
        foreach ($item->getChildren() as $child) {
            if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                $freeBoxes += $item->getQty() * $child->getQty();
            }
        }
        return $freeBoxes;
    }
}