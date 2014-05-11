<?php

class Mmartinovic_Coeditor_Block_Adminhtml_Sales_Order_View_Coeditor extends Mage_Adminhtml_Block_Template 
{

    /**
     * Get order item object from parent block
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getItem() {
        return $this->getParentBlock()->getData('item');
    }

    public function getOrderItemOptions() {
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

    /**
     * Do any of the product options affect price
     * 
     * @return boolean
     */
    protected function optionsAffectPrice() {
        $options = $this->getItem()->getProduct()->getOptions();

        foreach ($options as $option) {
            if ($option->hasPrice() && ((float) $option->getPrice() > 0)) {
                return true;
            }
        }

        return false;
    }

}