<?php

namespace Adrotec\UmlXport\Meta;

class UmlAssociation extends UmlBase {

    private $associationEnds;

    public function __construct($id, $name, array $associationEnds) {
        parent::__construct($id, $name);
        $this->associationEnds = $associationEnds;
    }

    public function getAssociationEnds() {
        return $this->associationEnds;
    }

}