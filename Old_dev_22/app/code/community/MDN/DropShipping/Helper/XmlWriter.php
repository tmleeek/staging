<?php

class MDN_DropShipping_Helper_XmlWriter extends Mage_Core_Helper_Abstract {

    var $xml;
    var $indent;
    var $stack = array();

    function init($indent = '  ') {
        $this->indent = $indent;
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    }

    function _indent() {
        for ($i = 0, $j = count($this->stack); $i < $j; $i++) {
            $this->xml .= $this->indent;
        }
    }

    function push($element, $attributes = array()) {
        $this->_indent();
        $this->xml .= '<' . $element;
        foreach ($attributes as $key => $value) {
            $this->xml .= ' ' . $key . '="' . htmlentities($value) . '"';
        }
        $this->xml .= ">\n";
        $this->stack[] = $element;
    }

    function element($element, $content, $attributes = array(), $contentMaxLength = null) {
        $this->_indent();
        $content = $this->removeAccent($content);
        if ($contentMaxLength && strlen($content) > $contentMaxLength)
            $content = substr($content, 0, $contentMaxLength);
        $this->xml .= '<' . $element;
        foreach ($attributes as $key => $value) {
            $this->xml .= ' ' . $key . '="' . htmlentities($value) . '"';
        }
        $this->xml .= '>' . htmlentities($content) . '</' . $element . '>' . "\n";
    }

    function emptyelement($element, $attributes = array()) {
        $this->_indent();
        $this->xml .= '<' . $element;
        foreach ($attributes as $key => $value) {
            $this->xml .= ' ' . $key . '="' . htmlentities($value) . '"';
        }
        $this->xml .= " />\n";
    }

    function pop() {
        $element = array_pop($this->stack);
        $this->_indent();
        $this->xml .= "</$element>\n";
    }

    function getXml() {
        return $this->xml;
    }

    /**
     * Remove accents
     *
     * @param unknown_type $string
     */
    public function removeAccent($string) {

        $string = $this->normalize_special_characters($string);

        return $string;
    }

    protected function normalize_special_characters($str) {
        # Quotes cleanup
        $str = str_replace(chr(ord("`")), "'", $str);        # `
        $str = str_replace(chr(ord("´")), "'", $str);        # ´
        $str = str_replace(chr(ord("„")), ",", $str);        # „
        $str = str_replace(chr(ord("`")), "'", $str);        # `
        $str = str_replace(chr(ord("´")), "'", $str);        # ´
        $str = str_replace(chr(ord("“")), "\"", $str);        # “
        $str = str_replace(chr(ord("”")), "\"", $str);        # ”
        $str = str_replace(chr(ord("´")), "'", $str);        # ´

        $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');
        $str = strtr($str, $unwanted_array);

        $str = str_replace(chr(149), "&#8226;", $str);    # bullet •
        $str = str_replace(chr(150), "&ndash;", $str);    # en dash
        $str = str_replace(chr(151), "&mdash;", $str);    # em dash
        $str = str_replace(chr(153), "&#8482;", $str);    # trademark
        $str = str_replace(chr(169), "&copy;", $str);    # copyright mark
        $str = str_replace(chr(174), "&reg;", $str);        # registration mark

        return $str;
    }

}