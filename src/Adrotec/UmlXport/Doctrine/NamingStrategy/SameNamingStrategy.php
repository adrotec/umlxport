<?php

namespace Adrotec\UmlXport\Doctrine\NamingStrategy;

use Adrotec\UmlXport\Doctrine\NamingStrategyInterface;

class SameNamingStrategy implements NamingStrategyInterface {
    
    public function getColumnName($fieldName) {
        return $fieldName;
    }

    public function getTableName($className) {
        return $className;
    }

}
