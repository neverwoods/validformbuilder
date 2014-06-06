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
 * Page Class
 *
 * @package ValidForm
 * @author Robin van Baalen
 */
class Page extends Base
{

    protected $__class;

    protected $__style;

    protected $__elements;

    protected $__header;

    protected $__id;

    protected $__isOverview;

    public function __construct($id = "", $header = "", $meta = array())
    {
        $this->__header = $header;
        $this->__class = (isset($meta["class"])) ? $meta["class"] : "";
        $this->__style = (isset($meta["style"])) ? $meta["style"] : "";
        $this->__id = (empty($id)) ? $this->getRandomId("vf__page") : $id;
        $this->__isOverview = (isset($meta["overview"])) ? $meta["overview"] : false;

        $this->__elements = new Collection();
    }

    public function toHtml($submitted = false, $blnSimpleLayout = false, $blnLabel = true, $blnDisplayError = true)
    {
        $strClass = (! empty($this->__class)) ? " class=\"{$this->__class} vf__page\"" : "class=\"vf__page\"";
        $strStyle = (! empty($this->__style)) ? " style=\"{$this->__style}\"" : "";
        $strId = " id=\"{$this->__id}\"";
        $strOutput = "<div {$strClass}{$strStyle}{$strId}>\n";
        if (! empty($this->__header)) {
            $strOutput .= "<h2>{$this->__header}</h2>\n";
        }

        if (! $this->__isOverview) {
            foreach ($this->__elements as $field) {
                $strOutput .= $field->toHtml($submitted, $blnSimpleLayout, $blnLabel, $blnDisplayError);
            }
        }

        $strOutput .= "</div>\n";

        return $strOutput;
    }

    public function addField($objField)
    {
        if (get_class($objField) == "ValidFormBuilder\\Fieldset") {
            $objField->setMeta("parent", $this, true);
            $this->__elements->addObject($objField);
        } else {
            if ($this->__elements->count() == 0) {
                $objFieldset = new Fieldset();
                $this->__elements->addObject($objFieldset);
            }

            $objFieldset = $this->__elements->getLast();

            $objField->setMeta("parent", $objFieldset, true);
            $objFieldset->getFields()->addObject($objField);

            if ($objField->isDynamic() && get_class($objField) !== "ValidFormBuilder\\MultiField"
                    && get_class($objField) !== "ValidFormBuilder\\Area") {
                $objHidden = new Hidden($objField->getId() . "_dynamic", ValidForm::VFORM_INTEGER, array(
                    "default" => 0,
                    "dynamicCounter" => true
                ));

                $objFieldset->addField($objHidden);

                $objField->setDynamicCounter($objHidden);
            }
        }
    }

    public function toJS($intDynamicPosition = 0)
    {
        $strReturn = "objForm.addPage('" . $this->getId() . "');\n";

        foreach ($this->__elements as $field) {
            $strReturn .= $field->toJS($intDynamicPosition);
        }

        return $strReturn;
    }

    public function isValid()
    {
        return $this->__validate();
    }

    public function isDynamic()
    {
        return false;
    }

    public function hasFields()
    {
        return ($this->__elements->count() > 0) ? true : false;
    }

    public function getFields()
    {
        return $this->__elements;
    }

    /**
     * Get the short header if available.
     * If no short header is set (meta 'summaryLabel' on the Page object),
     * the full-length regular header is returned.
     *
     * @return string Page (short)header as a string
     */
    public function getShortHeader()
    {
        $strReturn = $this->getHeader();
        $strShortLabel = $this->getMeta("summaryLabel", null);

        if (strlen($strShortLabel) > 0) {
            $strReturn = $strShortLabel;
        }

        return $strReturn;
    }

    public function getRandomId($name)
    {
        $strReturn = $name;

        if (strpos($name, "[]") !== false) {
            $strReturn = str_replace("[]", "_" . rand(100000, 900000), $name);
        } else {
            $strReturn = $name . "_" . rand(100000, 900000);
        }

        return $strReturn;
    }

    private function __validate()
    {
        $blnReturn = true;

        foreach ($this->__elements as $field) {
            if (! $field->isValid()) {
                $blnReturn = false;
                break;
            }
        }

        return $blnReturn;
    }
}
