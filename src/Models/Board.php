<?php

namespace App\Models;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity
 * @ORM\Table(name="board")
 */
class Board
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $author;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $attachment;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="boards")
     * @JoinColumn(name="user_username", referencedColumnName="username", nullable=true)
     */
    protected $user;


    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="boards")
     * @JoinColumn(name="group_name", referencedColumnName="name", nullable=false)
     */
    protected $group;

    /**
     * @var Connection The database connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function newPost($group, $user, $author, $title, $description, $attachment)
    {
        $values = [
            'group_name' => $group,
            'user_username' => $user,
            'author' => $author,
            'title' => $title,
            'description' => $description,
            'attachment' => $attachment
        ];

        return $this->connection->insert('board', $values);
    }

    public function allPosts()
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('board')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function allPostsByGroup($group)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('board')
            ->where('group_name = :group_name')
            ->setParameter('group_name', $group)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function allPostsByGroupTitle($group, $title)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('board')
            ->where('group_name = :group_name', 'title = :title')
            ->setParameter('group_name', $group)
            ->setParameter('title', $title)
            ->orderBy('title', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function deletePost($title)
    {
        $values = [
            'title' => $title
        ];

        return $this->connection->delete('board', $values);
    }

    public function deletePostByUsername($username)
    {
        $values = [
            'user_username' => $username
        ];

        return $this->connection->delete('board', $values);
    }

    public function searchGroupTitle($group, $title)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('board')
            ->where('group_name = :group_name', $query->expr()->like('title', ':title'))
            ->setParameter('group_name', $group)
            ->setParameter('title', '%'.$title.'%')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }
}