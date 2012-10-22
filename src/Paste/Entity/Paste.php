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

    protected $convertTabs;

    protected $highlight = true;

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setContent($content)
    {
        $this->content = $this->normalizeContent(
            $this->trimContent($content)
        );

        return $this;
    }

    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    public function setBinaryIp($ip)
    {
        $this->ip = inet_ntop($ip);

        return $this;
    }

    public function setConvertTabs($convert)
    {
        if ($convert === null) { return $this; }

        $this->convertTabs = (bool) $convert;

        return $this;
    }

    public function setHighlight($highlight)
    {
        if ($highlight === null) { return $this; }

        $this->highlight = (bool) $highlight;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getContent()
    {
        if ($this->convertTabs === true) {
            $this->content = $this->tabsToSpaces($this->content);
        }

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
        if ($this->ip == null) { return; }
        
        return inet_pton($this->ip);
    }

    public function getDigest()
    {
        if (!empty($this->content)) {
            return md5($this->getContent());
        }
    }

    public function getConvertTabs()
    {
        return $this->convertTabs;
    }

    public function getHighlight()
    {
        return $this->highlight;
    }

    protected function normalizeContent($content)
    {
        return preg_replace(array('#\r?\n#', '#\r#'), "\n", $content);
    }

    protected function trimContent($content)
    {
        return trim($content);
    }

    protected function tabsToSpaces($content)
    {
        $spaces = str_repeat(' ', 4);

        return preg_replace("#\t#", $spaces, $content);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('content', new Assert\NotBlank);
    }
}
