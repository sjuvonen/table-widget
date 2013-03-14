<?php

namespace Samu\Zend\Table;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Samu\Widget\Table\Table as BaseTable;

/**
 * ZF2-compatible version of the generic Table widget
 **/
class Table extends BaseTable {
    private static $default_factory;
    private $factory;
    private $hydrator;

    public static function setDefaultHydratorFactory($callable) {
        self::$default_factory = $callable;
    }

    public static function getDefaultHydratorFactory() {
        return self::$default_factory;
    }

    public function __construct() {
        $this->factory = static::getDefaultHydratorFactory();
    }

    public function setHydratorFactory($callable) {
        $this->factory = $callable;
    }

    public function getHydratorFactory() {
        return $this->factory;
    }

    public function setHydrator(HydratorInterface $hydrator) {
        $this->hydrator = $hydrator;
    }

    public function getHydrator() {
        return $this->hydrator;
    }

    protected function extractData($row) {
        $h = $this->hydrator;
        $f = $this->factory;

        if (is_object($row)) {
            if (!$h && $f) {
                $h = call_user_func($f, get_class($row));
                $this->setHydrator($h);
            }

            if ($h) {
                $data = $h->extract($row);
                return $data;
            }
        }
        return parent::extractData($row);
    }
}
