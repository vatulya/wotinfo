<?php

class Wotinfo_IndexController extends My_Controller_Action
{

    public $ajaxable = array();

    public function indexAction()
    {

        $this->_redirect($this->_helper->Url->url(array(), 'compare', true));


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

}