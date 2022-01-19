<?php

namespace App\Models;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile")
 */
class Profile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="profile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, name="username", referencedColumnName="username")
     *
     */
    protected $user;


    /**
     * @var Connection The database connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function profileByUsername($username)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('profile')
            ->where('username = :username')
            ->setParameter('username', $username)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function addUser($username)
    {
        $values = [
            'username' => $username
        ];

        return $this->connection->insert('profile', $values);
    }

    public function deleteProfile($username)
    {
        $values = [
            'username' => $username
        ];

        return $this->connection->delete('profile', $values);
    }



}