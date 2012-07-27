<?php

namespace Paste\Storage;

use Paste\Entity\Paste;

class Storage
{
    protected $db;
    
    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        $this->db = $db;
    }

    public function get($id)
    {
        $sql = 'SELECT id, paste, filename, token, timestamp FROM pastes WHERE token = :token';
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

        return $paste;
    }

    public function save($paste)
    {
        if (null != $filename = $paste->getFilename()) {
            $filename = $paste->getFilename();
        }

        $sql = 'INSERT INTO pastes (paste, filename) VALUES (:paste, :filename)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':paste', $paste->getContents());
        $stmt->bindValue(':filename', $filename);
        $stmt->execute();
        $id = $this->db->lastInsertId();

        $stmt = null;

        $token = $this->getId($id);

        $sql = 'UPDATE pastes SET token = :token WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $token;
    }

    /*
    public function getId($int)
    {
        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

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

    public function getId($int)
    {
        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $id = '';
        $int = (int) $int;

        if ($int === 0) {
            return $alphabet[0];
        }

        $stack = array();
        
        while($int) {
            $remainder = bcmod($int, 62);
            $int = $this->bcfloor(bcdiv($int, 62));
            $id = $alphabet[$remainder] . $id;
        }

        return $id;
    }

    /**
     * Use the bcmath functions to make a floor() function.
     *
     * Inspired by: http://stackoverflow.com/a/1653826
     *
     * @param string $number
     * @return string
     */
    private function bcfloor($number)
    {
        // If there is not a decimal place, just return the number.
        if (strpos($number, '.') === false) {
            return $number;
        }

        // If the number is negative, subtract 1 which rounds to the lowest
        // negative integer.
        if ($number[0] === '-') {
            return bcsub($number, 1, 0);
        }

        // If the number is positive and whole, add zero.
        return bcadd($number, 0, 0);
    }
}
