<?php

namespace Samu\Widget\Table;

/**
 * Footer for the table
 **/
class TableFooter extends TablePrimitive {

    private $content;
    private $spans = array();

    public function render() {
        if (!$this->isVisible()) {
            return;
        }
        $span = 1;
        ?>
        <tfoot>
            <tr>
                <?php if ($this->isUnified()): ?>
                    <td colspan="<?= count($this->getTable()->getColumns()) ?>">
                        <?= $this->content ?>
                    </td>
                <?php else: ?>
                    <?php foreach ($this->getTable()->getColumns() as $i => $col): ?>
                        <?php if ($col->isVisible()): ?>
                            <?php
                            if ($span-- > 1) {
                                continue;
                            }

                            $span = $this->getSpan($i);
                            $colspan = $span > 1 ? " colspan=\"{$span}\"" : '';
                            $content = $this->getContent($i);

                            print "<td{$colspan}>{$content}</td>\n";
                            ?>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endif ?>
            </tr>
        </tfoot>
        <?php
    }

    /**
     * Return content for given footer section or NULL if nothing is set
     *
     * @return mixed
     **/
    public function getContent($i = null) {
        if (!func_num_args()) {
            return $this->isUnified() ? $this->content : null;
        } else {
            return $this->isUnified()
                ? null
                : (isset($this->content[$i]) ? $this->content[$i] : null)
                ;
        }
    }

    /**
     * Set content for given footer section
     *
     * If only one parameter is passed, the footer will be made single-cell
     *
     * @return TableFooter
     **/
    public function setContent($i, $content = '') {
        $this->setVisible(true);

        if (func_num_args() == 1) {
            $this->content = $i;
        } else {
            $this->content[$i] = $content;
        }

        return $this;
    }

    /**
     * Return colspan for given footer section
     *
     * @return int
     **/
    public function getSpan($i) {
        return isset($this->spans[$i]) ? $this->spans[$i] : 1;
    }

    /**
     * Set colspan for given section
     *
     * @return TableFooter
     **/
    public function setSpan($i, $width) {
        $this->spans[$i] = max($width, 1);

        return $this;
    }

    /**
     * Tell if footer is single-cell
     *
     * @return bool
     **/
    public function isUnified() {
        return !is_array($this->content);
    }
}
