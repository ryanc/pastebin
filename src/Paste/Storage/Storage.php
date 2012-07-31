<?php

namespace Paste\Storage;

use Paste\Entity\Paste;
use Paste\Math\Base62;

class Storage
{
    protected $db;

    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        $this->db = $db;
    }

    public function get($id)
    {
        $sql = 'SELECT id, paste, filename, token, timestamp, ip '
             . 'FROM pastes '
             . 'WHERE token = :token';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':token', $id);
        $stmt->execute();
        $result = $stmt->fetch();

        // The statement failed to execute.
        if (false === $stmt->execute()) {
            throw new \RuntimeException('SQL statement failed to execute.');
        }

        // There are no results.
        if (false === $result = $stmt->fetch()) {
            return false;
        }

        // Assemble a paste model.
        $paste = new Paste();
        $paste->setId($result['id']);
        $paste->setContents($result['paste']);
        $paste->setTimestamp($result['timestamp']);
        $paste->setToken($result['token']);
        $paste->setFilename($result['filename']);
        $paste->setBinaryIp($result['ip']);

        return $paste;
    }

    public function save($paste)
    {
        $base62 = new Base62;

        if (null != $filename = $paste->getFilename()) {
            $filename = $paste->getFilename();
        }

        $sql = 'INSERT INTO pastes (paste, filename, ip) '
             . 'VALUES (:paste, :filename, :ip)';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':paste', $paste->getContents());
        $stmt->bindValue(':filename', $filename);
        $stmt->bindValue(':ip', $paste->getBinaryIp());
        $stmt->execute();
        $id = $this->db->lastInsertId();

        $stmt = null;

        $token = $base62->encode($id);

        $sql = 'UPDATE pastes '
             . 'SET token = :token WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $token;
    }

    /*
    public function getId($int)
    {
        $alphabet = self::DIGITS . self::ASCII_LOWERCASE . self::ASCII_UPPERCASE;

        if ($int === 0) {
            return $alphabet[0];
        }

        $stack = array();
        
        while($int) {
            $remainder = $int % 62;
            $int = floor($int / 62);
            $stack[] = $alphabet[$remainder];
        }

        $stack = array_reverse($stack);

        return implode($stack);
    }
    */
}
