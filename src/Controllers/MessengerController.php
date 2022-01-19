<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Models\Group;
use App\Models\User;
use App\Models\Messenger;
use DateTime;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

class MessengerController
{
    private $container;
    public $messenger;
    public $contact;
    public $group;
    public $user;


    public function __construct(ContainerInterface $container, Messenger $messenger, Contact $contact, Group $group, User $user)
    {
        $this->container = $container;
        $this->messenger = $messenger;
        $this->contact = $contact;
        $this->group = $group;
        $this->user = $user;
    }

    public function createChatContact(Request $request, Response $response, array $args)
    {
        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $_SESSION['token'] = ['nameKey' => $nameKey, 'valueKey' => $valueKey, 'name' => $name, 'value' => $value];

        $token = $_SESSION['token'];

        if (isset($_SESSION['session_login']))
        {
            $friend = $args['friend'];
            $chats = $this->messenger->allChats($_SESSION['session_login'], $friend);
            $contact = $this->contact->contactByUname($friend);
            return $this->container->get('view')->render($response, 'messengers/messengerContact.twig', ['chats' => $chats, 'contact' => $contact, 'token' => $token]);
        }
        else
        {
            return $response->withHeader('Location', '/');
        }
    }

    public function addChatContact(Request $request, Response $response)
    {
        $targetDir = "../public/uploads/";
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf', 'doc', 'docx', 'mp4', 'mov', 'mp3');

        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if (isset($_SESSION['session_login']) && isset($_POST['message']))
            {
                if (!empty($_FILES["file"]["name"]))
                {
                    if (in_array($fileType, $allowTypes))
                    {
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath))
                        {
                            $friend = htmlentities($_POST['friend']);
                            $message = htmlentities($_POST['message']);
                            $username = '';
                            $me = $_SESSION['session_login'];

                            $this->messenger->addFileContact($username, $message, $me, $friend, $fileName);

                            return $response->withHeader('Location', "/myContact/messenger/$friend");
                        }
                    }
                }
                else
                {
                    $friend = htmlentities($_POST['friend']);
                    $message = htmlentities($_POST['message']);
                    $username = '';
                    $me = $_SESSION['session_login'];

                    $this->messenger->addChat($username, $message, $me, $friend);
                    return $response->withHeader('Location', "/myContact/messenger/$friend");
                }
            } else if (!(isset($_SESSION['session_login'])) && isset($_POST['message'])) {
                return $response->withHeader('Location', '/');

            }
        }
        else
        {
            return $response->withHeader('Location', '/');
        }
        exit;
    }

    public function createChatGroup(Request $request, Response $response, array $args)
    {
        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $_SESSION['token'] = ['nameKey' => $nameKey, 'valueKey' => $valueKey, 'name' => $name, 'value' => $value];

        $token = $_SESSION['token'];

        if (isset($_SESSION['session_login']))
        {
            $group_name = $args['name'];
            $group = $this->group->groupByName($group_name);

            for ($i = 1; $i <= 20; $i++)
            {
                $info = $this->messenger->allByName($group_name);
            }

            return $this->container->get('view')->render($response, 'messengers/messengerGroup.twig', ['group_name' => $group_name, 'group' => $group, 'info' => $info, 'token' => $token]);
        }
        else
        {
            $group_name = $args['name'];
            $group = $this->group->groupByName($group_name);

            for ($i = 1; $i <= 20; $i++)
            {
                $info = $this->messenger->allByName($group_name);
            }

            return $this->container->get('view')->render($response, 'messengers/messengerGroup.twig', ['group' => $group, 'info' => $info, 'token' => $token]);
        }
    }

    public function addChatGroup(Request $request, Response $response)
    {
        $targetDir = "../public/uploads/";
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf', 'doc', 'docx', 'mp4', 'mov', 'mp3');

        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if (isset($_SESSION['session_login']) && isset($_POST['message']))
            {
                if (!empty($_FILES["file"]["name"]))
                {
                    if (in_array($fileType, $allowTypes))
                    {
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath))
                        {
                            $group = htmlentities($_POST['group']);
                            $message = htmlentities($_POST['message']);
                            $username = '';
                            $me = $_SESSION['session_login'];

                            $this->messenger->addFileGroup($username, $message, $me, $group, $fileName);

                            return $response->withHeader('Location', "/groups/messenger/$group");
                        }
                    }
                }
                else
                {
                    $group = htmlentities($_POST['group']);
                    $message = htmlentities($_POST['message']);
                    $username = '';
                    $me = $_SESSION['session_login'];

                    $this->messenger->addChatGroup($username, $message, $me, $group);
                    return $response->withHeader('Location', "/groups/messenger/$group");
                }

            } else if (!(isset($_SESSION['session_login'])) && isset($_POST['message']))
            {
                if (!empty($_FILES["file"]["name"]))
                {
                    if (in_array($fileType, $allowTypes))
                    {
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath))
                        {
                            $group = htmlentities($_POST['group']);
                            $message = htmlentities($_POST['message']);
                            $username = 'Anonymous';
                            $me = null;

                            $this->messenger->addFileGroup($username, $message, $me, $group, $fileName);

                            return $response->withHeader('Location', "/groups/messenger/$group");
                        }
                    }
                }
                else
                {
                    $group = htmlentities($_POST['group']);
                    $message = htmlentities($_POST['message']);
                    $username = 'Anonymous';
                    $me = null;

                    $this->messenger->addChatGroup($username, $message, $me, $group);
                    return $response->withHeader('Location', "/groups/messenger/$group");
                }

            }
        }
        else
        {
            return $response->withHeader('Location', '/');
        }
        exit;
    }
}
