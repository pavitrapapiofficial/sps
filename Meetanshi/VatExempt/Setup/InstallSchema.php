<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $connection = $installer->getConnection();

        $quoteTable = $installer->getTable('quote');
        $quote_columns = [
            'vat_exempt_customer' => ['type' => Table::TYPE_TEXT, 'length' => '256', 'nullable' => false, 'comment' => 'Vat Exempt Customer',],
            'vat_exempt_reason' => ['type' => Table::TYPE_TEXT, 'length' => '256', 'nullable' => false, 'comment' => 'Vat Exempt Customer',],
        ];
        foreach ($quote_columns as $name => $definition) {
            $connection->addColumn($quoteTable, $name, $definition);
        }

        $quoteItemTable = $installer->getTable('quote_item');
        $quote_item_columns = [
            'vat_exempted' => ['type' => Table::TYPE_TEXT, 'length' => '256', 'nullable' => false, 'comment' => 'Vat Exempt Tax',],
        ];
        foreach ($quote_item_columns as $name => $definition) {
            $connection->addColumn($quoteItemTable, $name, $definition);
        }

        $salesOrder = $installer->getTable('sales_order');
        $sales_order_columns = [
            'vat_exempt_customer' => ['type' => Table::TYPE_TEXT, 'length' => '256', 'nullable' => false, 'comment' => 'Vat Exempt Customer',],
            'vat_exempt_reason' => ['type' => Table::TYPE_TEXT, 'length' => '256', 'nullable' => false, 'comment' => 'Vat Exempt Customer',],
        ];
        foreach ($sales_order_columns as $name1 => $definition1) {
            $connection->addColumn($salesOrder, $name1, $definition1);
        }

        $table = $installer->getConnection()->newTable(
            $installer->getTable('vat_exempt_reason')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Vat Exempt Reason Id'
        )->addColumn(
            'reason',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Reason'
        )->addColumn(
            'status',
            Table::TYPE_BOOLEAN,
            '2M',
            ['nullable' => false],
            'Status'
        )->addColumn(
            'sort_order',
            Table::TYPE_TEXT,
            null,
            [],
            'Sort Order'
        )->setComment(
            'Vat Exempt Reason Table'
        );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
