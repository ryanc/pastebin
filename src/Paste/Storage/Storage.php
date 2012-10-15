<?php

namespace Paste\Storage;

use Paste\Entity\Paste;
use Paste\Math\Base62;

class Storage
{
    private $db;

    private $logger;

    public function __construct(
        \Doctrine\DBAL\Connection $db,
        \Monolog\Logger $logger
    )
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function get($id)
    {
        $sql = "SELECT p.id, c.content, p.filename, p.token, datetime(p.timestamp, 'unixepoch') AS timestamp, p.ip, p.highlight "
             . "FROM pastes p, paste_content c "
             . "WHERE p.content_id = c.id AND p.token = :token";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':token', $id);
        $stmt->execute();
        $result = $stmt->fetch();

        // The statement failed to execute.
        if (false === $stmt->execute()) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException('SQL statement failed to execute.');
            // @codeCoverageIgnoreEnd
        }

        // There are no results.
        if (false === $result = $stmt->fetch()) {
            return false;
        }

        // Assemble a paste model.
        $paste = new Paste();
        $paste->setId($result['id']);
        $paste->setContent($result['content']);
        $paste->setTimestamp(new \DateTime($result['timestamp']));
        $paste->setToken($result['token']);
        $paste->setFilename($result['filename']);
        $paste->setBinaryIp($result['ip']);
        $paste->setHighlight($result['highlight']);

        return $paste;
    }

    public function save($paste)
    {
        $base62 = new Base62;

        if (null != $filename = $paste->getFilename()) {
            $filename = $paste->getFilename();
        }

        $contentId = $this->alreadyExists($paste->getDigest());

        if ($contentId === false) {
            $sql = 'INSERT INTO paste_content (content, digest) '
                 . 'VALUES (:content, :digest)';

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':content', $paste->getContent());
            $stmt->bindValue(':digest', $paste->getDigest()); 
            $stmt->execute();
            $contentId = $this->db->lastInsertId();
        }

        $sql = 'INSERT INTO pastes (filename, ip, content_id, highlight) '
             . 'VALUES (:filename, :ip, :content_id, :highlight)';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':filename', $filename);
        $stmt->bindValue(':ip', $paste->getBinaryIp());
        $stmt->bindValue(':content_id', $contentId);
        $stmt->bindValue(':highlight', $paste->getHighlight());
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

    public function alreadyExists($digest)
    {
        $sql = 'SELECT id FROM paste_content WHERE digest = :digest';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':digest', $digest);
        $stmt->execute();

        $contentId = $stmt->fetchColumn();

        if ($contentId !== false) {
            $contentId = (int) $contentId;

            $this->logger->addDebug(sprintf(
                "Found identical content at id %s", $contentId
            ));
        }

        return $contentId;
    }

    /**
     * Get the ID of the most recent paste.
     *
     * @return string
     *  ID of the most recent paste.
     */
    public function getLatest()
    {
        $sql = 'SELECT token FROM pastes ORDER BY id DESC LIMIT 1';

        return $this->db->fetchColumn($sql);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function close()
    {
        $this->db = null;
    }

    /**
     * @codeCoverageIgnore
     */
    public function __destruct()
    {
        $this->close();
    }
}
