<?php

class Application_Model_Tank extends Application_Model_Abstract
{

    protected $_modelDb;
    protected $_modelTankTypesDb;
    protected $_modelNationsDb;

    public function __construct()
    {
        $this->_modelDb            = new Application_Model_Db_Tanks();
        $this->_modelTankTypesDbDb = new Application_Model_Db_Tank_Types();
        $this->_modelNationsDbDb   = new Application_Model_Db_Nations();
    }

    public function getAll($nationId = null, $tankTypeId = null, array $params = array())
    {
        $tanks = $this->_modelDb->getAll($nationId, $tankTypeId, $params);
        return $tanks;
    }

    public function getAllTankTypes()
    {
        $tankTypes = $this->_modelTankTypesDbDb->getAll();
        return $tankTypes;
    }

    public function getAllNations()
    {
        $nations = $this->_modelNationsDbDb->getAll();
        return $nations;
    }

    public function getAllowedLevels()
    {
        $toRoman = function($number)
        {
            if(!$number = abs($number)) return 0;
            $table = array(900=>'CM',500=>'D',400=>'CD',100=>'C',90=>'XC',50=>'L',40=>'XL',10=>'X',9=>'IX',5=>'V',4=>'IV',1=>'I');
            $result = str_repeat('M',$number/1000);
            while($number)
            {
                foreach($table as $part=>$fragment) if($part<=$number) break;
                $amount = (int)($number/$part);
                $number -= $part*$amount;
                $result .= str_repeat($fragment,$amount);
            }
            return $result;
        };

        $levels = array();
        $maxLevel = 10;
        $currentLevel = 1;
        while ($currentLevel <= $maxLevel) {
            $levels[$currentLevel] = array(
                'roman' => $toRoman($currentLevel),
                'arabic' => $currentLevel,
            );
            $currentLevel++;
        }
        return $levels;
    }

    public function getByCode($code, array $params = array())
    {
        list($nationCode, $tankCode) = explode('|', $code);
        if ( ! $nationCode || ! $tankCode) {
            return false;
        }
        $tank = $this->_modelDb->getByCode($nationCode, $tankCode, $params);
        return $tank;
    }

    public function truncateTanks()
    {
        $this->_modelDb->truncateTanks();
    }

    public function insert(array $tank, array $parameters)
    {
        $result = $this->_modelDb->insert($tank, $parameters);
        return $result;
    }

    public function getTanks(array $tanks, $sort = 'lvl', $direction = 'asc')
    {
        $direction = $direction == 'desc' ? false : true;
        $tanks = $this->_modelDb->getTanks($tanks, $sort, $direction);
        return $tanks;
    }

}
