<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';
class Tatva_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    public function massGeneratePriceMinisterCSVAction()
    {           
     	$productIds = $this->getRequest()->getParam('product');
		
        if (!is_array($productIds)) 
		     $this->_getSession()->addError($this->__('Please select product(s).'));
        else 
		{
            if (!empty($productIds)) 
			{   //echo $_SERVER["DOCUMENT_ROOT"];exit;
				$file_hightech = $_SERVER["DOCUMENT_ROOT"]."/var/export/priceminister_product_Hightech.csv";
				//$file_hightech = "var/export/priceminister_product_Hightech.csv";
				$hp = fopen($file_hightech , 'w+');
				$headings = "EAN;Manufactor reference;Manufactor;Your reference;Title;Kind of product;Description;URL Main Image;URLs Images Secondary;Price;Quantity;Quality;Comments;private comments;Promotion code"."\n";
				fwrite($hp , $headings);
				
				  
				$file_flag = $_SERVER["DOCUMENT_ROOT"]."/var/export/priceminister_product_Flag.csv"; 
				//$file_flag = "var/export/priceminister_product_Flag.csv";
				$fp = fopen($file_flag , 'w+');
				$headings = '"Product reference";"Listing reference";"Price";"Public price";"Quality";"quantity";"Listing comments";"private comments";"Kind of product";"Titre";"Description";"Manufacturer";"color (only for decoration)";"Main material";"Weight zone (in gramm)";"Style";"location";"Shippment";"Phone";"postal code";"Country";"Main image";"other images";"Promotion code"'."\n";                                                                                                                        
			    fwrite($fp , $headings);
				
				$hightech_arr = array();
				$flag_arr = array();
				$result = array();
                try 
				{   
					foreach ($productIds as $productId) 
					{   
						$hightech_arr = '';
						$flag_arr = '';
						$result = '';
						$product = Mage::getModel('catalog/product')->load($productId);
						$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
						$image = $product->getMediaGalleryImages();
						$cat_type = $product->getCategoryType();
						
						if($cat_type == 355)
						{    
							// for Hightech category
							$hightech_arr['ean'] = $product->getEan13();
							$hightech_arr['manufactor_reference'] = '';	
							$hightech_arr['manufactor'] = $product->getManufacturer();
							$hightech_arr['your_reference'] = $product->getSku(); 
							$hightech_arr['title'] = $product->getName();
							$hightech_arr['kind_of_product'] = "Accessoire mobile > Coque";
							
							if($product->getDescripption()) 
								$hightech_arr['description'] = $product->getDescripption();
							else
								$hightech_arr['description'] = $product->getDescription();	
										    	   
							foreach($image as $_image)
							{   
								$path = $_image->getUrl();
								$hightech_arr['url_main_image'] = $path;
							}
							$hightech_arr['urls_images_secondary'] = '';
							if($product->getprrice())
								$hightech_arr['price'] = $product->getprrice();
							else
								$hightech_arr['price'] = $product->getPrice();
								 
							$hightech_arr['qty'] = $stock->getQty(); 
						    $hightech_arr['quality'] = "N";
							$hightech_arr['comments'] = $product->getComments(); 
					   		$hightech_arr['private_comments'] = $product->getPrivateComments();
							$hightech_arr['promotion_code'] = '';
							
							$result = $hightech_arr['ean'].';'.$hightech_arr['manufactor_reference'].';'.$hightech_arr['manufactor'].';'.$hightech_arr['your_reference'].';'.$hightech_arr['title'].';'.$hightech_arr['kind_of_product'].';"'.$hightech_arr['description'].'";'.$hightech_arr['url_main_image'].';'.$hightech_arr['urls_images_secondary'].';'.$hightech_arr['price'].';'.$hightech_arr['qty'].';'.$hightech_arr['quality'].';'.$hightech_arr['comments'].';'.$hightech_arr['private_comments'].';'."\n";                  
						
fwrite($hp , $result);
							
						}
						else if ($cat_type == 356)
						{       
						 	// for Flag category	
							$flag_arr['product_ref'] = '"'.$product->getSku().'"'; 
							$flag_arr['listing_ref'] = '"'.$product->getSku().'"'; 
							if($product->getprrice())
								$flag_arr['price'] = number_format($product->getprrice(),2);
							else
								$flag_arr['price'] = number_format($product->getPrice(),2);
							$flag_arr['public_price'] = '';
							$flag_arr['quality'] = '"N"'; 
							$flag_arr['qty'] = number_format($stock->getQty(),0); 
						    $flag_arr['comments'] = '"'.$product->getComments().'"'; 
					   		$flag_arr['private_comments'] = '"'.$product->getPrivateComments().'"';
							$flag_arr['kind_of_product'] = '"drapeau"';
							$flag_arr['titre'] = '"'.$product->getName().'"';
							if($product->getDescripption()) 
								$flag_arr['description'] = '"'.$product->getDescripption().'"';
							else
								$flag_arr['description'] = '"'.$product->getDescription().'"';
							$flag_arr['manufecturer'] = $product->getManufacturer();	
							$flag_arr['color'] = $product->getColor();
							$flag_arr['main_material'] = $product->getMarque();
							$flag_arr['weight_zone'] = number_format($product->getWeight(),0);
							$flag_arr['style'] = '';
							$flag_arr['location'] = '';
							$flag_arr['shipment'] = '"EXP"';
							$flag_arr['phone'] = '';
							$flag_arr['postal_code'] = '';
							$flag_arr['country'] = '';
							foreach($image as $_image)
							{   
								$path = $_image->getUrl();
								$flag_arr['main_image'] = '"'.$path.'"';
							}
							$flag_arr['other_images'] = '';
							$flag_arr['promotion_code'] = '';	
								
						   	$result = $flag_arr['product_ref'].';'.$flag_arr['listing_ref'].';'.$flag_arr['price'].';'.$flag_arr['public_price'].';'.$flag_arr['quality'].';'.$flag_arr['qty'].';'.$flag_arr['comments'].';'.$flag_arr['private_comments'].';'.$flag_arr['kind_of_product'].';'.$flag_arr['titre'].';'.$flag_arr['description'].';'.$flag_arr['manufecturer'].';'.$flag_arr['color'].';'.$flag_arr['main_material'].';'.$flag_arr['weight_zone'].';'.$flag_arr['style'].';'.$flag_arr['location'].';'.$flag_arr['shipment'].';'.$flag_arr['phone'].';'.$flag_arr['postal_code'].';'.$flag_arr['country'].';'.$flag_arr['main_image'].';'.$flag_arr['other_images'].';'.$flag_arr['promotion_code']."\n";      
							fwrite($fp , $result);			
						}
						else
						{
							$this->_getSession()->addSuccess(
		                        $this->__('Please select category type')
		                    );	
						} 
						
				    } 
					
					if(!empty($hightech_arr)) 
						{
						   	$this->PM_import('ws.priceminister.com','MD-COQUES','mdc987541',$file_hightech,'17696641','');	
						}
						if(!empty($flag_arr))
						{
							$this->PM_import('ws.priceminister.com','az-flag','flag987541',$file_flag,'17696642','');
						}  
					$this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been added to csv.', count($productIds))
                    );
				}
				catch (Exception $e) 
				{
					$this->_getSession()->addError($e->getMessage());
				}
            }  
		}      
		$this->_redirect('*/*/index'); 
	}
	public function PM_import(
		$environnement="ws.priceminister.com",
		$login,
		$token,
		$file,
		$profileid=0,
		$mappingalias=""
	) { 
           
		$url = "https://".$environnement."/stock_ws?action=import";
		$url.= "&login=".$login;
		$url.= "&pwd=".$token;
		$url.= "&version=2010-09-20";

		if($profileid > 0)	
			$url.= "&profileid=".$profileid;
		else
			$url.= "&mappingalias=".$mappingalias;

		$post = array('file' => '@'.($file));
                 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$response = curl_exec($ch);
		curl_close($ch);
		echo '<pre>';print_r($response);exit;
	    return $response;

	}
}
