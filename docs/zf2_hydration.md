## Using hydrators with the ZF2 table view helper

Hydrators are quite nice when the items to be displayed are 'real' objects with private or protected members instead of simple stdClass objects or plain arrays. Hydrators map the objects into arrays that can be then used with the table widget. The ZF2 plugin for Table widget comes with built-in support for hydrators.

For basic information about using hydrators with objects, read this article: https://github.com/doctrine/DoctrineModule/blob/master/docs/hydrator.md

### Initialing the table with a hydrator
An easy way to setup the table widget with a hydrator is to set a hydrator factory somewhere in the initialiation phase of your ZF2 app. Any callable item can be a hydrator factory. A single parameter will be passed to the callable: FQCN of the class for which a hydrator is being created.

Here's how to set the factory in onBootstrap method of your central module:

```php
namespace MyModule;

// Will extract properties using public API of the object
use Zend\Stdlib\Hydrator\ClassMethods as ZendHydrator;

class Module {
    public function onBootstrap($e) {
        \Samu\Zend\Table\Table::setDefaultHydratorFactory(function($class) {
            // Use the same hydrator for all classes
            return ZendHydrator();
        });
    }
}
```

Here's an extended example demonstrating usage of hydrators with Doctrine (requires DoctrineModule):

```php
namespace MyModule;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class Module {
    public function onBootstrap($e) {
        $sm = $e->getApplication()->getServiceManager();

        \Samu\Zend\Table\Table::setDefaultHydratorFactory(function($class) use ($sm) {
            $em = $sm->get('Doctrine\ORM\EntityManager');
            $hydrator = new DoctrineHydrator($em, $class);
            return $hydrator;
        });
    }
}
```

**NOTE** The factory will be called only once for each instance (of the table)! The class name that is passed to the hydrator is deduced from the first row of the result set. This is important to remember when the result is not homogenous.
