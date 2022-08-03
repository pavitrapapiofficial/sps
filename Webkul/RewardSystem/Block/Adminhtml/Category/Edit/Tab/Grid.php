<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_RewardSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\RewardSystem\Block\Adminhtml\Category\Edit\Tab;

use Webkul\RewardSystem\Model\CategoryFactory;
use Webkul\RewardSystem\Model\ResourceModel\Rewardcategory\CollectionFactory as RewardCategoryCollection;
use Magento\Catalog\Helper\Category;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_catalog;

    protected $_eavAttribute;
    protected $_rewardCategoryCollection;

     /**
      * @param \Magento\Backend\Block\Template\Context   $context
      * @param \Magento\Backend\Helper\Data              $backendHelper
      * @param ProductFactory                            $productFactory
      * @param array                                     $data
      */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CategoryFactory $categoryFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        RewardCategoryCollection $rewardCategoryCollection,
        Category $categoryHelper,
        array $data = []
    ) {
        $this->_category = $categoryFactory;
        $this->_resource = $resource;
        $this->_eavAttribute = $eavAttribute;
        $this->_rewardCategoryCollection = $rewardCategoryCollection;
        $this->_categoryHelepr = $categoryHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rewardcategorygrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'rewardsystem/category/grid',
            ['_current' => true]
        );
    }
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_category->create()->getCollection();
        $proAttrId = $this->_eavAttribute->getIdByCode("catalog_category", "name");

        $collection->getSelect()->joinLeft(
            ['cpev'=>$collection->getTable('catalog_category_entity_varchar')],
            'main_table.entity_id = cpev.entity_id',
            ['category_name'=>'value']
        )->where("cpev.store_id = 0 AND cpev.attribute_id = ".$proAttrId);

        $collection->getSelect()->joinLeft(
            ['rc'=>$collection->getTable('wk_rs_reward_category')],
            'main_table.entity_id = rc.category_id',
            ['points'=>'points',"status"=>'status']
        );

        $collection->addFilterToMap("category_name", "cpev.value");
        $collection->addFilterToMap("points", "rc.points");
        $collection->addFilterToMap("status", "rc.status");

        $this->setCollection($collection);
        parent::_prepareCollection();
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
            'in_category',
            [
                'type' => 'checkbox',
                'name' => 'in_category',
                'index' => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Category ID'),
                'index' =>  'entity_id',
                'class' =>  'productid'
            ]
        );
        $this->addColumn(
            'category_name',
            [
                'header'                    => __('Category Name'),
                'index'                     => 'category_name',
                'filter_index'              => '`cpev`.`value`',
                'gmtoffset'                 => true,
                'filter_condition_callback' => [$this, 'filterCategoryName'],
                'order_callback'            => [$this, 'sortCategoryName']
            ]
        );
        $this->addColumn(
            'points',
            [
                'header' => __('Reward Points'),
                'index' => 'points',
                'filter_index'              => '`rc`.`points`',
                'gmtoffset'                 => true,
                'filter_condition_callback' => [$this, 'filterPoints'],
                'order_callback'            => [$this, 'sortPoints']
            ]
        );
        $this->addColumn(
            "status",
            [
                "header"    => __("Status"),
                "index"     => "status",
                'type' => 'options',
                'options' => $this->_getBasedOnOptions(),
                'filter_index'              => '`rc`.`status`',
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

    public function filterCategoryName($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }

        $condition = $collection->getConnection()
            ->prepareSqlCondition('cpev.value', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    public function sortCategoryName($collection, $column)
    {
        $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }
    public function filterStatus($collection, $column)
    {
        if (!$column->getFilter()->getCondition()) {
            return;
        }
        $condition = $collection->getConnection()
            ->prepareSqlCondition('rc.status', $column->getFilter()->getCondition());
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
            ->prepareSqlCondition('rc.points', $column->getFilter()->getCondition());
        $collection->getSelect()->where($condition);
    }

    public function sortPoints($collection, $column)
    {
        $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }
}
