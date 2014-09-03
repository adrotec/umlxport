<?php

namespace Adrotec\UmlXport\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;
use DoctrineUMLImporter\Util\Text;


use Adrotec\UmlXport\Meta\UmlClass;

class MappingConverter {
    
    private $mapped = array();


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
        $dataTypes = array(
            "integer", "smallint", "bigint",
            "boolean", "decimal", "date", "time", "datetime", "float",
            "text"
        );
        return in_array($this->getDoctrineDataType($typeName), $dataTypes);
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
        $fieldName = preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $fieldName);
        return trim(strtolower(strtr($fieldName, array(' ' => '_'))), '_');
    }
    
    public function getTableName($className) {
        return $this->getColumnName(Text::pluralize($className));
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
                  'targetDocument' => $this->getAssociationNamespaced($umlAssociationEnd->getTarget()->getName()),
                  'fieldName' => $umlAssociationEnd->getName(),
                  'scalar' => $umlAssociationEnd->isScalar(),
              );
              if (!$association['scalar']) {
                  $association['mappedBy'] = $umlAssociationEnd->getOtherEnd()->getName();
              }
              if ($association['scalar']) {
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
    $class->setCollection(array('name' => $this->getTableName($entityClassShortName)));
    $class->mapField(array('fieldName' => 'id', 'type' => 'integer', 'id' => true));
    $class->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);
    foreach ($fields as $field) {
      $class->mapField($field);
    }

    foreach ($associations as $association) {
      if ($association['scalar']) {
//          $class->mapManyToOne($association);
          $class->mapOneReference($association);
      } else {
          $class->mapManyReference($association);
      }
    }
    
    return $class;
  }
}