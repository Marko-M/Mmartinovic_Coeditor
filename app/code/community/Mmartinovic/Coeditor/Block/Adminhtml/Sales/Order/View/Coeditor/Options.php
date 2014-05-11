<?php

class Mmartinovic_Coeditor_Block_Adminhtml_Sales_Order_View_Coeditor_Options extends Mage_Catalog_Block_Product_View_Options
{
    /**
     * Constructor for our block with options
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->addOptionRenderer(
            'default',
            'catalog/product_view_options_type_default',
            'mmartinovic_coeditor/sales/order/view/coeditor/options/type/default.phtml'
        );
    }

    /**
     * Get option html block
     *
     * @param Mage_Catalog_Model_Product_Option $option
     *
     * @return string
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        $renderer = $this->getOptionRender(
            $this->getGroupOfOption($option->getType())
        );
        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                ->setTemplate($renderer['template'])
                ->setSkipJsReloadPrice(1);
        }
        return $renderer['renderer']
            ->setProduct($this->getProduct())
            ->setOption($option)
            ->toHtml();
    }
    
}
