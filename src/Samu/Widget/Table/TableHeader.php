<?php

namespace Samu\Widget\Table;

/**
 * Header for the table
 */
class TableHeader extends TablePrimitive {

    const SORT_ASCENDING = 'asc';
    const SORT_DESCENDING = 'desc';

    private $labels;
    private $callbacks;
    private $widths;
    private $is_sortable;
    private $spans;
    private $url_prototype;

    private $sort_column;
    private $sort_direction = self::SORT_ASCENDING;

    public function __construct(Table $table) {
        parent::__construct($table);

        $this->callbacks = array();
        $this->widths = array();
        $this->spans = array();

        $this->setSortable(false);
    }

    public function render() {
        if (!$this->isVisible()) {
            return;
        }

        $span = 1;

        if ($this->getSortColumn() !== false) {
            $col = $this->getTable()->getColumn($this->getSortColumn());

            if ($col) {
                $classes = array_merge($col->getClasses(), array('sorted'));
                $col->setClasses($classes);
            }
        }

        ?>

        <thead>
            <tr>
                <?php foreach ($this->getTable()->getColumns() as $i => $col): ?>
                    <?php
                    if (!$col->isVisible() || $span-- > 1) {
                        continue;
                    }

                    $span = $this->getSpan($i);
                    $label = $this->getLabel($i);

                    $colspan = $span > 1 ? " colspan=\"{$span}\"" : '';
                    $classes = array(is_numeric($i) ? 'col-' . $i : $i);

                    if ($i == $this->getSortColumn()) {
                        $classes[] = 'sort-active';
                        $classes[] = 'sort-' . $this->getSortDirection();
                    }

                    if (isset($this->callbacks[$i])) {
                        $callback = $this->callbacks[$i];
                        $label = call_user_func($callback, $label, $i);
                    } else {
                        $label = htmlspecialchars($label);
                    }

                    if ($this->isSortable($i)) {

                        if ($i == $this->getSortColumn()) {
                            $dir = ($this->getSortDirection() == self::SORT_ASCENDING)
                                ? self::SORT_DESCENDING
                                : self::SORT_ASCENDING
                                ;
                        } else {
                            $dir = self::SORT_ASCENDING;
                        }

                        $url = $this->getUrlPrototype();
                        $url = str_replace(':direction', $dir, $url);
                        $url = str_replace(':column', $i, $url);
                        $url = htmlspecialchars($url);

                        $label = "<a href=\"{$url}\">{$label}</a>";
                    }

                    if (count($classes)) {
                        $class = implode(' ', $classes);
                        $class = " class=\"{$class}\"";
                    }

                    if (isset($this->widths[$i])) {
                        $w = htmlspecialchars($this->widths[$i]) . 'px';
                        $width = " style=\"width: {$w}\"";
                    } else {
                        $width = '';
                    }

                    print "<th{$class}{$width}{$colspan}>{$label}</th>\n";
                    ?>
                <?php endforeach ?>
            </tr>
        </thead>

        <?php
    }

    /**
     * Return a label for given column
     *
     * @return string
     */
    public function getLabel($i) {
        return $this->labels[$i];
    }

    /**
     * Set a label for given column
     *
     * @return TableHeader
     */
    public function setLabel($i, $label) {
        $this->setVisible(true);
        $this->labels[$i] = $label;

        return $this;
    }

    /**
     * Return colspan for given header section
     *
     * @return int (1-n)
     */
    public function getSpan($i) {
        return isset($this->spans[$i]) ? $this->spans[$i] : 1;
    }

    /**
     * Set colspan for given header section
     *
     * @return TableHeader
     */
    public function setSpan($i, $width) {
        $this->spans[$i] = max($width, 1);

        return $this;
    }

    /**
     * Set column width
     *
     * @return TableHeader
     */
    public function setWidth($i, $width) {
        $this->widths[$i] = $width;
    }

    /**
     * Return sorting state for given column
     *
     * @return bool
     */
    public function isSortable($i) {
        if (!is_array($this->is_sortable)) {
            return (bool)$this->is_sortable;
        }

        return isset($this->is_sortable[$i]) ? $this->is_sortable[$i] : false;
    }

    /**
     * Set sort state for the given column (or all of them)
     *
     * @return TableHeader
     */
    public function setSortable($i, $state = null) {
        if (func_num_args() == 1) {
            $this->is_sortable = (bool)$i;
        } else {
            if (!is_array($this->is_sortable)) {
                $this->is_sortable = [];
            }
            $this->is_sortable[$i] = (bool)$state;
        }

        return $this;
    }

    /**
     * Return the URL prototype used for sorting
     */
    public function getUrlPrototype() {
        return $this->url_prototype;
    }

    /**
     * Set the URL prototype used for sorting
     *
     * The prototype should contain the following placeholders:
     *   :direction (sort direction)
     *   :column (sort column)
     *
     * @return TableHeader
     */
    public function setUrlPrototype($url) {
        $this->url_prototype = $url;

        return $this;
    }

    /**
     * Install a callback for transforming the header section value
     *
     * @return TableHeader
     */
    public function transform($i, $callback) {
        if (!is_callable($callback)) {
            throw new \Exception('Invalid callback passed');
        }

        $this->callbacks[$i] = $callback;

        return $this;
    }

    /**
     * Return current sort column or NULL if nothing is setCaption
     *
     * @return column index | NULL
     */
    public function getSortColumn() {
        return $this->sort_column;
    }

    /**
     * Set sort column
     *
     * @return TableHeader
     */
    public function setSortColumn($col) {
        $this->sort_column = $col;

        return $this;
    }

    /**
     * Return sort direction
     *
     * Default is 'asc' for ascending
     *
     * @return asc | desc
     */
    public function getSortDirection() {
        return $this->sort_direction;
    }

    /**
     * Set the sort direction
     *
     * Allowed values are 'asc' and 'desc'
     *
     * @return TableHeader
     */
    public function setSortDirection($dir) {
        $this->sort_direction = $dir;

        return $this;
    }

}
