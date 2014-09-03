<?php

namespace Adrotec\UmlXport\Doctrine\ORM;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Doctrine\ORM\Tools\EntityGenerator;

use Adrotec\UmlXport\Meta\UmlClass;

use Adrotec\UmlXport\Doctrine\ORM\MappingConverter;

class UmlExporter {
    
    private $exported = array();
    
    private $mappingConverter;
    
    private $format = 'xml';
    
    public function __construct(MappingConverter $mappingConverter) {
        $this->mappingConverter = $mappingConverter;
    }
    
    public function setFormat($format){
        $this->format = $format;
    }
    
    public function getFormat(){
        return $this->format;
    }

    public function export(UmlClass $umlClass, $format = null){
        
        if($format === null){
            $format = $this->format;
        }
        
        $entityClass = $umlClass->getFullyQualifiedName();

        if (isset($this->exported[$entityClass])) {
            return null;
        }

        $this->exported[$entityClass] = array();
            
        $classMetaInfo = $this->mappingConverter->convert($umlClass);
        
        if(!$classMetaInfo){
            return null;
        }
        
        $cme = new ClassMetadataExporter();
        $exporter = $cme->getExporter('yml' == $format ? 'yaml' : $format);
        if($format == 'annotation'){
            $entityGenerator = new EntityGenerator();
            $exporter->setEntityGenerator($entityGenerator);
            $entityGenerator->setNumSpaces(4);
            $extend = false;
            if ($extend) {
                $entityGenerator->setClassToExtend($extend);
            }
        }
        $mappingCode = $exporter->exportClassMetadata($classMetaInfo);
        return $mappingCode;
    }
}