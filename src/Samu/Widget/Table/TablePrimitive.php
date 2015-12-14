<?php

namespace Samu\Widget\Table;

/**
 * Abstract base class for table sub-elements such as header, footer and columns
 **/
abstract class TablePrimitive {
    private $table;
    private $is_visible;

    private $attrs;

    abstract public function render();

    public function __construct(Table $table) {
        $this->table = $table;
        $this->is_visible = false;
        $this->attrs = array(
            'class' => array(),
        );
    }

    public function __toString() {
        if (!$this->isVisible()) {
            return '';
        }

        ob_start();

        $this->render();

        return ob_get_clean();
    }

    public function setId($id) {
        $this->setAttribute('id', $id);
        return $this;
    }

    public function getClasses() {
        return $this->getAttribute('class');
    }

    public function setClasses($classes) {
        $this->setAttribute('class', $classes);
        return $this;
    }

    public function setClass($class) {
        $this->setAttribute('class', array($class));
        return $this;
    }

    /**
     * Return the visibility state
     *
     * @return bool
     **/
    public function isVisible() {
        return $this->is_visible;
    }

    public function setVisible($state) {
        $this->is_visible = (bool)$state;
        return $this;
    }

    protected function getTable() {
        return $this->table;
    }

    protected function getAttribute($name) {
        return isset($this->attrs[$name]) ? $this->attrs[$name] : null;
    }

    protected function setAttribute($name, $value) {
        $this->attrs[$name] = $value;
    }

    protected function getAttributes()
    {
        return $this->attrs;
    }

    public function attributes()
    {
        $attrs = '';
        foreach ($this->getAttributes() as $name => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
            $attrs .= sprintf(' %s="%s"', htmlspecialchars($name), htmlspecialchars($value, ENT_QUOTES | ENT_HTML5));
        }
        return $attrs;
    }
}
