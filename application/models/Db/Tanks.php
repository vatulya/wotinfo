<?php

class Application_Model_Db_Tanks extends Application_Model_Db_Abstract
{

    const TABLE_NAME = 'tanks';

    public function getAll($nationId = null, $tankTypeId = null, array $params = array())
    {
        $select = $this->_db->select()
            ->from(array('t' => self::TABLE_NAME))
            ->order(array('t.order_number ASC', 't.full_name ASC'));
        if ($nationId) {
            $select->where('t.nation_id = ?', $nationId);
        }
        if ($tankTypeId) {
            $select->where('t.tank_type_id = ?', $tankTypeId);
        }
        if (isset($params['tank_parameters'])) {
            $select->join(array('tp' => Application_Model_Db_Tank_Parameters::TABLE_NAME), 'tp.tank_id = t.id', array('*'));
        }
        $result = $this->_db->fetchAll($select);
        return $result;
    }

    public function getByCode($nationCode, $tankCode, array $params = array())
    {
        $select = $this->_db->select()
            ->from(array('t' => self::TABLE_NAME))
            ->join(array('n' => Application_Model_Db_Nations::TABLE_NAME), 'n.id = t.nation_id', array('n.code AS nation_code'))
            ->where('t.code = ?', $tankCode)
            ->where('n.code = ?', $nationCode);
        if (isset($params['tank_parameters'])) {
            $select->join(array('tp' => Application_Model_Db_Tank_Parameters::TABLE_NAME), 'tp.tank_id = t.id', array('*'));
        }
        $result = $this->_db->fetchRow($select);
        return $result;
    }

    public function truncateTanks()
    {
        $query = 'TRUNCATE ' . self::TABLE_NAME;
        $this->_db->query($query);
        $query = 'TRUNCATE ' . Application_Model_Db_Tank_Parameters::TABLE_NAME;
        $this->_db->query($query);
        return true;
    }

    public function insert(array $tank, array $parameters)
    {
        $tankId = 0;
        $result = $this->_db->insert(self::TABLE_NAME, $tank);
        if ($result) {
            $tankId = $this->_db->query('SELECT LAST_INSERT_ID();')->fetchColumn();
            if ($tankId > 0) {
                $parameters['tank_id'] = $tankId;
                $result = $this->_db->insert(Application_Model_Db_Tank_Parameters::TABLE_NAME, $parameters);
                if ( ! $result) {
                    $tankId = 0;
                }
            }
        }
        return $tankId;
    }

    public function getTanks(array $tanks, $sort, $direction)
    {
        $direction = $direction ? 'ASC' : 'DESC';
        $order = array('t.order_number ASC', 't.full_name ASC');
        switch ($sort) {
            case "lvl":
                $order = array('t.lvl ' . $direction, 't.order_number ' . $direction, 't.full_name ' . $direction);
                break;

            case "name":
                $order = array('t.full_name ' . $direction);
                break;

            case "hit_points":
                $order = array('tp.hit_points ' . $direction);
                break;

            case "weight":
                $order = array('tp.weight ' . $direction);
                break;

            case "load_limit":
                $order = array('tp.load_limit ' . $direction);
                break;

            case "price":
                $order = array('tp.price ' . $direction);
                break;

            case "engine_power":
                $order = array('tp.engine_power ' . $direction);
                break;

            case "speed_limit":
                $order = array('tp.speed_limit ' . $direction);
                break;

            case "traverse_speed":
                $order = array('tp.traverse_speed ' . $direction);
                break;

            case "turret_traverse_speed":
                $order = array('tp.turret_traverse_speed ' . $direction);
                break;

            case "hull_armor":
                $order = array('tp.hull_armor ' . $direction);
                break;

            case "turret_armor":
                $order = array('tp.turret_armor ' . $direction);
                break;

            case "gun_name":
                $order = array('tp.gun_name ' . $direction);
                break;

            case "ammunition":
                $order = array('tp.ammunition ' . $direction);
                break;

            case "avg_damage":
                $order = array('tp.avg_damage ' . $direction);
                break;

            case "armor_penetration":
                $order = array('tp.armor_penetration ' . $direction);
                break;

            case "rate_of_fire":
                $order = array('tp.rate_of_fire ' . $direction);
                break;

            case "view_range":
                $order = array('tp.view_range ' . $direction);
                break;

            case "signal_range":
                $order = array('tp.signal_range ' . $direction);
                break;

            default:
                // default sort. See above.
                break;
        }

        $select = $this->_db->select()
            ->from(array('t' => self::TABLE_NAME))
            ->join(array('n' => Application_Model_Db_Nations::TABLE_NAME), 'n.id = t.nation_id', array('n.code AS nation_code'))
            ->join(array('tp' => Application_Model_Db_Tank_Parameters::TABLE_NAME), 'tp.tank_id = t.id', array('*'))
            ->order($order);

        $wheres = array();
        $tmpSelect = $this->_db->select();
        foreach ($tanks as $code) {
            $tmpSelect->reset(Zend_Db_Select::WHERE);
            list($nationCode, $tankCode) = explode('|', $code);
            if ( ! $nationCode || ! $tankCode) {
                continue;
            }
            $tmpSelect->where('t.code = ?', $tankCode);
            $tmpSelect->where('n.code = ?', $nationCode);
            $wheres[] = $tmpSelect->getPart(Zend_Db_Select::WHERE);
        }
        if ( ! count($wheres)) {
            return array();
        }
        foreach ($wheres as $where) {
            $select->orWhere(implode(' ', $where));
        }
        $result = $this->_db->fetchAll($select);

        return $result;
    }

}