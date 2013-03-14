<?php

namespace Samu\Widget\Table;

/**
 * Helper for rendering columns and cells for table
 **/
class TableColumn extends TablePrimitive {

    private static $spanned = 1;

    private $spans;

    private $callback;
    private $before;
    private $after;

    public function __construct($defs, Table $table) {
        parent::__construct($table);

        $this->setVisible(true);
        $this->spans = array();
    }

    public function render($value = null, $col_i = 0, $row_i = 0, $row = null) {
        if (!$this->isVisible()) {
            return;
        }

        if (self::$spanned-- > 1) {
            return;
        }

        if ($this->before) {

        }

        self::$spanned = $this->getSpan($row_i);

        if ($this->callback) {
            $value = call_user_func($this->callback, $value, $col_i, $row);
        } else {
            $value = htmlspecialchars($value);
        }

        $classes = array(is_numeric($col_i) ? 'column-' . $col_i : $col_i);
        $classes = array_merge($this->getClasses(), $classes);

        if (count($classes)) {
            $class = implode(' ', $classes);
            $class = " class=\"{$class}\"";
        }

        $colspan = self::$spanned;
        $colspan = $colspan > 1 ? " colspan=\"{$colspan}\"" : '';

        print "<td{$class}{$colspan}>{$value}</td>\n";
    }

    /**
     * Install a callback for manipulating value of a cell
     *
     * @return TableColumn
     **/
    public function transform($callback) {
        if (!is_null($callback) && !is_callable($callback)) {
            throw new \Exception('Invalid callback passed');
        }

        $this->callback = $callback;

        return $this;
    }

    /**
     * Return colspan for the given column index
     **/
    public function getSpan($i) {
        return isset($this->spans[$i]) ? $this->spans[$i] : 1;
    }

    /**
     * Set colspan for given column index
     *
     * @return TableColumn
     **/
    public function setSpan($i, $width) {
        $this->spans[$i] = max($width, 1);

        return $this;
    }

}
