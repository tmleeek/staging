<?php

abstract class TBT_Milestone_Model_Rule_Factory_Abstract extends Varien_Object
{
    protected $_typeNodes = null;

    /**
     * Array of all of the different type model names. that exist for this component.  For example, the Trigger_Factory
     * will have Trigger_Type_Signup, Trigger_Type_Order, etc.
     *
     * @var null
     */
    protected $_typeModelNames = null;

    /**
     * Array of the actual model instances for each of the model types.
     * @var null
     */
    protected $_typeModels = null;

    /**
     * @var array
     */
    protected $_typeNames = null;

    protected abstract function _getTypeNode();

    protected abstract function _isTypeModelValid($model);

    /**
     * Constructs a new condition model based on the type specified
     * @param string $type
     * @return ST_Core_Model_Rule_Component
     */
    public function create($type, $args = array())
    {
        $typeModelNames = $this->getTypeModelNames();

        // Return null if this type model does not exist.
        if (!isset($typeModelNames[$type])) {
            throw new Exception("Class does not exist");
        }

        $typeModelName = $typeModelNames[$type];
        $typeModel = Mage::getModel($typeModelName, $args);

        return $typeModel;
    }

    /**
     * Retrieves all type model names.
     * @return array
     */
    public function getTypeNames()
    {
        if ($this->_typeNames == null) {
            $this->_loadTypes();
        }

        return $this->_typeNames;
    }

    /**
     * Loads the type models from the config and validates them.
     * This will not load multiple times.
     *
     * @throws Exception if a bad type model is specified.
     */
    public function getTypeModelNames()
    {
        if ($this->_typeModelNames == null) {
            $this->_loadTypes();
        }

        return $this->_typeModelNames;
    }

    /**
     * Loads the type models from the config and validates them.
     * This will not load multiple times.
     *
     * @throws Exception if a bad type model is specified.
     */
    public function getTypeModels()
    {
        if ($this->_typeModels == null) {
            $this->_loadTypes();
        }

        return $this->_typeModels;
    }

    public function getOptions()
    {
        if ($this->_typeNodes == null) {
            $this->_loadTypes();
        }

        $options = array();
        foreach ($this->_typeNodes as $code => $node) {
            $options[$code] = $node->label;
        }

        return $options;
    }

    /**
     * Loop over the config xml for <types> for the given type, and instantiate models, storing an array of
     * the instantiated models as well as model names.
     *
     * @return ST_Core_Model_Rule_Attribute_Factory
     */
    protected function _loadTypes()
    {
        // First load all the referrence models...
        $componentTypes = $this->_getTypeNode();

        $this->_typeNodes = array();
        $this->_typeModelNames = array();
        $this->_typeModels = array();

        // No condition types, exit out.
        if (empty($componentTypes)) {
            return $this;
        }

        $typeNodes = $componentTypes->children();
        foreach ($typeNodes as $code => $node) {
            $this->_loadTypeForNode($code, $node);
        }

        return $this;
    }

    /**
     * Load up type models for a given XML node.  This supports children nodes.  For example, see how the facebook like
     * type is specified in config xml.
     *
     * @param $code
     * @param $node
     * @return ST_Core_Model_Rule_Attribute_Factory
     * @throws Exception
     */
    protected function _loadTypeForNode($code, $node)
    {
        if(!$this->_isNodeValid($node, $code)) {
            return $this;
        }

        // We know by this point that this node is a node with type attributes in it.
        $class = (string) $node->class;
        $name  = (string) $node->name;

        $model = $this->_getModel($class);

        if(empty($model)) {
            throw new Exception ("Type model instance not loaded for class '{$class}' with code '{$code}'.");
        }

        if (!$this->_isTypeModelValid($model)) {
            throw new Exception ( "'{$name}' type model '" . (string)$node->class . "' with code '{$code}' does not extend the correct type model interface." );
        }

        $this->_typeNodes[$code]  = $node;
        $this->_typeModels[$code] = $model;
        $this->_typeNames[$code]  = $name;
        if($node->class) {
            $this->_typeModelNames[$code] = (string)$node->class;
        }

        return $this;
    }

    /**
     * @param object $node
     * @param string $class
     * @return true if the node is allowed to be parsed
     */
    protected function _isNodeValid($node, $class)
    {
        if((string)$node->class == "") {
            return false;
        }
        return true;
    }

    /**
     * Creates a blank model instance of the class provided.
     * @param unknown_type $class
     */
    protected function _getModel($class)
    {
        return Mage::getModel($class);
    }

    /**
     * This looks like it can be extended to special case logic in the future as needed.
     *
     * @param $model
     * @param $nodeAttributes
     * @return ST_Core_Model_Rule_Attribute_Factory
     */
    protected function _afterLoadTypeModel($model, $nodeComponents)
    {
        return $this;
    }

    /**
     *
     * @return array()
     */
    public function getAvailableTypes()
    {
        $types = array();
        foreach($this->getTypeModels() as $typeName => $typeModel) {
            $type = $typeModel->getData();
            $type['type'] = $typeName;
            $types[] = $type;
        }
        return $types;
    }
}
