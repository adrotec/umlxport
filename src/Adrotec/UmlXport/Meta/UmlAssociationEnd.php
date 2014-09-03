<?php

namespace Adrotec\UmlXport\Meta;

use Adrotec\UmlXport\Util;

class UmlAssociationEnd extends UmlBase {

    private $scalar;
    private $target;
    private $navigable;
    private $otherEnd;

    public function __construct($id, $name, UmlClass $target, $scalar, $navigable = null) {
        parent::__construct($id, $name);
        $this->target = $target;
        $this->scalar = $scalar;
        $this->navigable = $navigable;
    }

    public function isScalar() {
        return $this->scalar;
    }

    public function getTarget() {
        return $this->target;
    }

    public function getOwner() {
        return $this->getOtherEnd()->getTarget();
    }

    public function getOtherEnd() {
        return $this->otherEnd;
    }

    public function setOtherEnd(UmlAssociationEnd $otherEnd) {
        return $this->otherEnd = $otherEnd;
    }

    public function getName() {
        $name = parent::getName();
        if (!$name) {
            if ($this->scalar) {
                return lcfirst($this->target->getName());
            } else {
                return lcfirst(Util::pluralize($this->target->getName()));
            }
        }
        return $name;
    }

    public function isNavigable() {
        if ($this->navigable === null) {
            return true;
        }
        return $this->navigable;
    }

    public function getNavigable() {
        return $this->navigable;
    }

    public function setNavigable($navigable) {
        $this->navigable = $navigable;
    }

    public function setScalar($scalar) {
        $this->scalar = $scalar;
    }

}