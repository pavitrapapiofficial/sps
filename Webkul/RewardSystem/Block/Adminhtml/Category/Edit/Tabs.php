<?php
/**
 * Block\Adminhtml\Category\Edit\Tabs.php
 *
 * @category Webkul
 * @package Webkul_RewardSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Webkul\RewardSystem\Block\Adminhtml\Category\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Backend\Model\Auth\Session;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * @var InlineInterface
     */
    protected $_translateInline;

    /**
     * @param Context                                   $context
     * @param InlineInterface                           $translateInline
     * @param EncoderInterface                          $jsonEncoder
     * @param Session                                   $authSession
     * @param array                                     $data
     */
    public function __construct(
        Context $context,
        InlineInterface $translateInline,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        array $data = []
    ) {
        $this->_translateInline = $translateInline;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('category_points');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Reward Points On Category'));
    }

    /**
     * Prepare Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'rewardpointoncategory',
            [
                'label' => __('Reward Point On Category'),
                'content'=>$this->getLayout()->createBlock(
                    \Webkul\RewardSystem\Block\Adminhtml\Category\Edit\Tab\Form::class
                )->toHtml(),
                'class' => 'ajax'
            ]
        );
        $this->addTab(
            'rewardpointoncategoryspecifictime',
            [
                'label' => __('Reward Point On Category for Specific Time'),
                'content'=>$this->getLayout()->createBlock(
                    \Webkul\RewardSystem\Block\Adminhtml\Category\Edit\Tab\FormSpecificTime::class
                )->toHtml(),
                'class' => 'ajax'
            ]
        );
        return parent::_prepareLayout();
    }

    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        $this->_translateInline->processResponseBody($html);
        return $html;
    }
}
