<?php

namespace Adrotec\UmlXport\Meta;

class UmlBase implements UmlInterface {
    
    private $id;
    private $name;

    public function __construct($id, $name = null) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
    
}