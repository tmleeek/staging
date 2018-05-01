<?php

class Pektsekye_OptionImages_Model_Observer extends Mage_Core_Model_Abstract
{

	public function optionSaveAfter($observer)
	{
		$object = $observer->getEvent()->getObject();
              
		if ($object->getResourceName() == 'catalog/product_option_value'){

			$image = '';		
			$imageInfo = Zend_Json::decode($object->getImage());
			$resource = Mage::getSingleton('core/resource'); 
			$read= $resource->getConnection('core_read');		
			$write= $resource->getConnection('core_write');	
			$imageTable = $resource->getTableName('optionimages/product_option_type_image');	
			
			if (isset($imageInfo[0]['file']))
				$image = $this->_moveImageFromTmp($imageInfo[0]['file']);
				
			if (isset($imageInfo[0]['file']) || !isset($imageInfo[0]['url'])){

				$statement = $read->select()
					->from($imageTable)
					->where('option_type_id = '.$object->getId().' AND store_id = ?', 0);

				if ($read->fetchOne($statement)) {
					if ($object->getStoreId() == '0') {
						$write->update(
							$imageTable,
								array('image' => $image),
								$write->quoteInto('option_type_id='.$object->getId().' AND store_id=?', 0)
						);
					}
				} else {
					$write->insert(
						$imageTable,
							array(
								'option_type_id' => $object->getId(),
								'store_id' => 0,
								'image' => $image
					));
				}

				if ($object->getStoreId() != '0') {
					$statement = $read->select()
						->from($imageTable)
						->where('option_type_id = '.$object->getId().' AND store_id = ?', $object->getStoreId());

					if ($read->fetchOne($statement)) {;
						$write->update(
							$imageTable,
								array('image' => $image),
								$write->quoteInto('option_type_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
					} else {
						$write->insert(
							$imageTable,
								array(
									'option_type_id' => $object->getId(),
									'store_id' => $object->getStoreId(),
									'image' => $image
						));
					}
				}
			}	
			
		}
		
    }

	public function optionDeleteAfter($observer)
	{
		$object = $observer->getEvent()->getObject();	
		$resource = Mage::getSingleton('core/resource'); 
		$write= $resource->getConnection('core_write');	
		$imageTable = $resource->getTableName('optionimages/product_option_type_image');	
		
		if ($object->getResourceName() == 'catalog/product_option_value'){
			$childCondition = $write->quoteInto('option_type_id=?', $object->getId());		
			$write->delete(
				$imageTable,
				$childCondition
			);			
		}

	}
	
    /**
     * Move image from temporary directory to normal
     *
     * @param string $file
     * @return string
     */
    protected function _moveImageFromTmp($file)
    {

        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->_getMadiaConfig()->getMediaPath($file));

        try {
            $ioObject->open(array('path'=>$destDirectory));
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(array('path'=>$destDirectory));
        }

        if (strrpos($file, '.tmp') == strlen($file)-4) {
            $file = substr($file, 0, strlen($file)-4);
        }

        $destFile = dirname($file) . $ioObject->dirsep()
                  . Varien_File_Uploader::getNewFileName($this->_getMadiaConfig()->getMediaPath($file));

        $ioObject->mv(
            $this->_getMadiaConfig()->getTmpMediaPath($file),
            $this->_getMadiaConfig()->getMediaPath($destFile)
        );

        return str_replace($ioObject->dirsep(), '/', $destFile);
    }
	
    /**
     * Retrive media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getMadiaConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }	


}

