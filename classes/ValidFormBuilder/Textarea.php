<?php
/**
 * ValidForm Builder - build valid and secure web forms quickly
 *
 * Copyright (c) 2009-2017 Neverwoods Internet Technology - http://neverwoods.com
 *
 * Felix Langfeldt <felix@neverwoods.com>
 * Robin van Baalen <robin@cattlea.com>
 *
 * All rights reserved.
 *
 * This software is released under the GNU GPL v2 License <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 *
 * @package ValidForm
 * @author Felix Langfeldt <felix@neverwoods.com>, Robin van Baalen <robin@cattlea.com>
 * @copyright 2009-2017 Neverwoods Internet Technology - http://neverwoods.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU GPL v2
 * @link http://validformbuilder.org
 */
namespace ValidFormBuilder;

/**
 * Create textarea html elements
 *
 * TextArea objects are used to create textarea html elements.
 *
 * #### Example; Add a basic textarea
 * ```php
 * $objForm->addField(
 *     "message",
 *     "Your Message",
 *     ValidForm::VFORM_TEXT,
 *     array(
 *         // Make this field required
 *         "required" => true
 *     ),
 *     array(
 *         // Error message when required state isn't met
 *         "required" => "This is a required field"
 *     ),
 *     array(
 *         "cols" => 20,
 *         "rows" => 10
 *     )
 * );
 * ```
 *
 * @package ValidForm
 * @author Felix Langfeldt <felix@neverwoods.com>
 * @author Robin van Baalen <robin@cattlea.com>
 * @version Release: 3.0.0
 */
class Textarea extends Element
{
    /**
     * Create new Textarea object
     * @internal
     */
    public function __construct($name, $type, $label = "", $validationRules = array(), $errorHandlers = array(), $meta = array())
    {
        $varRows = $this->getFieldMeta("rows", null);
        if (is_null($varRows)) {
            $this->setFieldMeta("rows", "5");
        }
        $varCols = $this->getFieldMeta("cols", null);
        if (is_null($varCols)) {
            $this->setFieldMeta("cols", "21");
        }

        parent::__construct($name, $type, $label, $validationRules, $errorHandlers, $meta);
    }

    /**
     * Render HTML
     * @internal
     * @see \ValidFormBuilder\Element::toHtml()
     */
    public function toHtml($submitted = false, $blnSimpleLayout = false, $blnLabel = true, $blnDisplayErrors = true)
    {
        $strOutput = "";

        $intDynamicCount = $this->getDynamicCount();
        for ($intCount = 0; $intCount <= $intDynamicCount; $intCount ++) {
            $strOutput .= $this->__toHtml($submitted, $blnSimpleLayout, $blnLabel, $blnDisplayErrors, $intCount);
        }

        return $strOutput;
    }

