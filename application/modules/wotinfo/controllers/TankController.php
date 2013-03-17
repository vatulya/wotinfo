<?php

class Wotinfo_TankController extends My_Controller_Action
{

    public $ajaxable = array();

    public function indexAction()
    {
        $modelTank = new Application_Model_Tank();

        $assign = array();

        $this->view->assign($assign);
    }

}