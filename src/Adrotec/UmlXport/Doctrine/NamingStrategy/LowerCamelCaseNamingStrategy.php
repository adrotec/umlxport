<?php

namespace Adrotec\UmlXport\Doctrine\NamingStrategy;

class LowerCamelCaseNamingStrategy extends CamelCaseNamingStrategy {
    
    public function getColumnName($fieldName) {
        return lcfirst($fieldName);
    }
    
}