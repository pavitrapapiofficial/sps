<?php

namespace Webkul\GoogleShoppingFeed\Model\Cron;

class ReExport
{
    protected $_export;
    
    public function __construct(
        \Webkul\GoogleShoppingFeed\Controller\Adminhtml\Products\Export $export
    ){
        $this->_export = $export;
    }

	public function execute()
	{
		
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info(__METHOD__);
		$logger->info("command started--");
        $this->_export->execute();
		$logger->info("command executed after--");
		return $this;

	}
}