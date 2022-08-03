<?php
/**
 *
 * @category Webkul
 * @package Webkul_RewardSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\RewardSystem\Block\Adminhtml\Product\Edit\Tab;

use Magento\Catalog\Model\ProductFactory;
use Webkul\RewardSystem\Model\ResourceModel\RewardproductSpecific\CollectionFactory as RewardProductCollection;
use Webkul\RewardSystem\Helper\Data;

class GridSpecificTime extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_catalog;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_eavAttribute;

    /**
     * @var RewardProductCollection
     */
    protected $_rewardProductCollection;

     /**
      * @param \Magento\Backend\Block\Template\Context   $context
      * @param \Magento\Backend\Helper\Data              $backendHelper
      * @param ProductFactory                            $productFactory
      * @param array                                     $data
      */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        Data $dataHelper,
        ProductFactory $productFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        RewardProductCollection $rewardProductCollection,
        array $data = []
    ) {
        $this->_catalog = $productFactory;
        $this->_resource = $resource;
        $this->_eavAttribute = $eavAttribute;
        $this->dataHelper = $dataHelper;
        $this->_rewardProductCollection = $rewardProductCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rewardproductgridspecifictime');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'rewardsystem/product/gridspecific',
            ['_current' => true]
        );
    }
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $collection = $this->_catalog->create()->getCollection();

        $proAttrId = $this->_eavAttribute->getIdByCode("catalog_product", "name");

        $collection->getSelect()->joinLeft(
            ['cpev'=>$collection->getTable('catalog_product_entity_varchar')],
            'e.entity_id = cpev.entity_id',
            ['product_name'=>'value']
        )->where("cpev.store_id = 0 AND cpev.attribute_id = ".$proAttrId);

        $collection->getSelect()->joinLeft(
            ['rp'=>$collection->getTable('wk_rs_reward_products_specific')],
            'e.entity_id = rp.product_id',
            ['points'=>'points',"status"=>'status','start_time'=>'start_time','end_time'=>'end_time']
        );
        $collection->addFilterToMap("product_name", "cpev.value");
        $collection->addFilterToMap("points", "rp.points");
        $collection->addFilterToMap("status", "rp.status");
        $collection->addFilterToMap("start_time", "rp.start_time");
        $collection->addFilterToMap("end_time", "rp.end_time");

        /**
         * Get Time in grid by Magento locale timezone format
         */
        foreach ($collection as $collectionSetItem) {
            $stTime = $collectionSetItem->getStartTime();
            if ($stTime != "") {
                $startTime = $this->dataHelper->getTimeAccordingToTimeZone($stTime);
                $collectionSetItem->setStartTime($startTime);
            }
            $edTime = $collectionSetItem->getEndTime();
            if ($edTime != "") {
                $endTime = $this->dataHelper->getTimeAccordingToTimeZone($edTime);
                $collectionSetItem->setEndTime($endTime);
            }
        }
        $this->setCollection($collection);
    }
    protected function _setCollectionOrder($column)
    {
        if ($column->getOrderCallback()) {
            //phpcs:ignore
            call_user_func($column->getOrderCallback(), $this->getCollection(), $column);

            return $this;
        }

        return parent::_setCollectionOrder($column);
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_product',
            [
                'type' => 'checkbox',
                'name' => 'in_product',
                'index' => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'index' =>  'entity_id',
                'class' =>  'productid'
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Product SKU'),
                'index' =>  'sku',
                'class' =>  'productsku'
            ]
        );
        $this->addColumn(
            'product_name',
            [
                'header'                    => __('Product Name'),
                'index'                     => 'product_name',
                'filter_index'              => '`cpev`.`value`',
                'gmtoffset'                 => true,
                'filter_condition_callback' => [$this, 'filterProductName'],
                'order_callback'            => [$this, 'sortProductName']
            ]
        );
        $this->addColumn(
            'points',
            [
                'header' => __('Reward Points'),
                'index' => 'points',
                'filter_index'              => '`rp`.`points`',
                'gmtoffset'                 => true,
                'filter_condition_callback' => [$this, 'filterPoints'],
                'order_callback'            => [$this, 'sortPoints']
            ]
        );
        $this->addColumn(
            'start_time',
            [
                'header' => __('Start Time'),
                'index' => 'start_time',
                'filter_index'              => '`rp`.`start_time`',
                'gmtoffset'                 => true,
                'filter_condition_callback' => [$this, 'filterStartTime'],
                'order_callback'            => [$this, 'sortStartTime']
            ]
        );
        $this->addColumn(
            'end_time',
            [
                'header' => __('End Time'),
                'index' => 'end_time',
                'filter_index'              => '`rp`.`end_time`',
                'gmtoffset'                 => true,
                'filter_condition_callback' => [$this, 'filterEndTime'],
                'order_callback'            => [$this, 'sortEndTime']
            ]
        );
        $this->addColumn(
            "status",
            [
                "header"    => __("Status"),
                "index"     => "status",
                'type' => 'options',
                'options' => $this->_getBasedOnOptions(),
                'filter_index'              => '`rp`.`status`',
                'gmtoffset'                 => true,
                'filter_condition_callback' => [$this, 'filterStatus'],
                'order_callback'            => [$this, 'sortStatus']
            ]
        );
        return parent::_prepareColumns();
    }

    protected function _getBasedOnOptions()
    {
        return [
            0=>__('Disabled'),
            1=>__('Enabled')
        ];
    }

    public function filterProductName($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }

        $condition = $collection->getConnection()
            ->prepareSqlCondition('cpev.value', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    public function sortProductName($collection, $column)
    {
        $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }
    public function filterStatus($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }
        $condition = $collection->getConnection()
            ->prepareSqlCondition('rp.status', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    public function sortStatus($collection, $column)
    {
        $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }
    public function filterPoints($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }
        $condition = $collection->getConnection()
            ->prepareSqlCondition('rp.points', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    public function sortPoints($collection, $column)
    {
        $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }

    public function filterStartTime($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }
        $condition = $collection->getConnection()
            ->prepareSqlCondition('rp.start_time', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    public function sortStartTime($collection, $column)
    {
        $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }

    public function filterEndTime($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }
        $condition = $collection->getConnection()
            ->prepareSqlCondition('rp.end_time', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    public function sortEndTime($collection, $column)
    {
        $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }
}
