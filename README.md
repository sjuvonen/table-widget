table-widget
============

Table widget for generating rich and customizable tables from datasets easily. Also featured is a view helper for integrating the table into Zend Framework 2 (ZF2).

Instructions for ZF2 integration:

I suppose the easiest way is to copy directory ./src/Samu/ under any ZF2 module in your Zend project.
For example "zendroot/module/MyApp/src/Samu/". After that it is enough to add namespace Samu into
your module's autoloader configuration and also define the view helper in module.config.php.

Example Module::getAutoloaderConfig():

    public function getAutoloaderConfig() {
            return array(
                    'Zend\Loader\ClassMapAutoloader' => array(
                            array(),
                    ),
                    'Zend\Loader\StandardAutoloader' => array(
                            'namespaces' => array(
                                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,

                    // Simply add this row:
                                    'Samu' => __DIR__ . '/src/Samu',
                            ),
                    ),
            );
    }

module.config.php:

    return array(
        'view_helpers' => array(
            'invokables' => array(
                'Table' => 'Samu\Zend\Table',
            ),
        ),
    );

Then in your view template you can access the view helper by simply calling $this->table().

