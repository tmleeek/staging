<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../../app/Mage.php');
Mage::app();
ini_set ( 'memory_limit', '2048M' );


$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');



      $sql='SELECT DISTINCT entity_id_old FROM tatva_weee_temp';
      $data=$read->fetchAll($sql);

      foreach($data as $ids)
      {
        $new_entity='';
        $lengow_id_get='SELECT DISTINCT entity_id FROM catalog_product_entity_varchar where attribute_id=215 and value='.$ids;
        $new_entity=$read->fetchOne($lengow_id_get);
        if($new_entity!='')
        {
            $sql_data2 = 'update tatva_weee_temp  set entity_id = '.$new_entity.' where  old_entity_id ='.$ids;
            if($sql_data2)
            {
              $write->query($sql_data2);
              $row++;
            }
        }

      }

echo "Total Inserted Item:--".$row.'<br>';
echo 'Done';



?>