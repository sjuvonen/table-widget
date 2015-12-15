<?php

namespace Samu\Widget\Table;

/**
 * Helper for rendering table row
 **/
class TableRow extends TablePrimitive {
    private $before;
    private $after;

    private $row_index;
    private $data;

    public function render($row = null, $row_i = 0) {
        $this->row_index = $row_i;
        $this->data = $row;

        if ($this->before) {
            $callback = $this->before;
            call_user_func($callback, $this, $this->getTable());
        }

        ?>
        <tr<?= $this->attributes() ?>>
            <?php foreach ($this->getTable()->getColumns() as $i => $col): ?>
                <?= $col->render(isset($row[$i]) ? $row[$i] : null, $i, $row_i, $row) ?>
            <?php endforeach ?>
        </tr>
        <?php
    }

    /**
     * Install a callback that will be called before the row is rendered
     *
     * @return TableRow
     **/
    public function before($callback) {
        if (!is_null($callback) && !is_callable($callback)) {
            throw new \Exception('Invalid callback passed');
        }

        $this->before = $callback;
        return $this;
    }

    /**
     * Install a callback that will be called after the row is rendered
     *
     * @return TableRow
     **/
    public function after($callback) {
        if (!is_null($callback) && !is_callable($callback)) {
            throw new \Exception('Invalid callback passed');
        }

        $this->after = $callback;
        return $this;
    }

    /**
     * Return the data contained in current row
     *
     * @return array
     **/
    public function getData() {
        return $this->data;
    }

    /**
     * Return the line number / index for current row
     *
     * Index will correspond to the indexes in source data
     *
     * @return mixed
     **/
    public function getIndex() {
        return $this->row_index;
    }
}
