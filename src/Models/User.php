<?php

namespace App\Models;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="user",
 *    uniqueConstraints={
 *          @UniqueConstraint(name="username_unq",
 *                            fields={"username"})})
 */
class User
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
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $infected;

    /**
     * @ORM\Column(type="string", options={"default" : "hors ligne"}, nullable=true)
     *
     */
    protected $status;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $latitude;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="user_username")
     */
    protected $contact_username;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="contact_username")
     */
    protected $user_username;


    /**
     * @ORM\OneToMany(targetEntity=Messenger::class, mappedBy="me")
     */
    protected $user_uname;

    /**
     * @ORM\OneToOne(targetEntity=Profile::class, mappedBy="user", cascade={"persist", "remove"})
     */
    protected $profile;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, mappedBy="members")
     *
     */
    protected $groups;

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

    public function allUsers($login, $anonyme)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('user')
            ->where('username <> :username1', 'username <> :username2' )
            ->setParameter('username1', $login)
            ->setParameter('username2', $anonyme)
            ->orderBy('username', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function allUsersByLocation()
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('username, latitude, longitude')
            ->from('user')
            ->where('infected = :infected')
            ->setParameter('infected', 'yes')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function allUsersByUsername($login)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('user')
            ->where('username = :username')
            ->setParameter('username', $login)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function allUsersOnlyUsername($login)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('username')
            ->from('user')
            ->where('username = :username')
            ->setParameter('username', $login)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }


    public function allUsersByUsernamePwd($login, $pwd)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('username, password')
            ->from('user')
            ->where('username = :username AND password = :password')
            ->setParameters(['username' => $login,
                             'password' => $pwd])
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function addUser($login, $pwd, $email, $infected)
    {
        $values = [
            'username' => $login,
            'password' => $pwd,
            'email' => $email,
            'infected'=> $infected
        ];
        return $this->connection->insert('user', $values);
    }

    public function addLocation($login, $lat, $long)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->update('user')
            ->set('latitude', ':lat')
            ->set('longitude', ':long')
            ->where('username = :username')
            ->setParameter('username',$login)
            ->setParameter('lat', $lat)
            ->setParameter('long', $long)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function deleteUser($login)
    {
        $values = [
            'username' => $login,
        ];
        return $this->connection->delete('user', $values);
    }

    public function updatePwd($login, $pwd)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->update('user')
            ->set('password', ':password')
            ->where('username = :username')
            ->setParameter('username',$login)
            ->setParameter('password', $pwd)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function updateInfected($login, $infected)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->update('user')
            ->set('infected', ':infected')
            ->where('username = :username')
            ->setParameter('username',$login)
            ->setParameter('infected', $infected)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function updateStatus($login, $status)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->update('user')
            ->set('status', ':status')
            ->where('username = :username')
            ->setParameter('username',$login)
            ->setParameter('status', $status)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function searchUser($login, $session)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('user')
            ->where($query->expr()->like('username', ':username'))
            ->andWhere('username <> :session')
            ->setParameter('username', '%'.$login.'%')
            ->setParameter('session', $session)
            ->orderBy('username', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function searchAllUsers($login)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('*')
            ->from('user')
            ->where($query->expr()->like('username', ':username'))
            ->setParameter('username', '%'.$login.'%')
            ->orderBy('username', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function allUsersUsername()
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('username')
            ->from('user')
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

    public function listLocationByUsername($username)
    {
        $query = $this->connection->createQueryBuilder();

        $rows = $query
            ->select('username, latitude, longitude')
            ->from('user')
            ->where('username = :username')
            ->setParameter('username', $username)
            ->executeQuery()
            ->fetchAllAssociative();
        return $rows;
    }

}