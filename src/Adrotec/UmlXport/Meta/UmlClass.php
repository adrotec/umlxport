<?php

namespace Adrotec\UmlXport\Meta;

class UmlClass extends UmlType {

    private $attributes;
    private $associations;
    private $namespace;

    public function __construct($id, $name, array $attributes = array(), array $associations = array()) {
        parent::__construct($id, $name);
        $this->attributes = $attributes;
        $this->associations = $associations;
    }

    /**
     * 
     * @return array
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * 
     * @return array
     */
    public function getAssociations() {
        return $this->associations;
    }

    public function addAssociation(UmlAssociation $association) {
        $this->associations[] = $association;
    }

    public function setAssociations(array $associations) {
        $this->associations = $associations;
    }
    
    public function getNamespace(){
        return $this->namespace;
    }
    
    public function setNamespace($namespace){
        $namespace = $namespace ? trim($namespace, '\\') : '';
        $this->namespace = $namespace;        
    }
    
    public function getFullyQualifiedName(){
        return $this->getNamespace().'\\'.$this->getName();
    }

}