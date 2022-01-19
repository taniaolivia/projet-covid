<?php

namespace App\Models;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="groupe",
 *     indexes={
 *              @Index(name="name_index", columns={"name"})
 *              }))
 */
class Group
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
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="groups")
     * @JoinTable(name="group_user",
     *      joinColumns={@JoinColumn(name="group_name", referencedColumnName="name")},
     *      inverseJoinColumns={@JoinColumn(name="user_username", referencedColumnName="username")}
     *      )
     */
    protected $members;

    /**
     * @ORM\OneToMany(targetEntity=Messenger::class, mappedBy="groups")
     */
    protected $messages;

    /**
     * @ORM\OneToMany(targetEntity=Board::class, mappedBy="group")
     */
    protected $boards;

    /**
     * @var Connection The database connection
     */
    private $connection;


    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function listGroups()
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('groupe')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function listGroupsByName()
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('name')
            ->from('groupe')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function newGroup($group)
    {
        $values = [
            'name' => $group,
        ];

        return $this->connection->insert('groupe', $values);
    }

    public function groupByName($name)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('name')
            ->from('groupe')
            ->where('name = :name')
            ->setParameter('name', $name)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function groupMembers($name, $user)
    {
        $values = [
            'group_name' => $name,
            'user_username' => $user
        ];

        return $this->connection->insert('group_user', $values);
    }

    public function groupMembersByGroup($name)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('user_username')
            ->from('group_user')
            ->where('group_name = :group_name')
            ->setParameter('group_name', $name)
            ->orderBy('user_username', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function allGroups($name, $user)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('group_user')
            ->where('group_name = :group_name', 'user_username = :user_username')
            ->setParameter('group_name', $name)
            ->setParameter('user_username', $user)
            ->orderBy('group_name', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function quitGroup($name, $user)
    {
        $values = [
            'group_name' => $name,
            'user_username' => $user
        ];

        return $this->connection->delete('group_user', $values);
    }

    public function groupByUser($user)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('group_name')
            ->from('group_user')
            ->where('user_username = :user_username')
            ->setParameter('user_username', $user)
            ->orderBy('user_username', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function searchGroupByName($name)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('name')
            ->from('groupe')
            ->where($query->expr()->like('name', ':name'))
            ->setParameter('name', '%'.$name.'%')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function quitGroupByUsername($user)
    {
        $values = [
            'user_username' => $user
        ];

        return $this->connection->delete('group_user', $values);
    }

}