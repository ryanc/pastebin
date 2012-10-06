<?php

namespace Paste\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Paste
{
    protected $id;

    protected $content;

    protected $timestamp;

    protected $token;

    protected $filename;

    protected $ip;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setContent($content)
    {
        $this->content = $this->normalizeContent(
            $this->trimContent($content)
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
        if ($this->ip == null) { return ;}

        $this->ip = inet_ntop($ip);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getToken()
    {
        return $this->token;
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
        if ($this->ip == null) { return ;}
        
        return inet_pton($this->ip);
    }

    public function getDigest()
    {
        if (!empty($this->content)) {
            return md5($this->content);
        }
    }

    protected function normalizeContent($content)
    {
        return preg_replace(array('#\r?\n#', '#\r#'), "\n", $content);
    }

    protected function trimContent($content)
    {
        return trim($content);
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('content', new Assert\NotBlank);
    }
}
