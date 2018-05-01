<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Alternatelang
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Alternatelang_Block_Adminhtml_Widget_Info_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

	public function render(Varien_Data_Form_Element_Abstract $element) 
	{
		$html = '<div style="background:url(\'http://www.magmodules.eu/_logo.png\') no-repeat scroll 15px center #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 200px;">
					<h4>About Magmodules.eu</h4>
					<p>We are a Magento only E-commerce Agency located in the Netherlands.<br>
					<br />
					
					 <table width="500px" border="0">
						<tr>
							<td width="58%">View more extensions from us:</td>
							<td width="42%"><a href="http://www.magentocommerce.com/magento-connect/developer/Magmodules" target="_blank">Magento Connect</a></td>
						</tr>
						<tr>
							<td height="30">Visit our website:</td>
							<td><a href="http://www.magmodules.eu" target="_blank">www.magmodules.eu</a></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="30"><strong>Read everything about the extension configuration in our <a href="http://www.magmodules.eu/help/alternate-hreflang" target="_blank">Knowledgebase.</a></strong></td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>';
		return $html;
	}

}
