<?php

namespace Adrotec\UmlXport\Doctrine;

interface NamingStrategyInterface {
    
    public function getColumnName($fieldName);
    
    public function getTableName($className);

}
