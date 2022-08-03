<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Controller\Adminhtml\Grid;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\VatExempt\Model\GridFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Backend\App\Action;

class AddRow extends Action
{
    private $coreRegistry;

    private $gridFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        GridFactory $gridFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->gridFactory = $gridFactory;
    }

    public function execute()
    {
        $rowId = (int)$this->getRequest()->getParam('id');
        $rowData = $this->gridFactory->create();
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getTitle();
            if (!$rowData->getEntityId()) {
                $this->messageManager->addErrorMessage(__('Reason no longer exist.'));
                $this->_redirect('reason/grid/rowdata');
                return;
            }
        }

        $this->coreRegistry->register('row_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Reason Information') . $rowTitle : __('Add Reason Data');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Meetanshi_VatExempt::add_row');
    }
}
