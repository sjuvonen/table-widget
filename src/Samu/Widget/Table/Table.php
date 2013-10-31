<?php

namespace Samu\Widget\Table;

use Exception;
use Samu\Widget\Table\TableColumn;
use Samu\Widget\Table\TableHeader;
use Samu\Widget\Table\TableFooter;
use Samu\Widget\Table\TableRow;

/**
 * Utility class for printing (X)HTML tables
 *
 * Inserted data can be a regular array or anything that implements
 * the Traversable interface.
 **/
class Table {
    private $data;
    private $columns;
    private $header;
    private $footer;
    private $caption;

    private $before;
    private $after;

    private $id;
    private $classes;

    public function __construct() {
        $this->data = array();
        $this->columns = array();
        $this->classes = array();
    }

    public function __toString() {
        try {
            ob_start();
            $this->render();
            return ob_get_clean();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Renders the table as HTML
     **/
    public function render() {
        $row_helper = new TableRow($this);
        $row_helper->before($this->before);
        $row_helper->after($this->after);

        $id = $this->id ? " id=\"{$this->id}\"" : '';
        $class = count($this->classes) ? ' class="' . implode(' ', $this->classes) . '"' : '';
        ?>

        <table<?= $id . $class ?>>
            <?php if ($this->getCaption()): ?>
                <caption><?= htmlspecialchars($this->getCaption()) ?></caption>
            <?php endif ?>

            <?= $this->getHeader() ?>
            <?= $this->getFooter() ?>

            <tbody>
                <?php foreach ($this->getData() as $i => $row): ?>
                    <?= $row_helper->render($this->extractData($row), $i) ?>
                <?php endforeach ?>
            </tbody>
        </table>

        <?php
    }

    /**
     * Install a callback into a column.
     *
     * The callback can be used to manipulate the values in the column.
     * Callback must encode HTML properly itself. It will receive two parameters:
     * - cell value
     * - reference to the table
     *
     * Callback should return a string value.
     *
     * @return Table
     **/
    public function transform($i, $callback) {
        if ($c = $this->getColumn($i)) {
            $c->transform($callback);
        }

        return $this;
    }

    /**
     * Installs a callback that will be called before a row is rendered.
     *
     * Callback will receive two parameters:
     * - reference to the row helper
     * - reference to the table
     *
     * Callback may print HTML, return value is ignored.
     *
     * @return Table
     **/
    public function before($callback) {
        $this->before = $callback;

        return $this;
    }

    /**
     * Installs a callback that will be called after a row is rendered.
     *
     * @see before($callback)
     * @return Table
     **/
    public function after($callback) {
        $this->after = $callback;

        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setClass($class) {
        $this->classes = array($class);
        return $this;
    }

    public function setClasses($classes) {
        $this->classes = $classes;
        return $this;
    }

    public function getClasses() {
        return $this->classes;
    }

    /**
     * Convenience function for setting a width for a column
     *
     * Shorthand for getHeader()->setWidth($i, $width)
     *
     * @return Table
     **/
    public function setWidth($i, $width) {
        $this->getHeader()->setWidth($i, $width);
        return $this;
    }

    public function getCaption() {
        return $this->caption;
    }

    /**
     * Sets caption for the table.
     *
     * Caption is rendered as <caption> element
     *
     * @return Table
     **/
    public function setCaption($caption) {
        $this->caption = $caption;
        return $this;
    }

    public function getData() {
        return $this->data;
    }

    /**
     * Set data for the table.
     *
     * The data can be either a regular array or something that implements
     * the Traversable interface.
     *
     * Visible columns can be specified by setColumns or setIndexes. By default
     * every column in the data set is displayed in the order of appearance.
     *
     * @return Table
     **/
    public function setData($data) {
        if (!is_array($data) && !($data instanceof \Traversable)) {
            throw new \Exception('Invalid data passed');
        }

        $this->data = $data;

        if (count($this->getColumns()) == 0 && count($data)) {
            $row = current($data);
            $cols = array_keys($this->extractData($row));
            $cols = array_combine($cols, array_fill(0, count($cols), ''));

            $this->setColumns($cols);
            $this->getHeader()->setVisible(false);
        }

        return $this;
    }

    /**
     * Return the header object
     *
     * @return TableHeader
     **/
    public function getHeader() {
        if (!$this->header) {
            $this->header = new TableHeader($this);
        }

        return $this->header;
    }

    /**
     * Return the footer object
     *
     * @return TableFooter
     **/
    public function getFooter() {
        if (!$this->footer) {
            $this->footer = new TableFooter($this);
        }

        return $this->footer;
    }

    /**
     * Return the column objects as an associative array
     *
     * @return array
     **/
    public function getColumns() {
        return $this->columns;
    }

    /**
     * Return the column corresponding to given index
     *
     * @return TableColumn
     **/
    public function getColumn($i) {
        return isset($this->columns[$i]) ? $this->columns[$i] : null;
    }

    /**
     * Define the columns as an associative array
     *
     * @return Table
     **/
    public function setColumns($columns) {
        $this->columns = array();

        foreach ($columns as $i => $defs) {
            if (!is_array($defs)) {
                $label = $defs;
            } else {
                $label = isset($defs['label']) ? $defs['label'] : null;
            }

            if (!is_null($label)) {
                $this->getHeader()->setLabel($i, $label);
            }

            $this->columns[$i] = new TableColumn($defs, $this);
        }

        return $this;
    }

    public function setIndexes($indexes) {
        $this->columns = array();

        foreach ($indexes as $i) {
            $this->columns[$i] = new TableColumn(array(), $this);
        }

        return $this;
    }

    /**
     * Convenience function for enabling sorting of a column or all columns.
     *
     * Shorthand for getHeader()->setSortable($i, $state)
     *
     * @return Table
     **/
    public function setSortable($i, $state = null) {
        if (func_num_args() == 1) {
            $this->getHeader()->setSortable($i);
        } else {
            $this->getHeader()->setSortable($i, $state);
        }

        return $this;
    }

    protected function extractData($row) {
        if (is_object($row)) {
            $row = get_object_vars($row);
        }

        if (is_array($row)) {
            return $row;
        }

        throw new \Exception("Invalid data passed");
    }
}
