<?php

class Mmartinovic_Coeditor_Adminhtml_Coeditor_AjaxController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/edit');
    }

    public function indexAction()
    {
        $orderItemId = $this->getRequest()->getParam('order_item_id');

        $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);
        /* @var $orderItem Mage_Sales_Model_Order_Item */

        $product = $orderItem->getProduct();

        // Set preconfigured values on a product from buyRequest
        $buyRequest = $orderItem->getBuyRequest();
        if ($buyRequest) {
            /* The error suppression operatior (@) is here due to Magento bug
             * causing notice:
             *
             * Notice: Array to string conversion in
             * ...app/code/core/Mage/Catalog/Model/Product.php on line 1943
             *
             * being thrown when calling:
             *
             * Mage_Catalog_Helper_Product::prepareProductOptions()
             *
             * when buy request has datetime option type inside.
             *
             * This can be confirmed with Magento's own code at:
             *
             * Mage_Adminhtml_Sales_Order_CreateController::configureQuoteItemsAction()
             *
             */
            @Mage::helper('catalog/product')->prepareProductOptions($product, $buyRequest);
        }

        Mage::register('current_product', $product);
        Mage::register('product', $product);
        Mage::register('current_order_item', $orderItem);

        $this->loadLayout();
        $this->renderLayout();
    }

    public function updateAction()
    {
        // Validate form key
        if (!$this->_validateFormKey()) {
            $this->_redirectReferer();
            return;
        }

        // Received params
        $params = $this->getRequest()->getParams();

        // Order item Id
        $orderItemId = $params['order_item_id'];

        // Fetch order item by Id
        $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);
        /* @var $orderItem Mage_Sales_Model_Order_Item */

        // Extract buyRequest from order item
        $buyRequest = $orderItem->getBuyRequest();

        // Replace options in buyRequest
        $buyRequest->setOptions($params['options']);

        // Create a brand new quote
        $quote = Mage::getModel('sales/quote');
        /* @var $quote Mage_Sales_Model_Quote */

        // Add order item's product using modified $buyRequest
        $quote->addProduct($orderItem->getProduct(), $buyRequest);

        // Get quote's first (and only) order item
        $quoteItem = $quote->getItemsCollection()->getFirstItem();

        // Extract new order item options
        $newOrderItemOptions = $quote->getItemsCollection()
            ->getFirstItem()
            ->getProduct()
            ->getTypeInstance(true)
            ->getOrderOptions($quoteItem->getProduct());

        // Modify order item options
        $orderItem->setProductOptions($newOrderItemOptions);

        // Save order item
        try{
            $orderItem->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        // Grouped uses different block
        $productType = $orderItem->getProduct()->getTypeId();
        if($productType == 'grouped') {
            $responseBlockClass = 'adminhtml/sales_items_column_name_grouped';
        } else {
            $responseBlockClass = 'adminhtml/sales_items_column_name';
        }

        $this->loadLayout();

        // Create the response block
        $responseBlock = $this->getLayout()
            ->createBlock($responseBlockClass)
            ->setTemplate('sales/items/column/name.phtml')
            ->setItem($orderItem);

        $rootBlock = $this->getLayout()->getBlock('root');
        /* @var $root Mage_Core_Block_Text_List */

        // Insert the response block into layout
        $rootBlock->insert($responseBlock);

        $this->renderLayout();
    }

}