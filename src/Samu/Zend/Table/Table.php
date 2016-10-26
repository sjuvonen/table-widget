<?php

namespace Samu\Zend\Table;

use Zend\Hydrator\HydratorInterface;
use Samu\Widget\Table\Table as BaseTable;

/**
 * ZF2-compatible version of the generic Table widget
 */
class Table extends BaseTable
{
    private static $default_factory;
    private $factory;
    private $hydrator;

    public static function setDefaultHydratorFactory($callable)
    {
        self::$default_factory = $callable;
    }

    public static function getDefaultHydratorFactory()
    {
        return self::$default_factory;
    }

    public function __construct()
    {
        $this->factory = static::getDefaultHydratorFactory();
    }

    public function setHydratorFactory($callable)
    {
        $this->factory = $callable;
    }

    public function getHydratorFactory()
    {
        return $this->factory;
    }

    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    public function getHydrator()
    {
        return $this->hydrator;
    }

    protected function extractData($row)
    {
        $hydrator = $this->hydrator;
        $factory = $this->factory;
        $fields = array_keys($this->getColumns());

        if (is_object($row)) {
            if (!$hydrator && $factory) {
                $hydrator = call_user_func($factory, get_class($row), $fields);
                $this->setHydrator($hydrator);
            }

            if ($hydrator) {
                $data = $hydrator->extract($row);
                return $data;
            }
        }
        return parent::extractData($row);
    }
}
