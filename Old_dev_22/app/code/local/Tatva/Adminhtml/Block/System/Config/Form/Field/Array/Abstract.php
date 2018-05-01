<?php

abstract class Tatva_Adminhtml_Block_System_Config_Form_Field_Array_Abstract extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = array();

    /**
     * Enable the "Add after" button or not
     *
     * @var bool
     */
    protected $_addAfter = true;

    /**
     * Label of add button
     *
     * @var unknown_type
     */
    protected $_addButtonLabel;

    private $_arrayRowsCache;

    protected $_select = false;
    protected $_multiselect = false;
    
    /**
     * Check if columns are defined, set template
     *
     */
    public function __construct()
    {
        if (empty($this->_columns)) {
            throw new Exception('At least one column must be defined.');
        }
        if (!$this->_addButtonLabel) {
            $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add');
        }
        parent::__construct();
        if (!$this->getTemplate()) {
            $this->setTemplate('tatva/system/config/form/field/enhanced_array.phtml');
        }
    }

    /**
     * Add a column to array-grid
     *
     * @param string $name
     * @param array $params
     */
    public function addColumn($name, $params)
    {
        $this->_columns[$name] = array(
            'label'     => empty($params['label']) ? 'Column' 	: $params['label'],
            'size'      => empty($params['size']) ? false    	: $params['size'],
            'style'     => empty($params['style']) ? null    	: $params['style'],
            'class'     => empty($params['class']) ? null    	: $params['class'],
        	'type'      => empty($params['type'])  ? 'text'		: $params['type'],
        	'values'	=> empty($params['values']) ? array() 	: $params['values'],
            'renderer'  => false,
        );
        if(!empty($params['type']) && $params['type'] == 'select'){
        	$this->_select = true;
        }
        if(!empty($params['type']) && $params['type'] == 'multiselect'){
        	$this->_multiselect = true;
        }
        if ((!empty($params['renderer'])) && ($params['renderer'] instanceof Mage_Core_Block_Abstract)) {
            $this->_columns[$name]['renderer'] = $params['renderer'];
        }
    }

    /**
     * Get the grid and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $html = $this->_toHtml();
        $this->_arrayRowsCache = null; // doh, the object is used as singleton!
        return $html;
    }

    /**
     * Obtain existing data from form element
     *
     * Each row will be instance of Varien_Object
     *
     * @return array
     */
    public function getArrayRows()
    {
        if (null !== $this->_arrayRowsCache) {
            return $this->_arrayRowsCache;
        }
        $result = array();
        /** @var Varien_Data_Form_Element_Abstract */
        $element = $this->getElement();
        if ($element->getValue() && is_array($element->getValue())) {
            foreach ($element->getValue() as $rowId => $row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->htmlEscape($value);
                }
                $row['_id'] = $rowId;
                $result[$rowId] = new Varien_Object($row);
            }
        }
        $this->_arrayRowsCache = $result;
        return $this->_arrayRowsCache;
    }

    public function getJavascriptHtml($htmlId){
    	$html = '';
    	$element = $this->getElement();
    	if ($element->getValue() && is_array($element->getValue())) {
            foreach ($element->getValue() as $rowId => $row) {
            	$html .= 'if(rowId == "' . $rowId . '"){';
            	foreach ($row as $key => $value) {
            		if($this->_columns[$key]['type'] == 'select'){
            			$html .='for (var i=0; i<document.getElementById("'.$key.$rowId.'").options.length; i++){';
            				$html .='if(document.getElementById("'.$key.$rowId.'").options[i].value == "'.$value.'" ){';
            				$html .='document.getElementById("'.$key.$rowId.'").selectedIndex   = i ;';
            					$html .='break;';
            				$html .='}';
            			$html .='}';
            		}elseif($this->_columns[$key]['type'] == 'multiselect'){
            			foreach ($value as $v) {
            				$html .='for (var i=0; i<document.getElementById("'.$key.$rowId.'").options.length; i++){';
            					$html .='if(document.getElementById("'.$key.$rowId.'").options[i].value == "'.$v.'" ){';
            						$html .='document.getElementById("'.$key.$rowId.'").options[i].selected   = 1 ;';
            						$html .='break;';
            					$html .='}';
            				$html .='}';
            			}
            		}
            	} 
                $html .= '}';

            }
    	}
    	return $html;
    }
    
    public function isJavascript(){    
    	return ($this->_select || $this->_multiselect);
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)
                ->toHtml();
        }
        if ($column['type'] == "text") {
	        return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
	            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
	            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
	            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
        }elseif($column['type'] == "select"){
			$html = '<select name="' . $inputName . '" ' .
				($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
				(isset($column['class']) ? $column['class'] : '') . '"'.
				(isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . ' id="'.$columnName.'#{_id}">';
			foreach($column['values'] as $option){
				$html .= '<option value="'.$option['value'].'">' . str_replace("'","\'",$option['label']) . '</option>';
			}
			
			$html .= '</select>';
			return $html;
        }elseif($column['type'] == "multiselect"){
			$html = '<select multiple name="' . $inputName . '[]" ' .
				($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
				(isset($column['class']) ? $column['class'] : '') . '"'.
				(isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . 
					' size="5" id="'.$columnName.'#{_id}">';
			foreach($column['values'] as $option){
				$html .= '<option value="'.$option['value'].'">' . str_replace("'","\'",$option['label']) . '</option>';
			}
			$html .= '</select>';
			return $html;
        }
    }
    

}