<?php

class Application_Model_Db_Tank_Types extends Application_Model_Db_Abstract
{

    const TABLE_NAME = 'tank_types';

    public function getAll()
    {
        $select = $this->_db->select()
            ->from(array('tt' => self::TABLE_NAME))
            ->order(array('tt.order_number ASC', 'tt.full_name ASC'));
        $result = $this->_db->fetchAll($select);
        return $result;
    }

}