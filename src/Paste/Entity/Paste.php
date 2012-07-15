<?php

namespace Paste\Entity;

class Paste
{
    protected $id;

    protected $contents;

    protected $timestamp;

    protected $token;

    protected $filename;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setContents($contents)
    {
        $this->contents = $this->normalizeContent(
            $this->trimContent($contents)
        );
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    protected function normalizeContent($content)
    {
        return preg_replace(array('#\r?\n#', '#\r#'), "\n", $content);
    }

    protected function trimContent($content)
    {
        return trim($content);
    }
}
