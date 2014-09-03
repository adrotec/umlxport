<?php

namespace Adrotec\UmlXport\Processor;


use Adrotec\UmlXport\Meta\UmlAssociation;
use Adrotec\UmlXport\Meta\UmlAssociationEnd;
use Adrotec\UmlXport\Meta\UmlAttribute;
use Adrotec\UmlXport\Meta\UmlClass;
use Adrotec\UmlXport\Meta\UmlType;

use Adrotec\UmlXport\Processor\UmlCrawler;

class UmlProcessor {
  
  private $classes = array();
  private $primitives = array();
  private $dataTypes = array();
  private $crawler;

  protected function processPrimitives(){
    $this->primitives = array();
    $this->crawler->filter('Primitive')->each(
      function(UmlCrawler $node, $i) {
        $id = $node->attr('xmi.id');
        $name = $node->attr('name');
        $this->primitives[$id] = new UmlType($id, $name, true);
    });
    return $this->primitives;
  }

  protected function processDataTypes(){
    $this->dataTypes = array();
    $this->crawler->filter('DataType')->each(
      function(UmlCrawler $node, $i) {
        $id = $node->attr('xmi.id');
        $name = $node->attr('name');
        $this->dataTypes[$id] = new UmlType($id, $name, true);
    });
    return $this->dataTypes;
  }

  protected function processAttibutes(UmlCrawler $node){
      $this->primitives = $this->processPrimitives();
      $this->dataTypes = $this->processDataTypes();
      $attributes = array();
      $node->filter('Attribute')->each(
        function(UmlCrawler $node, $i) use (&$attributes) {
          $id = $node->attr('xmi.id');
          $name = $node->attr('name');

          $type = null;

          $typeId = $node->attr('type');

          if (!$typeId) {
              $classifier = $node->filter('StructuralFeature.type Classifier');
              if ($classifier->count()) {
                  $typeId = $classifier->attr('xmi.idref');
              }
          }
          if ($typeId) {
              if (isset($this->primitives[$typeId])) {
                  $type = $this->primitives[$typeId];
              } else if (isset($this->dataTypes[$typeId])) {
                  $type = $this->dataTypes[$typeId];
              } else if (isset($this->classes[$typeId])) {
                  $type = $this->classes[$typeId];
              } else {
                  $typeNode = $this->crawler->filter('Model Class[xmi.id="' . $typeId . '"]');
                  if ($typeNode->count()) {
                      $type = new UmlClass($typeId, $typeNode->attr('name'));
                      $this->classes[$typeId] = $type;
                  } else {
                      $type = new UmlType($typeId);
                  }
              }
          }
          $attributes[$id] = new UmlAttribute($id, $name, $type);
      });
    return $attributes;
  }

  protected function processClasses(){
    $this->classes = array();
    $this->crawler->filter('Model Class')->each(
      function(UmlCrawler $node, $i) {
      /* @var $node Crawler */
      $id = $node->attr('xmi.id');
      $name = $node->attr('name');
      $attributes = $this->processAttibutes($node, $this->classes);
      $this->classes[$id] = new UmlClass($id, $name, $attributes);
    });
    return $this->classes;
  }
  
  protected function processAssociations(){
    $this->crawler->filter('Association')->each(function(UmlCrawler $node, $i) {
      $associationEndsNode = $node->filter('Association.connection AssociationEnd');
      $associationEnds = array();
      /* @var $associationEndNode Crawler */
      for ($i = 0; $i < $associationEndsNode->count(); $i++) {
          $associationEndNode = $associationEndsNode->eq($i);
          $id = $associationEndNode->attr('xmi.id');
          $name = $associationEndNode->attr('name');

          $targetId = $associationEndNode->attr('type');
          if (!$targetId) {
              $cnode = $associationEndNode->filter('AssociationEnd.participant Classifier');
              if (!$cnode->count()) {
                  continue;
              }
              $targetId = $cnode->attr('xmi.idref');
          }
          if (isset($this->classes[$targetId])) {
              $multiplicity = $associationEndNode->filter('Multiplicity Multiplicity.range MultiplicityRange');
              if (
                      $multiplicity->count()) {
                  $multiplicity = array(
                      'lower' => $multiplicity->attr('lower'),
                      'upper' => $multiplicity->attr('upper'),
                  );
              } else {
                  $multiplicity = array(
                      'lower' => null,
                      'upper' => null
                  );
              }
              $navigable = null;
              $scalar = $multiplicity['upper'] == 1;
              if (strtolower('' . $associationEndNode->attr('navigabletype')) == 'navigable') {
                  $navigable = true;
              }
              if ($associationEndNode->attr('isnavigable') == 'true') {
                  $navigable = true;
              } else if ($associationEndNode->attr('isnavigable') == 'false') {
    //                            $navigable = false;
              }
              if ($multiplicity['lower'] === null && $multiplicity['upper'] === null) {
                  if ($navigable === true) {
    //                                $scalar = true;
                  } else {
                      $navigable = false;
                  }
              }
              $target = $this->classes[$targetId];
              $associationEnds[] = new UmlAssociationEnd($id, $name, $target, $scalar, $navigable);
          }
      }
      foreach ($associationEnds as $i => $associationEnd) {
          $targetId = $associationEnd->getTarget()->getId();
          $otherEnd = $associationEnds[$i == 1 ? 0 : 1];
          if (!$otherEnd->getNavigable() && $associationEnd->getNavigable() === true) {
              $otherEnd->setNavigable(false);
              $associationEnd->setScalar(true);
          }
          $associationEnd->setOtherEnd($otherEnd);
          $id = $node->attr('xmi.id');
          $name = $node->attr('name');
          $association = new UmlAssociation($id, $name, $associationEnds);
          $this->classes[$targetId]->addAssociation($association);
      }
    //
    });
  }

  public function process($uml){

//        $uml = $this->prepareUml($uml);
//        CssSelector::disableHtmlExtension();
//      $this->crawler = new UmlCrawler($uml);
        $this->crawler = UmlCrawler::createUmlCrawler($uml);

//        echo '<pre>';
//        print_r($_SERVER);
//        print_r($_POST);
//        print_r($_FILES);
//        echo '<pre>';
//        print_r();
//        exit;

//        echo '<textarea style="height:100%; width: 100%;">';

//    return array('hello', 'world');
        $this->classes = $this->processClasses();

        $this->processAssociations();
    
        return $this->classes;
    }

}
