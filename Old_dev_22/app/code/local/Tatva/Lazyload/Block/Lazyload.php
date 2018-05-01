<?php

class Tatva_Lazyload_Block_Lazyload extends Mage_Core_Block_Template
{
    
    protected $_links = array();
      
    protected function _construct()
    {
        
          $this->addData(array(
                'cache_lifetime'    => 5,
                'cache_tags'        => array("lazyload"),
            ));
        $this->setTemplate('page/html/footer/lazyloading.javascript.phtml');
    }
    
    
    public function getLinks()
    {
        return $this->_links;
    }
     public function addLink($path, $type, $position , $attributes=array())
    {
        $link = new Varien_Object(array(
            'path'         => $path,
            'type'           => $type,
            'attributes'    => $attributes
        ));

        $this->_links[$this->_getNewPosition($position)] = $link;
        if (intval($position) > 0) {
             ksort($this->_links);
        }

        return $this;
    }
    
     protected function _getNewPosition($position = 0)
    {
        if (intval($position) > 0) {
            while (isset($this->_links[$position])) {
                $position++;
            }
        } else {
            $position = 0;
            foreach ($this->_links as $k=>$v) {
                $position = $k;
            }
            $position += 10;
        }
        return $position;
    }

}


?>