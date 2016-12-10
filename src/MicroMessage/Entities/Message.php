<?php

namespace MicroMessage\Entities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @Table(name="messages")
 * @Entity
 */
class Message
{
    /**
     * Cria uma nova mensagem com os valores passados
     *
     * @param string $author message author
     * @param string $text message text
     * @return Message
     */
    public static function create($author, $text)
    {
        $message = new Message();
        $message->setAuthor($author);
        $message->setMessage($text);
        return $message;
    }

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
        return $this->id;
    }

    /**
     * @param int $id
     * @return Message
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthor()
    {
        return $this->author;
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
        return $this->message;
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

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('author', new Assert\NotBlank());
        $metadata->addPropertyConstraint('author', new Assert\Length(array('max' => 32)));
        $metadata->addPropertyConstraint('message', new Assert\NotBlank());
        $metadata->addPropertyConstraint('message', new Assert\Length(array('max' => 140)));
    }
}
