<?php

class Application_Model_Db_Nations extends Application_Model_Db_Abstract
{

    const TABLE_NAME = 'nations';

    public function getAll()
    {
        $select = $this->_db->select()
            ->from(array('n' => self::TABLE_NAME))
            ->order(array('n.order_number ASC', 'n.full_name ASC'));
        $result = $this->_db->fetchAll($select);
        return $result;
    }

}