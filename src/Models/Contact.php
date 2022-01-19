<?php

namespace App\Models;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="contact",
 *     indexes={
 *              @Index(name="uname_index", columns={"username"})
 *              }))
 */
class Contact
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
    public $username;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="contact_username")
     * @JoinColumn(name="user_username", referencedColumnName="username", nullable=false)
     *
     */
    protected $user_username;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="user_username")
     * @JoinColumn(name="contact_username", referencedColumnName="username", nullable=false)
     *
     */
    protected $contact_username;

    /**
     * @ORM\OneToMany(targetEntity=Messenger::class, mappedBy="friend")
     */
    protected $contact_uname;


    /**
     * @var Connection The database connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function allContacts()
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('contact')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function allContactsByUsername($user_username, $contact_username, $username)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('contact')
            ->where('user_username = :user_username', 'contact_username = :contact_username', 'username = :username')
            ->setParameter('user_username', $user_username)
            ->setParameter('contact_username', $contact_username)
            ->setParameter('username', $username)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function addToContact($username, $user_username, $contact_username)
    {
        $values = [
            'username' => $username,
            'contact_username' => $contact_username,
            'user_username' => $user_username,
        ];
        return $this->connection->insert('contact', $values);
    }

    public function deleteContact($contact_username, $user_username)
    {
        $values = [
            'contact_username' => $contact_username,
            'user_username' => $user_username
        ];
        return $this->connection->delete('contact', $values);
    }

    public function deleteAllContact($user_username)
    {
        $values = [
            'user_username' => $user_username
        ];
        return $this->connection->delete('contact', $values);
    }

    public function deleteUser($contact_username)
    {
        $values = [
            'contact_username' => $contact_username,
        ];
        return $this->connection->delete('contact', $values);
    }



    public function allContactsOnlyMine($login)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('contact_username')
            ->from('contact')
            ->where('user_username = :user_username')
            ->setParameter('user_username', $login)
            ->orderBy('contact_username', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function allContactsOnlyMine2($login)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('user_username')
            ->from('contact')
            ->where('contact_username = :contact_username')
            ->setParameter('contact_username', $login)
            ->orderBy('user_username', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function contactByUname($username)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('username')
            ->from('contact')
            ->where('username = :username')
            ->setParameter('username', $username)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }


}