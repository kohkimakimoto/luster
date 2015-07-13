<?php

namespace Kohkimakimoto\Luster\Remote;

class Result
{
    protected $returnCode;
    protected $contents;

    public function __construct($returnCode, $contents)
    {
        $this->returnCode = $returnCode;
        $this->contents = $contents;
    }

    public function isFailed()
    {
        return !$this->isSuccessful();
    }

    public function isSuccessful()
    {
        if ($this->returnCode === 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function contents()
    {
        return $this->getContents();
    }

    public function __toString()
    {
        return $this->contents;
    }
}
