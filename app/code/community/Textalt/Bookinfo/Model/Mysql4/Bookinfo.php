<?php

class Textalt_Bookinfo_Model_Mysql4_Bookinfo extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct()
    {
        $this->_init('bookinfo/bookinfo', 'book_id');
    }   
} 


?>
