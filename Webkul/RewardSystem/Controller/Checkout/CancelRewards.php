<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_RewardSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software protected Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\RewardSystem\Controller\Checkout;

use Magento\Framework\Session\SessionManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

class CancelRewards extends Action
{
    /**
     * @var SessionManager
     */
    protected $session;

    /**
     * @var Context
     */
    protected $sescontextsion;

    /**
     * @param Context          $context
     * @param SessionManager   $session
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        SessionManager $session
    ) {
        $this->session = $session;
        parent::__construct($context);
    }
    /**
     * unset the credit data from session.
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $this->session->unsRewardInfo();
        $this->messageManager->addSuccess('Your Reward was successfully cancelled.');
        return $this->resultRedirectFactory
                ->create()
                ->setPath('checkout/cart', ['_secure' => $this->getRequest()->isSecure()]);
    }
}
