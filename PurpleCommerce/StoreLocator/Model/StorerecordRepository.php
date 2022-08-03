<?php


namespace PurpleCommerce\StoreLocator\Model;

use PurpleCommerce\StoreLocator\Api\Data;
use PurpleCommerce\StoreLocator\Api\StorerecordRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use PurpleCommerce\StoreLocator\Model\ResourceModel\Storerecord as StoreRecordResource;
use PurpleCommerce\StoreLocator\Model\ResourceModel\Storerecord\CollectionFactory as StoreRecordCollection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class TimeslotConfigRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StorerecordRepository implements StorerecordRepositoryInterface
{
    /**
     * @var ResourceBlock
     */
    protected $resource;

    /**
     * @var BlockFactory
     */
    protected $storeRecordFactory;

    /**
     * @var BlockCollectionFactory
     */
    protected $storeRecordCollectionFactory;

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
        StoreRecordResource $resource,
        StorerecordFactory $storeRecordFactory,
        StoreRecordCollection $storeRecordCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->storeRecordFactory = $storeRecordFactory;
        $this->storeRecordCollectionFactory = $storeRecordCollectionFactory;
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
    public function save(Data\StorerecordInterface $storeRecord)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $storeRecord->setStoreId($storeId);
        try {
            $this->resource->save($storeRecord);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $storeRecord;
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
        $storeRecord = $this->storeRecordFactory->create();
        $this->resource->load($storeRecord, $id);
        if (!$storeRecord->getId()) {
            throw new NoSuchEntityException(__('Store record with id "%1" does not exist.', $id));
        }
        return $storeRecord;
    }

    /**
     * Delete PreorderComplete
     *
     * @param \Webkul\MarketplacePreorder\Api\Data\PreorderCompleteInterface $timeSlot
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\StorerecordInterface $storeRecord)
    {
        try {
            $this->resource->delete($storeRecord);
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
