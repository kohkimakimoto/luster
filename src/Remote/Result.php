<?php

namespace Kohkimakimoto\Luster\Remote;

class Result
{
    protected $returnCode;
    protected $contents;
    protected $errorContents;

    public function __construct($returnCode, $contents, $errorContents)
    {
        $this->returnCode = $returnCode;
        $this->contents = $contents;
        $this->errorContents = $errorContents;
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

    public function getErrorContents()
    {
        return $this->contents;
    }

    public function errorContents()
    {
        return $this->getErrorContents();
    }

    public function __toString()
    {
        return $this->contents;
    }
}
