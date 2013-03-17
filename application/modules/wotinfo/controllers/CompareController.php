<?php

class Wotinfo_CompareController extends My_Controller_Action
{

    public $ajaxable = array();

    public function indexAction()
    {
        $modelTank = new Application_Model_Tank();

        $tankTypes = $modelTank->getAllTankTypes();
        $nations   = $modelTank->getAllNations();
        $levels    = $modelTank->getAllowedLevels();

        $assign = array(
            'nations'    => array(),
            'tank_types' => $tankTypes,
            'levels'     => $levels,
        );

        foreach ($nations as $nation) {
            $nationTotalTanks = 0;
            foreach ($tankTypes as $tankType) {
                if ( ! isset($nation['tank_types'])) {
                    $nation['tank_types'] = array();
                }
                $tanks = $modelTank->getAll($nation['id'], $tankType['id'], array('tank_parameters' => 1));
                $tankType['tanks'] = array();
                foreach ($tanks as $tank) {
                    $tankType['tanks'][$tank['lvl']][] = $tank;
                }
                $typeTotalTanks = count($tanks);
                $tankType['total_tanks'] = $typeTotalTanks;
                $nationTotalTanks += $typeTotalTanks;
                $nation['tank_types'][$tankType['code']] = $tankType;
            }
            $nation['total_tanks'] = $nationTotalTanks;
            $assign['nations'][$nation['code']] = $nation;
        }

        $this->view->assign($assign);
    }

    public function compareAction()
    {
        $modelTank = new Application_Model_Tank();

        $levels    = $modelTank->getAllowedLevels();
        $compare   = $this->_getParam('tanks');
        $sort      = $this->_getParam('sort');
        $direction = $this->_getParam('d');
        $scroll    = $this->_getParam('scroll');
        $direction = $direction == 'desc' ? 'desc' : 'asc';
        $assign = array(
            'compare'    => $compare,
            'parameters' => Application_Model_Db_Tank_Parameters::getAllowedParameters(),
            'levels'     => $levels,
            'sort'       => $sort,
            'direction'  => $direction,
            'scroll'     => $scroll,
        );
        $tanks = $modelTank->getTanks($compare, $sort, $direction);
        $assign['tanks'] = $tanks;
        $this->view->assign($assign);
    }

}