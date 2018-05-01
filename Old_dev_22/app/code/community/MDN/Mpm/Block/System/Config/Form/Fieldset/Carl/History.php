<?php


class MDN_Mpm_Block_System_Config_Form_Fieldset_Carl_History
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        $max = 10;

        try
        {
            $history = Mage::helper('Mpm/Carl')->getTransactionHistory();
            $html .= '<table class="grid np" cellspacing="0" border="0" cellpading="3" width="100%">';
            $isFirst = true;
            $i = 0;
            foreach($history as $item)
            {
                if ($isFirst)
                {
                    $html .= '<thead><tr class="headings">';
                    foreach($item as $k => $v)
                    {
                        $html .= '<th class="a-center">'.$k."</th>";
                    }
                    $html .= '</tr></thead><tbody>';
                    $isFirst = false;
                }

                $class = ($i % 2) ? "even" : "";
                $html .= '<tr class="'.$class.'">';
                foreach($item as $k => $v)
                {
                    if (in_array($k, array('created_at', 'updated_at')))
                        $v = date('Y-m-d H:i:s', $v);
                    $html .= "<td>".$v."</td>";
                }
                $html .= '</tr>';

                $i++;
                if ($i > $max)
                    break;
            }
            $html .= '</tbody></table><p>&nbsp;</p>';

            $cronCodes = array('bmsperformance_catalog_export', 'import_pricing');
            $history = Mage::getModel('cron/schedule')->getCollection()->addFieldToFilter('job_code', array('in' => $cronCodes))->setOrder('schedule_id', 'desc');
            $html .= '<table class="grid np" cellspacing="0" border="0" cellpading="3" width="100%">';
            $isFirst = true;
            $i = 0;
            foreach($history as $item)
            {
                if ($isFirst)
                {
                    $html .= '<thead><tr class="headings">';
                    $html .= '<th>'.$this->__('Scheduled Date').'</th>';
                    $html .= '<th>'.$this->__('Executed at').'</th>';
                    $html .= '<th>'.$this->__('Job').'</th>';
                    $html .= '<th>'.$this->__('Status').'</th>';
                    $html .= '<th>'.$this->__('Messages').'</th>';
                    $html .= '</tr></thead><tbody>';
                    $isFirst = false;
                }

                $class = ($i % 2) ? "even" : "";
                $html .= '<tr class="'.$class.'">';
                $html .= "<td>".$item->getscheduled_at()."</td>";
                $html .= "<td>".$item->getexecuted_at()."</td>";
                $html .= "<td>".$item->getjob_code()."</td>";
                $html .= "<td>".$item->getstatus()."</td>";
                $html .= "<td>".$item->getmessages()."</td>";
                $html .= '</tr>';

                $i++;
                if ($i > $max)
                    break;
            }
            $html .= '</tbody></table>';

        }
        catch(Exception $ex)
        {
            $html .= $this->__($ex->getMessage());
        }

        $html .= $this->_getFooterHtml($element);
        return $html;
    }

}
