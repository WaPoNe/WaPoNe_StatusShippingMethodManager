<?php

class WaPoNe_StatusShippingMethodManager_Model_StatusShippingMethodManager extends Mage_Core_Model_Abstract
{

    const ACTIVE = 1;
    const DEACTIVE = 0;
    private $all_shipping_methods = array();

    protected function _construct()
    {
        $this->_init('statusshippingmethodmanager/statusshippingmethodmanager');
        $this->_populateAvailableMethods();
    }

    private function _populateAvailableMethods() {
        //Available Shipping Methods
        $all_methods = Mage::getSingleton('shipping/config')->getAllCarriers();
        foreach ($all_methods as $shippigCode=>$shippingModel)
        {
            array_push($this->all_shipping_methods, $shippigCode);
        }
    }

    /* WaPoNe (06-10-2016): attivazione dei metodi */
    private function enableMethods()
    {
        // Check dates
        if ($this->getActionDate('statusshippingmethodmanager/statusshippingmethodmanager_group/activestartdate', 'statusshippingmethodmanager/statusshippingmethodmanager_group/activestarttime')) {

            Mage::log('Active Shipping Methods', null, 'wapone.log');

            //Shipping Methods to active
            $methods_to_active = $this->getMethods('statusshippingmethodmanager/statusshippingmethodmanager_group/methodstoactivate');

            if (count($methods_to_active) > 0) {
                for ($row = 0; $row < count($methods_to_active); $row++) {
                    // Checking shipping method is valid
                    if(in_array($methods_to_active[$row], $this->all_shipping_methods)) {
                        $_path = "carriers/".$methods_to_active[$row]."/active";
                        //Mage::log('Method to active:'.$_path, null, 'wapone.log');
                        Mage::getConfig()->saveConfig($_path, self::ACTIVE, 'default', 0);
                    }
                }

                Mage::getConfig()->saveConfig('statusshippingmethodmanager/statusshippingmethodmanager_group/module_activation_status', '0', 'default', 0);
            }
        }
    }

    /* WaPoNe (06-10-2016): disattivazione dei metodi */
    private function disableMethods()
    {
        // Check dates
        if ($this->getActionDate('statusshippingmethodmanager/statusshippingmethodmanager_group/deactivestartdate', 'statusshippingmethodmanager/statusshippingmethodmanager_group/deactivestarttime')) {

            Mage::log('Deactive Shipping Methods', null, 'wapone.log');

            //Shipping Methods to deactive
            $methods_to_deactive = $this->getMethods('statusshippingmethodmanager/statusshippingmethodmanager_group/methodstodeactivate');

            if (count($methods_to_deactive) > 0) {
                for ($row = 0; $row < count($methods_to_deactive); $row++) {
                    // Checking shipping method is valid
                    if(in_array($methods_to_deactive[$row], $this->all_shipping_methods)) {
                        $_path = "carriers/" . $methods_to_deactive[$row] . "/active";
                        //Mage::log('Method to deactive:' . $_path, null, 'wapone.log');
                        Mage::getConfig()->saveConfig($_path, self::DEACTIVE, 'default', 0);
                    }
                }

                Mage::getConfig()->saveConfig('statusshippingmethodmanager/statusshippingmethodmanager_group/module_deactivation_status', '0', 'default', 0);
            }
        }
    }

    public function manageShippingStatus()
    {
        if ((int)Mage::getStoreConfig('statusshippingmethodmanager/statusshippingmethodmanager_group/module_activation_status') === 1) :
            $this->enableMethods();
        endif;

        if ((int)Mage::getStoreConfig('statusshippingmethodmanager/statusshippingmethodmanager_group/module_deactivation_status') === 1) :
            $this->disableMethods();
        endif;

    }


    /* WaPoNe (06-10-2016): to check if it's time to start script */
    private function getActionDate($date_param, $time_param)
    {
        $date = Mage::getStoreConfig($date_param);
        $time = Mage::getStoreConfig($time_param);

        if (!empty($date)) {
            $now = Mage::app()->getLocale()->date()->toString('yy-MM-dd HH.mm');
            $date1 = new DateTime($now);

            $arr_date = explode("-", $date);
            $arr_time = explode(",", $time);

            try {
                $date2 = new DateTime($arr_date[2] . "-" . $arr_date[1] . "-" . $arr_date[0] . " " . $arr_time[0] . ":" . $arr_time[1] . ":" . $arr_time[2]);
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'wapone.log');
            }
            // Mage::log("Date1:".$date1->format('Y-m-d H:i:s')." - Date2:".$date2->format('Y-m-d H:i:s'), null, "wapone.log");

            if ($date1 >= $date2) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    /* WaPoNe (06-10-2016): Obtaining shipping methods list to enable/disable */
    public function getMethods($param)
    {
        $methods = Mage::getStoreConfig($param);
        $arr_result = array();

        if (!empty($methods)):
            $arr_result = explode(",", $methods);
        endif;

        return $arr_result;
    }

}
