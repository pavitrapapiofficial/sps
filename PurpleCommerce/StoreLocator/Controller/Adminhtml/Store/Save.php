<?php

namespace PurpleCommerce\StoreLocator\Controller\Adminhtml\Store;


use Magento\Backend\App\Action\Context;
use PurpleCommerce\StoreLocator\Helper\Data as HelperData;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use PurpleCommerce\StoreLocator\Api\StorerecordRepositoryInterface;
use PurpleCommerce\StoreLocator\Api\StoredetailRepositoryInterface;
use PurpleCommerce\StoreLocator\Api\Data\StorerecordInterfaceFactory;
use PurpleCommerce\StoreLocator\Api\Data\StoredetailInterfaceFactory;
/**
 * Class PurpleCommerce\StoreLocator\Controller\Adminhtml\Store\Save
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var DataObjectHelper
     */
    protected $_dataObjectHelper;
    
    /**
     * @var HelperData
     */
    protected $helperData;

     /**
     * @var \PurpleCommerce\StoreLocator\Api\StoredetailRepositoryInterface;
     */
    protected $_storeDetailRepository;

    /**
     * @var \PurpleCommerce\StoreLocator\Api\StorerecordRepositoryInterface;
     */
    protected $_storeRecordRepository;
    /**
     * @var \PurpleCommerce\StoreLocator\Api\Data\StorerecordInterfaceFactory;
     */
    protected $_storeRecordInterface;
    /**
     * @var \PurpleCommerce\StoreLocator\Api\StorerecordRepositoryInterface;
     */
    protected $_storeDetailInterface;
    
    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        StorerecordInterfaceFactory $storeRecordInterface,
        StoredetailInterfaceFactory $storeDetailInterface,
        StorerecordRepositoryInterface $storeRecordRepository,
        StoredetailRepositoryInterface $storeDetailRepository,
        DataPersistorInterface $dataPersistor,
        DataObjectHelper $dataObjectHelper,
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
        $this->_storeRecordRepository = $storeRecordRepository;
        $this->_storeDetailRepository = $storeDetailRepository;
        $this->_storeRecordInterface = $storeRecordInterface;
        $this->_storeDetailInterface = $storeDetailInterface;
        $this->dataPersistor = $dataPersistor;
        $this->_dataObjectHelper = $dataObjectHelper;
        parent::__construct($context);
    }

    /**
     *  {@inheritDoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        // $data['locatorid']=1;
        // echo '<pre>';
        // print_r($data);
        // die;
        if ($data) {
            try {
                $this->dataPersistor->set('details', $data);
                $this->setDataFromAdmin($data);
                $this->dataPersistor->clear('details');
                $this->messageManager->addSuccessMessage(__('Details is saved successfully!!'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                echo $e->getMessage();die('errorMessage'); 
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the details.')
                );
            }
            return $resultRedirect->setPath('*/*/new');
        }
        return $resultRedirect->setPath('*/*/');
    }


    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('PurpleCommerce_StoreLocator::storelocator');
    }

    public function setDataFromAdmin($storeData) {
        $recordDetail = [
            'storename' => $storeData['storename'],
            'add1' => $storeData['add1'],
            'add2' => $storeData['add2'],
            'region' => $storeData['region'],
            'postcode' => $storeData['postcode'],
            'city' => $storeData['city'],
            'country' => $storeData['country'],
            'phone' => $storeData['phone'],
            'link' => $storeData['link'],
            'lat' => $storeData['lat'],
            'miscl' => $storeData['miscl'],
            'locatorid' => $storeData['locatorid'],
            'lng' => $storeData['lng']
        ];

        $dataObjectRecordDetail = $this->_storeDetailInterface->create();
        $this->_dataObjectHelper->populateWithArray(
            $dataObjectRecordDetail,
            $recordDetail,
            \PurpleCommerce\StoreLocator\Api\Data\StoredetailInterface::class
        );
        // echo 'tada';
        // echo '<pre>';
        // print_r($storeData);
        // die;
        // $dataObjectRecordDetail = $this->_storeDetailInterface->create();
        $this->saveDataStoreDetail($dataObjectRecordDetail);

    }

    public function saveDataStoreDetail($completeDataObject)
    {
        try {
            $this->_storeDetailRepository->save($completeDataObject);
        } catch (\Exception $e) {
            throw new LocalizedException(
                __(
                    $e->getMessage()
                )
            );
        }
    }
}
