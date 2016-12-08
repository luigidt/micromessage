<?php

namespace MicroMessage\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Table(name="messages")
 * @Entity
 */
class Message
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     * @var int|null
     */
    private $id;

    /**
     * @Column(type="string", length=32, nullable=false)
     * @var string|null
     */
    private $author;

    /**
     * @Column(type="string", length=140, nullable=false)
     * @var string|null
     */
    private $message;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $id;
    }

    /**
     * @return string|null
     */
    public function getAuthor()
    {
        return $author;
    }

    /**
     * @param string $author
     * @return Message
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $message;
    }

    /**
     * @param string $message
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}
