<?php
namespace ValidFormBuilder;

/**
 * ValidForm Builder - build valid and secure web forms quickly
 *
 * Copyright (c) 2009-2012, Felix Langfeldt <flangfeldt@felix-it.com>.
 * All rights reserved.
 *
 * This software is released under the GNU GPL v2 License <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 *
 * @package ValidForm
 * @author Felix Langfeldt <flangfeldt@felix-it.com>
 * @copyright 2009-2012 Felix Langfeldt <flangfeldt@felix-it.com>
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU GPL v2
 * @link http://code.google.com/p/validformbuilder/
 */

/**
 * Button Class
 *
 * @package ValidForm
 * @author Felix Langfeldt
 * @version Release: 0.2.0
 *
 */
class Button extends Base
{

    protected $__id;

    protected $__label;

    protected $__type;

    public function __construct($label, $meta = array())
    {
        $this->__label = $label;
        $this->__meta = $meta;
        $this->__type = (isset($meta["type"])) ? $meta["type"] : "submit";
        $this->__id = $this->__generateId();

        $this->setFieldMeta("class", "vf__button");

        // *** Set label & field specific meta
        $this->__initializeMeta();

        $strFieldId = $this->getFieldMeta("id", null);
        if (is_null($strFieldId)) {
            $this->setFieldMeta("id", $this->__id);
        }
    }

    /**
     * This method is used to initialize this object from an array structure.
     * @return array
     */
    public function getFingerprint()
    {
        return ["label", "meta"];
    }

    public function toHtml($submitted = false, $blnSimpleLayout = false, $blnLabel = true, $blnDisplayErrors = true)
    {
        if (! empty($this->__disabled)) {
            $this->setFieldMeta("disabled", "disabled");
        }

        $strReturn = "<input type=\"{$this->__type}\" value=\"{$this->__label}\"{$this->__getFieldMetaString()} />\n";

        return $strReturn;
    }

    public function isValid()
    {
        return true;
    }

    public function isDynamic()
    {
        return false;
    }

    public function hasFields()
    {
        return false;
    }

    private function __generateId()
    {
        return strtolower(ValidForm::getStrippedClassName(get_class($this))) . "_" . mt_rand();
    }
}
