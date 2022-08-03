<?php

namespace Interprise\Logger\Controller\Cronprocessor;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactor
     */
    public $is_status;
    public $is_api_key;
    public $is_api_url;
    public $helper;
    public $_inventoryItem;
    public $_customer;
   // public $_prices;

    public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    \Interprise\Logger\Helper\Data $_helper,
    \Interprise\Logger\Helper\InventoryItem $_inventoryItem,
    \Interprise\Logger\Helper\Customers $_customer,
    \Interprise\Logger\Helper\Prices $_prices,
    \Interprise\Logger\Helper\InventoryStock $_stock,
    \Interprise\Logger\Helper\InventoryMatrixItem $_inventoryMatrixItem
    
    ) {
        $this->resultPageFactory        = $resultPageFactory;
        $this->helper                   = $_helper;
        $this->inventoryitem            = $_inventoryItem;
        $this->customer                 = $_customer;
        $this->prices                   = $_prices;
        $this->stock                    = $_stock;
        $this->inventoryMatrixItem      = $_inventoryMatrixItem;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        $data = $this->getItem();
         if (is_array($data) && count($data) > 0) {
            //$this->interprise_processor($processor_id, 1);
            do {
                $this->do_inprocess_item($data);
                $master_id = $data['CronMasterId'];
                switch ($master_id) {
                    case 1:
                        $result = $this->inventoryitem->Inventoryitem_single($data);
                        break;
                    case 2:
                        $result = $this->customer->Customer_single($data);
                        break;
                    case 5:
                        $result = $this->prices->Prices_single($data);
                        break;
                    case 7:
                        $result = $this->stock->InventoryStock_single($data);
                        break;
                    case 12:
                        $result = $this->inventoryMatrixItem->InventoryMatrixItem_single($data);
                        break;
                    case 16:
                       // $result = $this->inventoryBundleItem->InventoryBundleItem_single($data);
                        break;
                    default:
                        break;
                }
                $this->update_processed_status($result,$data);
                $data = $this->getItem();
            } while (count($data) > 0);
        }
    }

    public function prf($param) {
        echo "<pre>";
        print_r($param);
        echo "</pre>";
    }

    public function do_inprocess_item($data) {
        $data_id = $data['DataId'];
        $master_id = $data['CronMasterId'];
        $collections = $this->_objectManager->create('Interprise\Logger\Model\CronActivitySchedule')->getCollection()
                ->addFieldToFilter('DataId', array('eq' => "$data_id"))
                ->addFieldToFilter('CronMasterId', array('eq' => $master_id))
                ->addFieldToFilter('Status', array('eq' => 'pending'));
        foreach ($collections as $item) {
            $item->setStatus('In Process');
            $item->save();
        }
    }

    public function update_processed_status($result, $data) {
        $data_id = $data['DataId'];
        $master_id = $data['CronMasterId'];
        $status = $result['Status'];
        $remarks = $result['Remarks'];
        $request = $result['Request'];
        $response = $result['Response'];
        $collections = $this->_objectManager->create('Interprise\Logger\Model\CronActivitySchedule')->getCollection()
            ->addFieldToFilter('DataId', array('eq' => "$data_id"))
            ->addFieldToFilter('CronMasterId', array('eq' => $master_id))
            ->addFieldToFilter('Status', array('eq' => 'In Process'));
        foreach ($collections as $item) {
            $item->setStatus($status);
            $item->setRemarks($remarks);
            $item->setRequest($request);
            $item->setResponse($response);
            $item->save();
        }
    }

    public function getItem() {
        $collections = $this->_objectManager->create('Interprise\Logger\Model\CronActivitySchedule');
        $collection = $collections->getCollection();
        $collection->addFilter('Status', array('pending'));
        $collection->getSelect()->order(array('priority asc', 'cronactivityschedule_id asc'))->limit(1);
        $result = $collection->getData();
        if (isset($result[0])) {
            return $result[0];
        } else {
            return array();
        }
    }

    public function interprise_processor_count($id) {
        $interprise_processor = $this->cron_helper->_getTableName('interprise_processor');
        $result = $this->cron_helper->connection->fetchRow("select status from $interprise_processor where id=$id");
        return $result['status'];
    }

}
