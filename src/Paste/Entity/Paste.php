<?php

namespace Paste\Entity;

class Paste
{
    protected $id;

    protected $contents;

    protected $timestamp;

    protected $token;

    protected $filename;

    protected $ip;

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

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function setBinaryIp($ip)
    {
        if ($this->ip === null) { return ;}

        $this->ip = inet_ntop($ip);
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

    public function getIp()
    {
        return $this->ip;
    }

    public function getBinaryIp()
    {
        if ($this->ip === null) { return ;}
        
        return inet_pton($this->ip);
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
