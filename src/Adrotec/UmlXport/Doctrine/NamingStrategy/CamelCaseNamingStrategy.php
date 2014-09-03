<?php

namespace Adrotec\UmlXport\Doctrine\NamingStrategy;

class CamelCaseNamingStrategy extends SnakeCaseNamingStrategy {
    
    public function getColumnName($fieldName) {
        $fieldName = strtolower(parent::getColumnName($fieldName));
        $fieldName = str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)));
        return $fieldName;
    }
    
    public function getTableName($className) {
        return $this->getColumnName($className);
    }
    
}