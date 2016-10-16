<?php

class WaPoNe_StatusShippingMethodManager_Model_System_Config_Source_Shipping_Activemethods
{
    public function getMethodsOption()
    {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $shipMethods = array();
        foreach ($methods as $shippigCode=>$shippingModel)
        {
            $shippingTitle = Mage::getStoreConfig('carriers/'.$shippigCode.'/title');
            $shipMethods[$shippigCode] = $shippingTitle;
        }
        return $shipMethods;
    }

    public function toOptionArray()
    {
        $options = array();
        foreach ($this->getMethodsOption() as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }

        return $options;
    }
}
