<?php
/**
 * @category  Webkul
 * @package   Webkul_AdvancedLayeredNavigation
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\AdvancedLayeredNavigation\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
      /**
       * {@inheritdoc}
       */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
  
        $installer = $setup;
        $installer->startSetup();

        /*
         * Create table 'wk_layered_carousel_options'
         */
        $table = $installer->getConnection()->newTable($installer->getTable('wk_layered_carousel_options'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false,'primary' => true],
                'Entity Id'
            )->addColumn(
                'carousel_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Title'
            )->addColumn(
                'image_path',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Image Path'
            )->addColumn(
                'attribute_option_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Attribute Code'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Add time'
            )->addIndex(
                $installer->getIdxName('wk_layered_carousel_options', ['entity_id']),
                ['entity_id']
            )->setComment('Carousel Options');

        $installer->getConnection()->createTable($table);

        /*
         * Create table 'wk_layered_carousel_attributes'
         */
        $table = $installer->getConnection()->newTable($installer->getTable('wk_layered_carousel_attributes'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false,'primary' => true],
                'Entity Id'
            )->addColumn(
                'attribute_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Attribute Code'
            )->addColumn(
                'categories',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Categories'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Title'
            )->addColumn(
                'enable',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Enable'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Add time'
            )->addIndex(
                $installer->getIdxName('wk_layered_carousel_attributes', ['entity_id']),
                ['entity_id']
            )->setComment('Carousel Attributes');

         $installer->getConnection()->createTable($table);
         $installer->getConnection()->addColumn(
             $setup->getTable('wk_layered_carousel_options'),
             'option_name',
             [
                'type' =>   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'unsigned' => true,
                'nullable' => false,
                'comment' => 'Option Name'
             ]
         );

        $installer->endSetup();
    }
}
