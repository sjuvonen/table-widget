<?php

namespace Samu\Zend\Table\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Simple Zend Framework 2 view helper to integrate the Table widget into ZF2.
 **/
class Table extends AbstractHelper {

    /**
     * @param $data Data for the table
     * @param $columns Column definitions
     * @param $index_only set to TRUE if $columns is only a list of visible indexes
     * @return \Samu\Widget\Table
     **/
    public function __invoke($data = null, $columns = null, $index_only = false) {
        $table = new \Samu\Zend\Table\Table();

        if ($columns) {
            if ($index_only) {
                $table->setIndexes($columns);
            } else {
                $table->setColumns($columns);
            }
        }

        if ($data) {
            $table->setData($data);
        }

        return $table;
    }
}
