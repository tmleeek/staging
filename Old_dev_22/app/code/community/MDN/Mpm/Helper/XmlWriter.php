<?php


class MDN_Mpm_Helper_XmlWriter extends Mage_Core_Helper_Abstract
{

    protected $xml;
    protected $indent;
    protected $stack = array();

    /**
     * Init the xml string
     *
     * @param string $indent
     */
    public function init($indent = '  ')
    {
        $this->indent = $indent;
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    }

    /**
     * Added an indent to xml
     */
    protected function indent()
    {
        for ($i = 0, $j = count($this->stack); $i < $j; $i++) {
            $this->xml.= $this->indent;
        }
    }

    /**
     * Open a new element
     *
     * @param string $element
     * @param array $attributes
     */
    public function push($element, $attributes = array())
    {
        $this->indent();
        $this->xml.= '<' . $element;
        foreach ($this->cleanAttributes($attributes) as $key => $value) {
            if ($value !== '') {
                $this->xml.= ' ' . $key . '="' . $value . '"';
            }
        }
        $this->xml.= ">\n";
        $this->stack[] = $element;
    }

    /**
     * Add a new element
     *
     * @param string $element
     * @param string $content
     * @param array $attributes
     */
    public function element($element, $content, $attributes = array())
    {
        $this->indent();
        $this->xml.= '<' . $element;
        foreach ($this->cleanAttributes($attributes) as $key => $value) {
            if ($value !== '') {
                $this->xml.= ' ' . $key . '="' . $value . '"';
            }
        }

        $this->xml.= '>' . ($content) . '</' . $element . '>' . "\n";
    }

    /**
     * Add a new empty element
     *
     * @param string $element
     * @param array $attributes
     */
    public function emptyelement($element, $attributes = array())
    {
        $this->indent();
        $this->xml.= '<' . $element;
        foreach ($this->cleanAttributes($attributes) as $key => $value) {
            if ($value !== '') {
                $this->xml.= ' ' . $key . '="' . $value . '"';
            }
        }
        $this->xml.= " />\n";
    }

    /**
     * Clean attributes
     *
     * @param array $attributes
     * @return array
     */
    protected function cleanAttributes($attributes)
    {
        foreach ($attributes as &$value) {
            $value = htmlspecialchars($value);
        }

        return $attributes;
    }

    /**
     * Close an element
     */
    public function pop()
    {
        $element = array_pop($this->stack);
        $this->indent();
        $this->xml.= "</$element>\n";
    }

    /**
     * Return the xml
     *
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Return the xml and set to empty
     *
     * @return string
     */
    public function flush()
    {
        $content = $this->xml;
        $this->xml = '';

        return $content;
    }

    /**
     * Add an enclose CData
     *
     * @param string $value
     * @return string
     */
    public function encloseCData($value)
    {
        return '<![CDATA['.$value.']]>';
    }

    /**
     * Return last errors generated
     *
     * @return array
     */
    public function getErrors()
    {
        $errors = array();
        foreach (libxml_get_errors() as $error) {
            $errors[] = $this->getError($error);
        }
        libxml_clear_errors();

        return $errors;
    }

    /**
     * Return an error
     *
     * @param XmlError $error
     * @return string
     */
    protected function getError($error)
    {
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return = "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $return = "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return = "Fatal Error $error->code: ";
                break;
        }

        $return.= trim($error->message);
        if ($error->file) {
            $return.= " in $error->file";
        }
        $return.= " on line <b>$error->line";

        return $return;
    }
}