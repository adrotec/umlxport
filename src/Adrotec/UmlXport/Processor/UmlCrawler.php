<?php

namespace Adrotec\UmlXport\Processor;

use Symfony\Component\DomCrawler\Crawler;

class UmlCrawler extends Crawler {
  
    protected static function prepareUml($uml) {
  //    return $uml;
      // $uml = preg_replace('/\<\/UML(.+?)\.(.+?)>/', '</UML$1_$2>', $uml);
      // $uml = preg_replace('/\<UML(.+?)\.(.+?)>/', '<UML$1_$2>', $uml);
      $uml = preg_replace('/\<\/UML:([a-zA-Z0-9]+?)\.([a-zA-Z0-9]+)/', '</UML:$1_$2', $uml);
      $uml = preg_replace('/\<UML:([a-zA-Z0-9]+?)\.([a-zA-Z0-9]+)/', '<UML:$1_$2', $uml);
      $uml = str_replace('xmi.id', 'xmi_id', $uml);
      $uml = preg_replace('/(.+?)="(.+?)\.(.+?)"/', '$1="$2_$3"', $uml);
      // exit($uml);
      return $uml;
    }
    
    protected static function prepareSelector($selector){
        $selector = strtr($selector, '.', '_');
        return $selector;
    }

    public static function createUmlCrawler($uml){
      $uml = self::prepareUml($uml);
      $crawler = new self($uml, null, true);
      return $crawler;
    }

    public function __construct($node = null, $uri = null, $uml = false) {
        $this->uri = $uri;
        if($uml){
          $this->addContent($node, 'text/html');
        }
        else {
          $this->add($node);
        }
    }
  
    public function filter($selector) {
        $selector = self::prepareSelector($selector);
        return parent::filter($selector);
    }

    public function attr($attribute) {
        $attribute = self::prepareSelector($attribute);
        return parent::attr($attribute);
    }

}