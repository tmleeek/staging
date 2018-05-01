<?php

class MDN_Mpm_Adminhtml_Mpm_RulesController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        if (!Mage::helper('Mpm/Carl')->checkCredentials()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('Please configure Carl credentials first'));
            $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
        } else {
            Mage::helper('Mpm/Product')->pricingInProgress();
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    public function EditAction()
    {
        if($this->getRequest()->getParam('id')) {
            $rule = Mage::helper('Mpm/Carl')->getRule($this->getRequest()->getParam('id'));
        } else {
            $rule = Mage::helper('Mpm/Carl')->getRuleByType($this->getRequest()->getParam('type'));
        }

        if($this->getRequest()->getParam('add_perimeter_row')) {
            Mage::register('new_perimeter_field', $this->getRequest()->getParam('add_perimeter_row'));
        }
        if($this->getRequest()->getParam('add_condition_row')) {
            Mage::register('new_condition_field', $this->getRequest()->getParam('add_condition_row'));
        }
        Mage::register('current_rule', $rule);

        Mage::helper('Mpm/Product')->pricingInProgress();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        $allData = $this->getRequest()->getPost();
        unset($allData['form_key']);

        foreach($allData as &$data) {
            if(is_array($data) && isset($data['from']) && isset($data['to'])) {
                $data = $data['from'].'..'.$data['to'];
            }
        }

        if (isset($allData['perimeter']))
        {
            foreach($allData['perimeter'] as &$data) {
                if(is_array($data) && isset($data['from']) && isset($data['to'])) {
                    $data = $data['from'].'..'.$data['to'];
                }
            }
        }

        if (isset($allData['condition']))
        {
            foreach($allData['condition'] as &$data) {
                if(is_array($data) && isset($data['from']) && isset($data['to'])) {
                    $data = $data['from'].'..'.$data['to'];
                }
            }
        }

        if(isset($allData['condition'])) {
            if(isset($allData['condition']['offers.bbw_by_price']) && !empty($allData['condition']['offers.bbw_by_price']) && preg_match('#:#', $allData['condition']['offers.bbw_by_price'])){
                list($channel, $sellerReference) = explode(':', $allData['condition']['offers.bbw_by_price']);
                $allData['condition']['offers.bbw_by_price'] = $sellerReference;
            }
            if(isset($allData['condition']['offers.bbw']) && !empty($allData['condition']['offers.bbw']) && preg_match('#:#', $allData['condition']['offers.bbw'])){
                list($channel, $sellerReference) = explode(':', $allData['condition']['offers.bbw']);
                $allData['condition']['offers.bbw'] = $sellerReference;
            }
        }

        if(isset($allData['ignore_sellers'])) {
            $sellersToIgnore = array();
            $sellers = explode(',', $allData['ignore_sellers']);
            foreach($sellers as $seller) {
                if(empty($seller)) {
                    continue;
                }
                
                list($channel, $seller) = explode(':', $seller);
                $sellersToIgnore[$channel][] = $seller;
            }
            $allData['ignore_sellers'] = json_encode($sellersToIgnore);
        }

        $allData['enable'] = $allData['enable'] === '1' ? 'on' : 'off';
        $allData['allow_free_shipping'] = isset($allData['allow_free_shipping']) && $allData['allow_free_shipping'] === '1' ? 'on' : 'off';
        $allData['ignore_shipped_by_marketplace'] = isset($allData['ignore_shipped_by_marketplace']) && $allData['ignore_shipped_by_marketplace'] === '1' ? 'on' : 'off';

        if(!empty($allData['id'])) {
            $rule = Mage::helper('Mpm/Carl')->updateRule($allData['id'], $allData);
        } else {
            $rule = Mage::helper('Mpm/Carl')->createRule($allData);
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Rule saved'));
        $this->_redirect('*/*/Edit', array('id' => $rule->id));
    }

    public function deleteAction()
    {
        Mage::helper('Mpm/Carl')->deleteRule($this->getRequest()->getParam('id'));

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Rule deleted'));
        $this->_redirect('*/*/index');
    }

    public function productsGridAction()
    {
        if($this->getRequest()->getParam('rule_id')) {
            $rule = Mage::helper('Mpm/Carl')->getRule($this->getRequest()->getParam('rule_id'));
        }

        Mage::register('current_rule', $rule);

        $block = $this->getLayout()->createBlock('Mpm/Rules_Edit_Tab_Products');
        $filters =  str_replace(array('&', "="), ",",  base64_decode($this->getRequest()->getParam('filter')));
        $filters = str_replace("sku", 'product_id', $filters);
        if($this->getRequest()->getParam('sort') !== null){
            $sortBy =  $this->getRequest()->getParam('sort') ."," . $this->getRequest()->getParam('dir');
        }else{
            $sortBy = array();
        }

        $block->setPage($this->getRequest()->getParam('page'));
        $block->setFilters($filters);
        $block->setSort($sortBy);
        $this->getResponse()->setBody($block->toHtml());
    }

    public function indexProductsAction()
    {
        Mage::helper('Mpm/Carl')->indexRule($this->getRequest()->getParam('id'));

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Rule indexes updated'));
        $this->_redirect('*/*/Edit', array('id' => $this->getRequest()->getParam('id')));
    }

    public function IndexAllAction()
    {
        Mage::getModel('Mpm/Observer')->indexAllRules();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('All rules indexes updated'));
        $this->_redirect('*/*/index');
    }

    public function ProductsBlockAction()
    {
        $rule = Mage::helper('Mpm/Carl')->getRule($this->getRequest()->getParam('rule_id'));
        Mage::register('current_rule', $rule);

        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Mpm/Rules_Edit_Tab_Products');
        $this->getResponse()->setBody($block->toHtml());
    }

    protected function _isAllowed()
    {
        return true;
    }

    public function getConditionBlockAction(){

        try{

            $rule = Mage::helper('Mpm/Carl')->getRule($this->getRequest()->getParam('rule_id'));
            Mage::register('current_rule', $rule);
            $block = $this->getLayout()->createBlock('Mpm/Rules_Edit_Form_Renderer_FieldSet_Condition_Item');
            $block->setField($this->getRequest()->getParam('field'));
            $block->setValue($this->getRequest()->getParam('value'));

           $this->getResponse()->setBody($block->toHtml());

        }catch(Exception $e){
            $this->getResponse()->setBody($e->getMessage());
        }

    }

    public function getPerimeterBlockAction(){

        try{

            $rule = Mage::helper('Mpm/Carl')->getRule($this->getRequest()->getParam('rule_id'));
            Mage::register('current_rule', $rule);
            $block = $this->getLayout()->createBlock('Mpm/Rules_Edit_Form_Renderer_FieldSet_Perimeter_Item');
            $block->setField($this->getRequest()->getParam('field'));
            $block->setValue($this->getRequest()->getParam('value'));

            $this->getResponse()->setBody($block->toHtml());

        }catch(Exception $e){
            $this->getResponse()->setBody($e->getMessage());
        }

    }

}