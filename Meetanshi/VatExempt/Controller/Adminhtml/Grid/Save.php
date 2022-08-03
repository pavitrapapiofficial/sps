<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Meetanshi\VatExempt\Model\GridFactory;
use Magento\Backend\App\Action\Context;

class Save extends Action
{
    var $gridFactory;

    public function __construct(
        Context $context,
        GridFactory $gridFactory
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('reason/grid/addrow');
            return;
        }
        try {
            $rowData = $this->gridFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setEntityId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccessMessage(__('Reason information has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
        $this->_redirect('reason/grid/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Meetanshi_VatExempt::add_row');
    }
}
