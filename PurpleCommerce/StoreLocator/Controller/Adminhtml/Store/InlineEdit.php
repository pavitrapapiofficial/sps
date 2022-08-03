<?php

namespace PurpleCommerce\StoreLocator\Controller\Adminhtml\Store;

use Magento\Backend\App\Action\Context;
use PurpleCommerce\StoreLocator\Api\StoredetailRepositoryInterface as StoredetailRepository;
use PurpleCommerce\StoreLocator\Api\Data\StoredetailInterface;

class InlineEdit extends \Magento\Backend\App\Action
{
    protected $storedetailRepository;
    protected $jsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        StoredetailRepository $storedetailRepository
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->storedetailRepository = $storedetailRepository;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                // echo 'pppp';
                // print_r($postItems);
                foreach (array_keys($postItems) as $entityId) {
                    /** load your model to update the data */
                    $store = $this->storedetailRepository->getById($entityId);
                    try {
                        $storeData = $postItems[$entityId];
                        $extendedStoreData = $store->getData();
                        $this->setStoreData($store,$extendedStoreData,$storeData);
                        $this->storedetailRepository->save($store);
                    } catch (\Exception $e) {
                        $messages[] = "[Error:]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
    public function setStoreData(\PurpleCommerce\StoreLocator\Model\Storedetail $store, array $extendedStoreData,array $storeData){
        $store->setData(array_merge($store->getData(), $extendedStoreData,$storeData));
        return $this;
    }
}