<?php

namespace App\Controllers;

use App\Models\Board;
use App\Models\Group;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

class BoardController
{
    private $container;
    public $board;
    public $group;

    public function __construct(ContainerInterface $container, Board $board, Group $group)
    {
        $this->container = $container;
        $this->board = $board;
        $this->group = $group;
    }

    public function createPost(Request $request, Response  $response, array $args)
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
            $group = $args['group'];
            $list = $this->group->groupByName($group);
            return $this->container->get('view')->render($response, 'boards/createPost.twig', ['group' => $group, 'list' => $list, 'token' => $token]);
        }
        else
        {
            return $response->withHeader('Location', '/');
        }
    }

    public function addPost(Request $request, Response  $response, array $args)
    {
        $targetDir = "../public/uploads/";
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf', 'doc', 'docx', 'mp4', 'mov', 'mp3');

        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if (isset($_SESSION['session_login']) && isset($_POST['title']) && isset($_POST['description'])) {
                if (!empty($_FILES["file"]["name"])) {
                    if (in_array($fileType, $allowTypes)) {
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                            $group_name = htmlentities($_POST['groupname']);
                            $title = htmlentities($_POST['title']);
                            $description = htmlentities($_POST['description']);
                            $user = $_SESSION['session_login'];

                            $this->board->newPost($group_name, $user, null, $title, $description, $fileName);
                            return $response->withHeader('Location', "/groups/$group_name");
                        }
                    }
                } else {
                    $group_name = htmlentities($_POST['groupname']);
                    $title = htmlentities($_POST['title']);
                    $description = htmlentities($_POST['description']);
                    $user = $_SESSION['session_login'];

                    $this->board->newPost($group_name, $user, null, $title, $description, null);
                    return $response->withHeader('Location', "/groups/$group_name");
                }
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
        exit;
    }

    public function listPosts(Request $request, Response  $response, array $args)
    {
        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $_SESSION['token'] = ['nameKey' => $nameKey, 'valueKey' => $valueKey, 'name' => $name, 'value' => $value];

        $token = $_SESSION['token'];

        if ($_SERVER["REQUEST_METHOD"] == "GET")
        {
            if (isset($_SESSION['session_login'])|| !(isset($_SESSION['session_login'])))
            {
                $group_name = $args['group'];
                $list = $this->board->allPostsByGroup($group_name);
                return $this->container->get('view')->render($response, 'boards/listPosts.twig', ['group'=> $group_name, 'list' => $list, 'token' => $token]);
            }
            else
            {
                return $response->withHeader('Location', '/');
            }
        }
        exit;
    }

    public function posts(Request $request, Response  $response, array $args)
    {
        if (isset($_SESSION['session_login']) || !(isset($_SESSION['session_login'])))
        {
            $title = $args['title'];
            $group_name = $args['group'];
            $info = $this->board->allPostsByGroupTitle($group_name, $title);
            return $this->container->get('view')->render($response, 'boards/posts.twig', ["info" => $info, "group" => $group_name, "title" => $title]);
        }
        exit;
    }


    public function deletePost(Request $request, Response  $response)
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET")
        {
            if (isset($_SESSION['session_login']) || !(isset($_SESSION['session_login'])))
            {
                $deletePost = htmlentities($_GET['deletePost']);

                if ($this->board->deletePost($deletePost))
                {
                    $deleted = "You have successfully deleted a post !";
                    return $this->container->get('view')->render($response, 'boards/posts.twig', ['message' => $deleted]);
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

    public function searchPost(Request $request, Response  $response, array $args)
    {
        if (isset($_SESSION['session_login']) || !(isset($_SESSION['session_login'])))
        {
            if(isset($_POST['search']))
            {
                $search = htmlentities($_POST['search']);
                $group_name = htmlentities($_POST['groupname']);
                $listSearch = $this->board->searchGroupTitle($group_name, $search);
                return $this->container->get('view')->render($response, 'boards/listPosts.twig', ['listSearch' => $listSearch]);

            }
            else
            {
                return $response->withHeader('Location', "/");

            }
        }
        exit;
    }
}


