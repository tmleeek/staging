<?php
class Tatva_Productproblem_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
		$this->renderLayout();
    }

    public function formAction()
    {

        $data = $this->getRequest()->getParams();
        $model = Mage::getModel('productproblem/productproblem');
        $proid = array($data['productid']);
        $model->setData(array(
                'productid'=>$data['productid'],
                'name'=>$data['name'],
                'email'=>$data['email'],
                'bibno'=>$data['bibno'],
                'comment'=>$data['comment'])
                );

        $model->save();
        Mage::getSingleton('core/session')->addSuccess('Your problem has been sent');
        $this->_redirect('*/*/index',array('productid'=>$data['productid']));
     
    }

}
