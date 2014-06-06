<?php
namespace ValidFormBuilder;

/**
 * ValidForm Builder - build valid and secure web forms quickly
 *
 * Copyright (c) 2009-2013 Neverwoods Internet Technology - http://neverwoods.com
 *
 * Felix Langfeldt <felix@neverwoods.com>
 * Robin van Baalen <robin@neverwoods.com>
 *
 * All rights reserved.
 *
 * This software is released under the GNU GPL v2 License <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 *
 * @package ValidForm
 * @author Felix Langfeldt <felix@neverwoods.com>, Robin van Baalen <robin@neverwoods.com>
 * @copyright 2009-2013 Neverwoods Internet Technology - http://neverwoods.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU GPL v2
 * @link http://validformbuilder.org
 */

/**
 * Navigation Class
 *
 * @package ValidForm
 * @author Felix Langfeldt
 */
class Navigation extends Base
{

    protected $__fields;

    public function __construct($meta = array())
    {
        $this->__meta = $meta;
        $this->__initializeMeta();

        $this->__fields = new Collection();
    }

    public function addButton($label, $options = array())
    {
        $objButton = new Button($label, $options);
        // *** Set the parent for the new field.
        $objButton->setMeta("parent", $this, true);

        $this->__fields->addObject($objButton);

        return $objButton;
    }

    public function addHtml($html)
    {
        $objString = new String($html);
        $objString->setMeta("parent", $this, true);
        $this->__fields->addObject($objString);

        return $objString;
    }

    public function toHtml($submitted = false, $blnSimpleLayout = false, $blnLabel = true, $blnDisplayError = true)
    {
        $this->setConditionalMeta();

        $this->setMeta("class", "vf__navigation vf__cf");
        $strReturn = "<div{$this->__getMetaString()}>\n";

        foreach ($this->__fields as $field) {
            $strReturn .= $field->toHtml($submitted, $blnSimpleLayout, $blnLabel, $blnDisplayError);
        }

        $strReturn .= "</div>\n";

        return $strReturn;
    }

    public function toJS($intDynamicPosition = 0)
    {
        $strReturn = "";

        foreach ($this->__fields as $field) {
            $strReturn .= $field->toJS($intDynamicPosition);
        }

        return $strReturn;
    }

    public function isValid()
    {
        return true;
    }

    public function getFields()
    {
        return $this->__fields;
    }

    public function getValue()
    {
        return true;
    }

    public function getId()
    {
        return null;
    }

    public function isDynamic()
    {
        return false;
    }

    public function getType()
    {
        return 0;
    }

    public function getHeader()
    {
        return;
    }

    public function hasFields()
    {
        return ($this->__fields->count() > 0) ? true : false;
    }
}