    /**
     * Render a single field's HTML
     * @internal
     * @see \ValidFormBuilder\Element::__toHtml()
     */
    public function __toHtml($submitted = false, $blnSimpleLayout = false, $blnLabel = true, $blnDisplayErrors = true, $intCount = 0)
    {
        $strOutput = "";

        $strName = ($intCount == 0) ? $this->__name : $this->__name . "_" . $intCount;
        $strId = ($intCount == 0) ? $this->__id : $this->__id . "_" . $intCount;

        $this->setConditionalMeta();

        $varValue = $this->__getValue($submitted, $intCount);

        $blnError = ($submitted && ! $this->__validator->validate($intCount) && $blnDisplayErrors) ? true : false;
        if (! $blnSimpleLayout) {
            // *** We asume that all dynamic fields greater than 0 are never required.
            if ($this->__validator->getRequired() && $intCount == 0) {
                $this->setMeta("class", "vf__required");
            } else {
                $this->setMeta("class", "vf__optional");
            }

            // *** Set custom meta.
            if ($blnError) {
                $this->setMeta("class", "vf__error");
            }

            if (! $blnLabel) {
                $this->setMeta("class", "vf__nolabel");
            }

            if (! empty($this->__hint) && ($varValue == $this->__hint)) {
                $this->setMeta("class", "vf__hint");
            }

            $strOutput = "<div{$this->__getMetaString()}>\n";

            if ($blnError) {
                $strOutput .= "<p class=\"vf__error\">{$this->__validator->getError($intCount)}</p>";
            }

            if ($blnLabel) {
                $strLabel = (! empty($this->__requiredstyle) && $this->__validator->getRequired()) ? sprintf($this->__requiredstyle, $this->__label) : $this->__label;
                if (! empty($this->__label)) {
                    $strOutput .= "<label for=\"{$strId}\"{$this->__getLabelMetaString()}>{$strLabel}</label>\n";
                }
            }
        } else {
            if (! empty($this->__hint) && ($varValue == $this->__hint)) {
                $this->setMeta("class", "vf__hint");
            }

            if ($blnError) {
                $this->setMeta("class", "vf__error");
            }

            $this->setMeta("class", "vf__multifielditem");

            $strOutput = "<div{$this->__getMetaString()}>\n";
        }

        // *** Add max-length attribute to the meta array. This is being read by the getMetaString method.
        if ($this->__validator->getMaxLength() > 0) {
            $this->setFieldMeta("maxlength", $this->__validator->getMaxLength());
        }

        $varValue = htmlspecialchars($varValue, ENT_QUOTES);

        $strOutput .= "<textarea name=\"{$strName}\" id=\"{$strId}\"{$this->__getFieldMetaString()}>{$varValue}</textarea>\n";

        if (! empty($this->__tip)) {
            $strOutput .= "<small class=\"vf__tip\"{$this->__getTipMetaString()}>{$this->__tip}</small>\n";
        }

        $strOutput .= "</div>\n";

        if (! $blnSimpleLayout && $this->__dynamic && ! empty($this->__dynamicLabel) && ($intCount == $this->getDynamicCount())) {
            $strOutput .= "<div class=\"vf__dynamic\"><a href=\"#\" data-target-id=\"{$this->__id}\" data-target-name=\"{$this->__name}\">{$this->__dynamicLabel}</a></div>\n";
        }

        return $strOutput;
    }

    /**
     * Render javascript
     * @internal
     * @see \ValidFormBuilder\Element::toJS()
     */
    public function toJS($intDynamicPosition = 0)
    {
        $strOutput = "";
        $strCheck = $this->__sanitizeCheckForJs($this->__validator->getCheck());
        $strRequired = ($this->__validator->getRequired()) ? "true" : "false";
        $intMaxLength = ($this->__validator->getMaxLength() > 0) ? $this->__validator->getMaxLength() : "null";
        $intMinLength = ($this->__validator->getMinLength() > 0) ? $this->__validator->getMinLength() : "null";

        if ($this->__dynamic || $intDynamicPosition) {
            $intDynamicCount = $this->getDynamicCount($intDynamicPosition);
            for ($intCount = 0; $intCount <= $intDynamicCount; $intCount ++) {
                $strId = ($intCount == 0) ? $this->__id : $this->__id . "_" . $intCount;
                $strName = ($intCount == 0) ? $this->__name : $this->__name . "_" . $intCount;

                // *** We asume that all dynamic fields greater than 0 are never required.
                if ($intDynamicCount > 0) {
                    $strRequired = "false";
                }

                $strOutput .= "objForm.addElement('{$strId}', '{$strName}', {$strCheck}, {$strRequired}, {$intMaxLength}, {$intMinLength}, '" . addslashes($this->__validator->getFieldHint()) . "', '" . addslashes($this->__validator->getTypeError()) . "', '" . addslashes($this->__validator->getRequiredError()) . "', '" . addslashes($this->__validator->getHintError()) . "', '" . addslashes($this->__validator->getMinLengthError()) . "', '" . addslashes($this->__validator->getMaxLengthError()) . "');\n";

                // *** Render the condition logic per dynamic field.
                $strOutput .= $this->conditionsToJs($intCount);
            }
        } else {
            $strOutput = "objForm.addElement('{$this->__id}', '{$this->__name}', {$strCheck}, {$strRequired}, {$intMaxLength}, {$intMinLength}, '" . addslashes($this->__validator->getFieldHint()) . "', '" . addslashes($this->__validator->getTypeError()) . "', '" . addslashes($this->__validator->getRequiredError()) . "', '" . addslashes($this->__validator->getHintError()) . "', '" . addslashes($this->__validator->getMinLengthError()) . "', '" . addslashes($this->__validator->getMaxLengthError()) . "');\n";

            // *** Condition logic.
            $strOutput .= $this->conditionsToJs();
        }

        return $strOutput;
    }
}
