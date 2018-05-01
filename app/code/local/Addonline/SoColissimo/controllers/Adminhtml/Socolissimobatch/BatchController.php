<?php
/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Addonline_SoColissimo
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_SoColissimo_Adminhtml_Socolissimobatch_BatchController extends Mage_Adminhtml_Controller_Action
{
    /* patch JETPULP suite à SUPEE-6285 */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/socolissimo');
    }

    /**
     * Lance l'import en mode interactif
     */
    public function indexAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('core/text');
        
        $log = Mage::getSingleton('socolissimo/liberte_batch')->run("");
        
        $block->setText($log);
        
        $this->_addContent($block);
        $this->renderLayout();
    }
}