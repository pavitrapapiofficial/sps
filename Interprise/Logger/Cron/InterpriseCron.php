<?php
namespace Interprise\Logger\Cron;

class InterpriseCron
{

    protected $logger;
    protected $_processor;
    protected $_scheduler;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Interprise\Logger\Controller\Cron\Processor $cronprocessor,
        \Interprise\Logger\Controller\Cron\Scheduler $scheduler
    ) {
        $this->logger = $logger;
        $this->_processor = $cronprocessor;
        $this->_scheduler = $scheduler;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob InterpriseCron is executed.");
    }
    public function processor()
    {
        $this->_processor->execute();
        $this->logger->addInfo("Cronprocessor for InterpriseCron is executed.");
    }
    public function scheduler()
    {
        $this->_scheduler->execute();
        $this->logger->addInfo("Cronscheduler for InterpriseCron is executed.");
    }
}
