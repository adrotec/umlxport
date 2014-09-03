<?php

namespace Adrotec\UmlXport\Doctrine\ORM;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
//use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
//use Doctrine\ORM\Tools\EntityGenerator;
//use DoctrineUMLImporter\Util\Text;

use Adrotec\UmlXport\Meta\UmlAssociation;
use Adrotec\UmlXport\Meta\UmlAssociationEnd;

use Adrotec\UmlXport\Meta\UmlClass;

use Adrotec\UmlXport\Doctrine\NamingStrategyInterface;

class MappingConverter {
    
    private $mapped = array();
    
    /**
     *
     * @var NamingStrategyInterface
     */
    private $dbNamingStrategy;
    
    public function setDbNamingStrategy(NamingStrategyInterface $namingStrategy){
        $this->dbNamingStrategy = $namingStrategy;
    }

    protected function getCommonClasses() {
    return array();
    $classMap = array_merge(//
                \Adro\Common\CoreBundle\AdroCommonCoreBundle::getAllEntityClasses(), //
                \Adro\Common\ContactBundle\AdroCommonContactBundle::getAllEntityClasses(), //
                \Adro\Common\FSBundle\AdroCommonFSBundle::getAllEntityClasses(), //
//                \Adro\Common\PaymentBundle\AdroCommonPaymentBundle::getAllEntityClasses(),
                \Adro\Common\ProfileBundle\AdroCommonProfileBundle::getAllEntityClasses(), //
                \Adro\Common\UserBundle\AdroCommonUserBundle::getAllEntityClasses(), //
                array());

        return $classMap;
    }
    
    protected function isCommonClass($class){
        $classMap = $this->getCommonClasses();
        if(isset($classMap[$class])){
            return $classMap[$class];
        }
        return false;
    }


    protected function getAssociationNamespaced($class) {
        $classMap = $this->getCommonClasses();
        if (isset($classMap[$class])) {
            return $classMap[$class];
        }
        return $class;
    }
    
    protected function isDoctrineDataType($typeName) {
        try {
            return \Doctrine\DBAL\Types\Type::getType($typeName);
        }
        catch(\Doctrine\DBAL\DBALException $e){
            return false;
        }
//        $dataTypes = array(
//            "string",
//            "integer", "smallint", "bigint",
//            "boolean", "decimal", "date", "time", "datetime", "float",
//            "text"
//        );
//        return in_array($this->getDoctrineDataType($typeName), $dataTypes);
    }

    protected function getDoctrineDataType($type) {
        $type = strtolower($type);
        $map = array(
            'int' => 'integer',
            'double' => 'float',
            'bool' => 'boolean'
        );
        if (isset($map[$type])) {
            return $map[$type];
        }
        return $type;
    }
    
    public function getColumnName($fieldName) {
        if(!$this->dbNamingStrategy){
            $this->dbNamingStrategy = new \Adrotec\UmlXport\Doctrine\NamingStrategy\SameNamingStrategy();
        }
        return $this->dbNamingStrategy->getColumnName($fieldName);
    }
    
    public function getTableName($className) {
        if(!$this->dbNamingStrategy){
            $this->dbNamingStrategy = new \Adrotec\UmlXport\Doctrine\NamingStrategy\SameNamingStrategy();
        }
        return $this->dbNamingStrategy->getTableName($className);
    }
    
  public function convert(UmlClass $umlClass){
      
    $entityClass = $umlClass->getFullyQualifiedName();

    $entityClassShortName = $umlClass->getName();

    if($this->isCommonClass($entityClassShortName)){
        return;
    }

    if ($this->isDoctrineDataType($entityClassShortName)) {
        return;
    }
    
    $fields = array();

    if(!isset($this->mapped[$entityClass])){
        $this->mapped[$entityClass] = array();
    }
    //        array('fieldName' => $name, 'type' => $type, 'length' => $length);
    foreach ($umlClass->getAttributes() as $umlAttribute) {
      /* @var $umlAttribute UmlAttribute */
      $field = array(
          'fieldName' => $umlAttribute->getName(),
          'type' => $umlAttribute->getType() ? $this->getDoctrineDataType($umlAttribute->getType()->getName()) : null,
          'columnName' => $this->getColumnName($umlAttribute->getName())
      );
      $id = $umlAttribute->getName();
      if (!isset($this->mapped[$entityClass][$id])) {
          $fields[] = $field;
      }
      $this->mapped[$entityClass][$id] = $umlAttribute;
    }

    $associations = array();

    foreach ($umlClass->getAssociations() as $umlAssociation) {
      /* @var $umlAssociation UmlAssociation */
      /* @var $umlAssociationEnd UmlAssociationEnd */
      foreach ($umlAssociation->getAssociationEnds() as $umlAssociationEnd) {
    //                    echo $umlAssociationEnd->getName().':'.$umlAssociationEnd->getTarget()->getName().':'.$umlAssociationEnd->getOwner()->getName()."<br><br>";
          if ($umlAssociationEnd->getOwner() == $umlClass && $umlAssociationEnd->isNavigable()) {
              $association = array(
                  'targetEntity' => $this->getAssociationNamespaced($umlAssociationEnd->getTarget()->getName()),
                  'fieldName' => $umlAssociationEnd->getName(),
                  'scalar' => $umlAssociationEnd->isScalar(),
              );
              $umlAssociationOtherEnd = $umlAssociationEnd->getOtherEnd();
              if (!$association['scalar'] && $umlAssociationOtherEnd) {
                  $association['mappedBy'] = $umlAssociationOtherEnd->getName();
              }
              if ($association['scalar']) {
                  if($umlAssociationOtherEnd->isNavigable()){
                    $association['inversedBy'] = $umlAssociationOtherEnd->getName();
                  }
                  $association['joinColumns'] = array(array(
                          'name' => $this->getColumnName($umlAssociationEnd->getName() . 'Id'),
                          'referencedColumnName' => 'id', 
                  ));
              }
              $id = $umlAssociationEnd->getName();
              if (!isset($this->mapped[$entityClass][$id])) {
                  $associations[] = $association;
              }
              $this->mapped[$entityClass][$id] = $umlAssociationEnd;
          }
      }
    }

    $class = new ClassMetadataInfo($entityClass);
    $class->setPrimaryTable(array('name' => $this->getTableName($entityClassShortName)));
    $class->mapField(array('fieldName' => 'id', 'type' => 'integer', 'id' => true));
    $class->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);
    foreach ($fields as $field) {
      $class->mapField($field);
    }

    foreach ($associations as $association) {
      if ($association['scalar']) {
          $class->mapManyToOne($association);
      } else {
          $class->mapOneToMany($association);
      }
    }
    
    return $class;
  }
}