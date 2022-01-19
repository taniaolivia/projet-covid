<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="messenger",
 *     indexes={
 *              @Index(name="uname_index", columns={"username"})
 *              }))
 */
class Messenger
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $message;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $file;


    /**
     * @ORM\ManyToOne(targetEntity=Contact::class, inversedBy="contact_uname")
     * @JoinColumn(name="friend", referencedColumnName="username", nullable=true)
     *
     */
    protected $friend;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="user_uname")
     * @JoinColumn(name="me", referencedColumnName="username", nullable=true)
     *
     */
    protected $me;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="messages")
     * @JoinColumn(name="group_name", referencedColumnName="name", nullable=true)
     */
    protected $group_name;


    /**
     * @var Connection The database connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function addChat($username, $chat, $me, $friend)
    {
        $values = [
            'username' => $username,
            'message' => $chat,
            'me' => $me,
            'friend' => $friend
        ];

        $this->connection->insert('messenger', $values);
    }

    public function addChatGroup($username, $chat, $me, $group)
    {
        $values = [
            'username' => $username,
            'message' => $chat,
            'me' => $me,
            'group_name' => $group
        ];

        $this->connection->insert('messenger', $values);
    }

    public function addChatGroupAnonyme($username, $chat, $group)
    {
        $values = [
            'username' => $username,
            'message' => $chat,
            'group_name' => $group
        ];

        $this->connection->insert('messenger', $values);
    }


    public function allChats($me, $friend)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('messenger')
            ->where('me = :me AND friend = :friend')
            ->orWhere('me = :friend AND friend = :me')
            ->andWhere('me IS NOT NULL', 'friend IS NOT NULL')
            ->setParameter('me', $me)
            ->setParameter('friend', $friend)
            ->orderBy('id', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }


    public function allChatsGroup($group)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('messenger')
            ->where('group_name = :group')
            ->setParameter('group', $group)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function chatByFriend($me, $friend)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('friend')
            ->from('messenger')
            ->where('friend = :friend', 'me = :me')
            ->setParameter('me', $me)
            ->setParameter('friend', $friend)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function deleteChat($friend, $me)
    {
        $values = [
            'friend' => $friend,
            'me' => $me
        ];

        $this->connection->delete('messenger', $values);
    }

    public function deleteAllChatMe($me)
    {
        $values = [
            'me' => $me
        ];

        $this->connection->delete('messenger', $values);
    }

    public function deleteAllChatFriend($friend)
    {
        $values = [
            'friend' => $friend
        ];

        $this->connection->delete('messenger', $values);
    }

    public function addFileGroup($username, $chat, $me, $group, $file)
    {
        $values = [
            'username' => $username,
            'message' => $chat,
            'me' => $me,
            'group_name' => $group,
            'file' => $file
        ];

        $this->connection->insert('messenger', $values);
    }


    public function allByName($group)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('file, me, message, username')
            ->from('messenger')
            ->where( 'group_name = :group')
            ->setParameter('group', $group)
            ->executeQuery()
            ->fetchAllAssociative();

        return $rows;
    }


    public function addFileContact($username, $chat, $me, $friend, $file)
    {
        $values = [
            'username' => $username,
            'message' => $chat,
            'me' => $me,
            'friend' => $friend,
            'file' => $file
        ];

        $this->connection->insert('messenger', $values);
    }
}