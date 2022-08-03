<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_RewardSystem
 * @author    Webkul
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\RewardSystem\Controller\Adminhtml\Reward;

use Magento\Backend\App\Action\Context;
use Webkul\RewardSystem\Helper\Data as HelperData;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Webkul\RewardSystem\Controller\Adminhtml\Reward\Save
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     *  {@inheritDoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $this->dataPersistor->set('transaction', $data);
                $this->processSave($data);
                $this->dataPersistor->clear('transaction');
                $this->messageManager->addSuccessMessage(__('Transaction is saved successfully!!'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the transaction.')
                );
            }
            return $resultRedirect->setPath('*/*/new');
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array $data
     * @throws LocalizedException
     * @return void
     */
    private function processSave(array $data)
    {
        if (isset($data['customer_selections'])) {
            $rewardData = [
              'points' => $data['reward_point'],
              'type' => $data['action'],
              'review_id' => 0,
              'order_id' => 0,
              'status' => 1,
              'is_revert' => 0,
              'note' => $data['transaction_note']
            ];
            foreach ($data['customer_selections'] as $customerData) {
                $rewardData['customer_id'] = $customerData['customer_id'];
                if ($data['action'] == 'credit') {
                    $msg = __(
                        'You have been credited with %1 Feather Points into your online account at sandpipershoes.com.',
                        $data['reward_point']
                    );
                    $adminMsg = __(
                        '%1 has been credited with %2 Feather points',
                        $customerData['customer_name'],
                        $data['reward_point']
                    );
                } else {
                    $msg = __(
                        '%1 reward points debited by the admin',
                        $data['reward_point']
                    );
                    $adminMsg = __(
                        '%1 customer has been debited with %2 reward points',
                        $customerData['customer_name'],
                        $data['reward_point']
                    );
                }
                $this->helperData->setDataFromAdmin(
                    $msg,
                    $adminMsg,
                    $rewardData
                );
            }
        } else {
            throw new LocalizedException(
                __('No customers added, Please select customers')
            );
        }
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_RewardSystem::reward');
    }
}
