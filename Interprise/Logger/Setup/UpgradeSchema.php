<?php
/**
 * Created by Shadab.
 * User: mm
 * Date: 7/15/2018
 * Time: 9:53 AM
 */

namespace Interprise\Logger\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Model\Config;
 
class UpgradeSchema implements UpgradeSchemaInterface
{
    public $eavSetupFactory;
    public $eavConfig;
    public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
 
        //handle all possible upgrade versions
        $setup->endSetup();
    }
}
