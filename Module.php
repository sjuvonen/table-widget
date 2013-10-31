<?php

namespace SamuTable;

/**
 * Module class for ZF2 to allow integration into ZF2 as a separate module.
 */
class Module {
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Samu\Widget\Table' => __DIR__ . '/src/Samu/Widget/Table',
                    'Samu\Zend\Table' => __DIR__ . '/src/Samu/Zend/Table',
                ),
            ),
        );
    }

    public function getConfig()
    {
        return array(
            'view_helpers' => array(
                'invokables' => array(
                    'SamuTable' => 'Samu\Zend\Table\View\Helper\Table',
                ),
            ),
        );
    }
}
