<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 * @author Andrei
 */
class Aitoc_Aitsys_Model_Rewriter_Observer
{
    public function init($observer)
    {try{
        Aitoc_Aitsys_Model_Rewriter_Autoload::register();
        }catch(Exception $e) { Mage::log((string)$e, false, 'rewriter.log', true);}
    }
    
    public function clearCache($observer)
    {
        // this part for flush magento cache
        $tags = $observer->getTags();
        $rewriter = new Aitoc_Aitsys_Model_Rewriter();
        if (null !== $tags) {
            if (empty($tags) || !is_array($tags) || in_array('aitsys', $tags)) {
                return $rewriter->prepare();
            }
        }
        
        // this part for mass refresh
        $cacheTypes = Mage::app()->getRequest()->getParam('types');
        if ($cacheTypes) {
            $cacheTypesArray = $cacheTypes;
            if (!is_array($cacheTypesArray)) {
                $cacheTypesArray = array($cacheTypesArray);
            }
            if (in_array('aitsys', $cacheTypesArray)) {
                return $rewriter->prepare();
            }
        }
        
        // this part is for flush cache storage
        if (null === $cacheTypes && null === $tags) {
            return $rewriter->prepare();
        }
    }
}