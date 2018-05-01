<?php
require_once 'Mage/CatalogSearch/controllers/ResultController.php';
class Tatva_Ajax_ResultController extends Mage_CatalogSearch_ResultController
 {
    	public function indexAction(){
    		$request = $this->getRequest();
    		$search_q = Mage::helper('catalogsearch')->getQuery();

            $search_q->setStoreId(Mage::app()->getStore()->getId());
            if ($search_q->getQueryText())
                {
                 if(Mage::helper('catalogsearch')->isMinQueryLength())
                   {
                    $search_q->setId(0)
                        ->setIsActive(1)
                        ->setIsProcessed(1);
                   }
                  else
                   {
                    if ($search_q->getId())
                      {
                        $search_q->setPopularity($search_q->getPopularity()+1);
                      }
                    else
                      {
                        $search_q->setPopularity(1);
                      }

                    if ($search_q->getRedirect())
                      {
                        $search_q->save();
                        $this->getResponse()->setRedirect($search_q->getRedirect());
                        return;
                       }
                    else
                      {
                        $search_q->prepare();
                      }
                    }

                Mage::helper('catalogsearch')->checkNotes();
                if($request->isXmlHttpRequest())
                  {
    				$this->getResponse()->setBody($this->_getAjaxSearchResult());
                  }
                else
                  {
    	    		$this->loadLayout();
    	            $this->_initLayoutMessages('catalog/session');
    	            $this->_initLayoutMessages('checkout/session');
    	            $this->renderLayout();
    	    	  }

                if (!Mage::helper('catalogsearch')->isMinQueryLength())
                  {
                    $search_q->save();
                  }
              }
            else
              {
                $this->_redirectReferer();
              }
    	}
    	protected function _getAjaxSearchResult(){
    		$layout = $this->getLayout();
            $layout->getUpdate()->load('catalogsearch_result_tatva_ajax');
            $layout->generateXml()->generateBlocks();
            $output = $layout->getOutput();
            return $output;
    	}
}