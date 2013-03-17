<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->setEncoding('UTF-8');
    }

    protected function _initFrontControllerOutput() {

        $this->bootstrap('FrontController');
        $frontController = $this->getResource('FrontController');

        $response = new Zend_Controller_Response_Http;
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
        $frontController->setResponse($response);

        $frontController->setParam('useDefaultControllerAlways', false);

        return $frontController;
    }

    protected function _initRoute()
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'production');
        $router->addConfig($config, 'routes');
    }

    protected function _initAutoload()
    {
        $this->getApplication()->getAutoloader()->registerNamespace('Sch');
        $this->getApplication()->getAutoloader()->registerNamespace('My');
    }

    protected function _initWotinfoAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Wotinfo_',
            'basePath'  => APPLICATION_PATH .'/modules/wotinfo',
            'resourceTypes' => array (
                'form' => array(
                    'path' => 'forms',
                    'namespace' => 'Form',
                ),
                'model' => array(
                    'path' => 'models',
                    'namespace' => 'Model',
                ),
            )
        ));
        return $autoloader;
    }

}

