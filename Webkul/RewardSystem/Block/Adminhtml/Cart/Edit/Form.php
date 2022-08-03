<?php

/**
 * Block\Adminhtml\Cartrules\Edit Form.php
 *
 * @category Webkul
 * @package Webkul_RewardSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\RewardSystem\Block\Adminhtml\Cart\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('edit_form');
        $this->setTitle(__('Reward system Cart Rule'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('reward_cartrewardData');
        $form = $this->_formFactory->create(
            ['data' =>
                ['id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post']
            ]
        );
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );
        if ($model && $model->getEntityId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }
        $fieldset->addField(
            'points',
            'text',
            [
                'name' => 'points',
                'label' => __('Reward Point'),
                'required' => true,
                'class' =>  'required-entry validate-greater-than-zero',
                'placeholder'=>'Points'
            ]
        );
        $fieldset->addField(
            'amount_from',
            'text',
            [
                'name' => 'amount_from',
                'label' => __('Amount From'),
                'required' => true,
                'class' =>  'required-entry validate-greater-than-zero',
                'placeholder'=>'Amount From'
            ]
        );
        $fieldset->addField(
            'amount_to',
            'text',
            [
                'name' => 'amount_to',
                'label' => __('Amount To'),
                'required' => true,
                'class' =>  'required-entry validate-greater-than-zero',
                'placeholder'=>'Amount To'
            ]
        );
        $fieldset->addField(
            'start_date',
            'date',
            [
                'name' => 'start_date',
                'label' => __('Start From Date'),
                'title' => __('Start From Date'),
                'disabled' => false,
                'singleClick'=> true,
                'required' => true,
                'date_format' => 'yyyy-MM-dd',
                'time'=>false
            ]
        );
        $fieldset->addField(
            'end_date',
            'date',
            [
                'name' => 'end_date',
                'label' => __('End Date'),
                'title' => __('End Date'),
                'disabled' => false,
                'singleClick'=> true,
                'required' => true,
                'date_format' => 'yyyy-MM-dd',
                'time'=>false
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Rule Status'),
                'title' => __('Rule Status'),
                'name' => 'status',
                'required' => true,
                'options' => [
                    1 => __('Enabled'),
                    0 => __('Disabled')
                ]
            ]
        );
        if ($model) {
            $form->setValues($model->getData());
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
