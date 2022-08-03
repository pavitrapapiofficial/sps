<?php

namespace PurpleCommerce\StoreLocator\Model;

use PurpleCommerce\StoreLocator\Api\Data;
use PurpleCommerce\StoreLocator\Api\StoredetailRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use PurpleCommerce\StoreLocator\Model\ResourceModel\Storedetail as StoreDetailResource;
use PurpleCommerce\StoreLocator\Model\ResourceModel\Storedetail\CollectionFactory as StoreDetailCollection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class TimeslotConfigRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoredetailRepository implements StoredetailRepositoryInterface
{
    /**
     * @var ResourceBlock
     */
    protected $resource;

    /**
     * @var BlockFactory
     */
    protected $storeDetailFactory;

    /**
     * @var BlockCollectionFactory
     */
    protected $storeDetailCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceTimeSlotConfig $resource
     * @param TimeSlotConfigFactory $timeSlotConfigFactory
     * @param Data\PreorderCompleteInterfaceFactory $dataTimeslotConfigFactory
     * @param TimeSlotConfigCollectionFactory $timeSlotCollectionFactory
     * @param Data\PreorderCompleteSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreDetailResource $resource,
        StoredetailFactory $storeDetailFactory,
        StoreDetailCollection $storeDetailCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->storeDetailFactory = $storeDetailFactory;
        $this->storeDetailCollectionFactory = $storeDetailCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Preorder Complete data
     *
     * @param \Webkul\MarketplacePreorder\Api\Data\TimeslotConfigInterface $timeSlotConfig
     * @return PreorderComplete
     * @throws CouldNotSaveException
     */
    public function save(Data\StoredetailInterface $storeDetail)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $storeDetail->setStoreId($storeId);
        try {
            $this->resource->save($storeDetail);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $storeDetail;
    }

    /**
     * Load Preorder Complete data by given Block Identity
     *
     * @param string $id
     * @return PreorderComplete
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $storeDetail = $this->storeDetailFactory->create();
        $this->resource->load($storeDetail, $id);
        if (!$storeDetail->getId()) {
            throw new NoSuchEntityException(__('Time Slot with id "%1" does not exist.', $id));
        }
        return $storeDetail;
    }

    /**
     * Delete PreorderComplete
     *
     * @param \Webkul\MarketplacePreorder\Api\Data\PreorderCompleteInterface $timeSlot
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\StoredetailInterface $storeDetail)
    {
        try {
            $this->resource->delete($storeDetail);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete PreorderComplete by given Block Identity
     *
     * @param string $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
