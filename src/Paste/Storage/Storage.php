<?php

namespace Paste\Storage;

class Storage
{
    protected $db;
    
    protected function normalizeContent($content)
    {
        return preg_replace(array('#\r?\n#', '#\r#'), "\n", $content);
    }

    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        $this->db = $db;
    }

    public function get($id)
    {
        // $sql = 'SELECT paste, created_at FROM pastes WHERE id = ?';
        $sql = 'SELECT paste, filename, timestamp FROM pastes WHERE token = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $paste = $stmt->fetch();

        return $paste;
    }

    public function save($paste)
    {
        if (null != $filename = $paste['filename']) {
            $filename = $paste['filename'];
        }

        $paste = $this->normalizeContent($paste['paste']);

        // $sql = 'INSERT INTO pastes (paste) VALUES (?) RETURNING id';
        $sql = 'INSERT INTO pastes (paste, filename) VALUES (?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $paste);
        $stmt->bindValue(2, $filename);
        $stmt->execute();
        $id = $this->db->lastInsertId();

        $stmt = null;

        $token = $this->getId($id);

        // $sql = 'UPDATE pastes SET slug = ? WHERE id = ?';
        $sql = 'UPDATE pastes SET token = ? WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $token);
        $stmt->bindValue(2, $id);
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
