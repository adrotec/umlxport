<?php

namespace Adrotec\UmlXport\Meta;

class UmlAttribute extends UmlBase {

    private $type;

    public function __construct($id, $name, UmlType $type = null) {
        parent::__construct($id, $name);
        $this->type = $type;
    }

    /**
     * 
     * @return UmlType
     */
    public function getType() {
        return $this->type;
    }
}