<?php

class WaPoNe_StatusShippingMethodManager_Model_System_Config_Source_Shipping_Deactivemethods
{
    public function getMethodsOption()
    {
        $all_methods = Mage::getSingleton('shipping/config')->getAllCarriers();
        $active_methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $shipMethods = array();
        foreach ($all_methods as $shippigCode=>$shippingModel)
        {
            if(!array_key_exists($shippigCode, $active_methods)) {
                $shippingTitle = Mage::getStoreConfig('carriers/'.$shippigCode.'/title');
                $shipMethods[$shippigCode] = $shippingTitle;
            }
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
