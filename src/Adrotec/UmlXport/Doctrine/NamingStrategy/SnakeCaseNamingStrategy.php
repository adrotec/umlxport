<?php

namespace Adrotec\UmlXport\Doctrine\NamingStrategy;

use Adrotec\UmlXport\Doctrine\NamingStrategyInterface;

use Adrotec\UmlXport\Util;

class SnakeCaseNamingStrategy implements NamingStrategyInterface {
    
    public function getColumnName($fieldName) {
        $fieldName = preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $fieldName);
        return trim(strtolower(strtr($fieldName, array(' ' => '_'))), '_');
    }

    public function getTableName($className) {
        return $this->getColumnName(Util::pluralize($className));
    }

}
