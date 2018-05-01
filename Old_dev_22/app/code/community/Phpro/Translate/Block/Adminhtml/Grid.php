<?php

class Phpro_Translate_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('translateGrid');
        $this->_controller = 'translate';
        $this->setDefaultSort('translate_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }

    protected function _prepareCollection() {
        $model = Mage::getModel('translate/translate');
        $collection = $model->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();

        $idArray = array();
        foreach ($collection as $row) {
            $idArray[] = $row->getId();
        }
        Mage::getSingleton('adminhtml/session')->setIdArray($idArray);
    }

    protected function _prepareColumns() {

        $this->addColumn('translate_id', array(
            'header' => Mage::helper('translate')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'translate_id',
        ));

        $this->addColumn('string', array(
            'header' => Mage::helper('translate')->__('String'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'string',
            'type' => 'text',
            'truncate' => 50,
            'escape' => true,
            'renderer'  => 'Phpro_Translate_Block_Adminhtml_Renderer_String'
        ));

        $localesSourceModel = Mage::getModel('translate/system_config_source_locales');
        $localesOptions = $localesSourceModel->toArray();

        $this->addColumn('locale', array(
            'header' => Mage::helper('translate')->__('Locale'),
            'align' => 'left',
            'index' => 'locale',
            'type' => 'options',
            'escape' => true,
            'options' => $localesOptions
        ));

        $modulesSourceModel = Mage::getModel('translate/system_config_source_modules');
        $modulesOptions = $modulesSourceModel->toArray();

        $this->addColumn('module', array(
            'header' => Mage::helper('translate')->__('Module'),
            'align' => 'left',
            'index' => 'module',
            'type' => 'options',
            'escape' => true,
            'options' => $modulesOptions
        ));

        $interfaceSourceModel = Mage::getModel('translate/system_config_source_interface');
        $interfaceOptions = $interfaceSourceModel->toArray();

        $this->addColumn('interface', array(
            'header' => Mage::helper('translate')->__('Interface'),
            'align' => 'left',
            'index' => 'interface',
            'type' => 'options',
            'escape' => true,
            'options' => $interfaceOptions
        ));

        $storeSourceModel = Mage::getModel('translate/system_config_source_stores');
        $storeOptions = $storeSourceModel->toArray();
        $this->addColumn('store_id', array(
            'header' => Mage::helper('translate')->__('Store view'),
            'align' => 'left',
            'index' => 'store_id',
            'type' => 'options',
            'escape' => false,
            'options' => $storeOptions
        ));
        
        $this->addColumn('page', array(
            'header' => Mage::helper('translate')->__('Page'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'page',
            'type' => 'text',
            'truncate' => 50,
            'escape' => true,
        ));
        
        $this->addColumn('time', array(
            'header' => Mage::helper('translate')->__('Time'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'time',
            'type' => 'text',
            'truncate' => 50,
            'escape' => true,
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('translate')->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getTranslateId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('translate')->__('Edit'),
                    'url' => array(
                        'base' => '*/*/edit'
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array(
                    'id' => $row->getTranslateId(),
                ));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
