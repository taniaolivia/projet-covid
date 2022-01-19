<?php

namespace App\Controllers;

use App\Models\Group;
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

class GroupController
{
    private $container;
    public $group;
    public $user;

    public function __construct(ContainerInterface $container, Group $group, User $user)
    {
        $this->container = $container;
        $this->group = $group;
        $this->user = $user;
    }


    public function listGroups(Request $request, Response  $response)
    {
        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $_SESSION['token'] = ['nameKey' => $nameKey, 'valueKey' => $valueKey, 'name' => $name, 'value' => $value];

        $token = $_SESSION['token'];

        $list = $this->group->listGroups();

        return $this->container->get('view')->render($response, 'groups/listGroups.twig', ['list' => $list, 'token' => $token]);
    }

    public function myGroup(Request $request, Response  $response)
    {
        if(isset($_SESSION['session_login']))
        {
            $user = $_SESSION['session_login'];
            $list = $this->group->groupByUser($user);

            return $this->container->get('view')->render($response, 'groups/mygroup.twig', ['list' => $list]);
        }
        else
        {
            return $response->withHeader('Location', '/');
        }
    }

    public function createGroup(Request $request, Response  $response)
    {
        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $_SESSION['token'] = ['nameKey' => $nameKey, 'valueKey' => $valueKey, 'name' => $name, 'value' => $value];

        $token = $_SESSION['token'];

        if(isset($_SESSION['session_login']))
        {
            return $this->container->get('view')->render($response, 'groups/createGroup.twig', ['token' => $token]);
        }
        else
        {
            return $response->withHeader('Location', '/');
        }
    }

    public function addGroup(Request $request, Response  $response)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if (isset($_POST['name']))
            {
                $name = htmlentities($_POST['name']);
                $me = $_SESSION['session_login'];
                $this->group->newGroup($name);
                $this->group->groupMembers($name, $me);
                $message = "Group has been created successfully!";
                return $this->container->get('view')->render($response, 'groups/createGroup.twig', ['message' => $message]);
            }
        }
        else
        {
            return $response->withHeader('Location', '/');
        }
        exit;
    }

    public function groups(Request $request, Response  $response, array $args)
    {
        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $_SESSION['token'] = ['nameKey' => $nameKey, 'valueKey' => $valueKey, 'name' => $name, 'value' => $value];

        $token = $_SESSION['token'];
        $name = $args['name'];
        $gname = $this->group->groupByName($name);
        $members = $this->group->groupMembersByGroup($name);

        return $this->container->get('view')->render($response, 'groups/groups.twig', ["gname" => $gname, "members" => $members, 'token' => $token]);
    }

    public function joinGroup(Request $request, Response  $response)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if (isset($_POST['groupname']) && isset($_SESSION['session_login']))
            {
                $group_name = htmlentities($_POST['groupname']);
                $user_username = $_SESSION['session_login'];

                if($this->group->allGroups($group_name, $user_username))
                {
                    $error = "This user already joined this group!";

                    return $this->container->get('view')->render($response, 'groups/groups.twig', ['message' => $error]);
                }
                else
                {
                    $members = $this->group->groupMembers($group_name, $user_username);
                    $added = "You have joined this group successfully!";

                    return $this->container->get('view')->render($response, 'groups/groups.twig', ['message' => $added, 'members' => $members]);
                }
            }
            else
            {
                return $response->withHeader('Location', '/');
            }
        }
        exit;
    }

    public function quitGroup(Request $request, Response  $response)
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET")
        {
            if (isset($_SESSION['session_login']))
            {
                $name = htmlentities($_GET['quitGroup']);
                $session = $_SESSION['session_login'];
                if ($this->group->quitGroup($name, $session))
                {
                    $quit = "You have successfully quit the group !";
                    return $this->container->get('view')->render($response, 'groups/groups.twig', ['message' => $quit]);
                }
                else
                {
                    return $response->withHeader('Location', '/');
                }
            }
            else
            {
                return $response->withHeader('Location', '/');
            }
        }
        exit;
    }

    public function searchGroup(Request $request, Response  $response)
    {
        if (isset($_SESSION['session_login']) || !(isset($_SESSION['session_login'])))
        {
            if(isset($_POST['search']))
            {
                $search = htmlentities($_POST['search']);
                $listSearch = $this->group->searchGroupByName($search);
                return $this->container->get('view')->render($response, 'groups/listGroups.twig', ['listSearch' => $listSearch]);
            }
            else
            {
                return $response->withHeader('Location', "/");

            }
        }
        exit;
    }
}


