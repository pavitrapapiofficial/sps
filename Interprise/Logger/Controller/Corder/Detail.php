<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Index
 *
 * @author geuser1
 */
namespace Interprise\Logger\Controller\Corder;

class Detail extends \Magento\Framework\App\Action\Action
{
    public $_pageFactory;
    public $request;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_pageFactory = $pageFactory;
        $this->request = $request;
        return parent::__construct($context);
    }

    public function execute()
    {
               return $this->_pageFactory->create();
    }
}
