<?php

namespace App\Lib\Type\Parse;

require_once "ParseTypeInterface.php";

use App\Lib\Type\Array\CGArray;
use App\Lib\Type\Array\CGPath;
use App\Lib\Type\String\CGString;

class ParseType implements ParseTypeInterface
{
    protected Mixed $mixed;

    public function __construct($Mixed)
    {
        $this->mixed = $Mixed;
    }

    public function toString()
    {
        return $this->mixed;
    }

    public function toCGString(): CGString
    {
        return new CGString($this->mixed);
    }

    public function toArray()
    {
        return $this->mixed;
    }

    public function toCGArray(): CGArray
    {
        return new CGArray($this->mixed);
    }

    public function toPath(): CGPath
    {
        return new CGPath($this->mixed);
    }

    public function toCGPath(): CGPath
    {
        return new CGPath($this->mixed);
    }

    public function toBoolean(): bool
    {
        return (boolean)$this->mixed;
    }

    public function toJSON()
    {
    }

    public function toObject()
    {
    }

    public function toInteger()
    {
    }

    public function toMap()
    {
    }
}
