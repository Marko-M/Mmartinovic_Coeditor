<?php

class Mmartinovic_Coeditor_Block_Adminhtml_Sales_Order_View_Form extends Mage_Adminhtml_Block_Widget
{
    protected $_product;
    protected $_orderItem;

    /**
     * Retrieve product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if (Mage::registry('current_product')) {
                $this->_product = Mage::registry('current_product');
            } else {
                $this->_product = Mage::getSingleton('catalog/product');
            }
        }
        return $this->_product;
    }

    public function getOrderItem()
    {
        if (!$this->_orderItem) {
            if (Mage::registry('current_order_item')) {
                $this->_orderItem = Mage::registry('current_order_item');
            } else {
                $this->_orderItem = Mage::getSingleton('sales/order_item');
            }
        }
        return $this->_orderItem;
    }

    /**
     * Set product object
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Adminhtml_Block_Catalog_Product_Composite_Configure
     */
    public function setProduct(Mage_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }
}
