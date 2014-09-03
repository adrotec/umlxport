<?php

namespace Adrotec\UmlXport;

use Adrotec\UmlXport\Meta\UmlClass;

interface ExporterInterface {
    
    public function setFormat($format);
    
    public function export(UmlClass $umlClass, $format = null);
    
}
