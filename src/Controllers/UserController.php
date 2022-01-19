<?php

namespace App\Controllers;

use App\Models\Messenger;
use App\Models\User;
use App\Models\Profile;
use App\Models\Contact;
use App\Models\Board;
use App\Models\Group;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

class UserController
{
    private $container;
    public $user;
    public $profile;
    public $contact;
    public $messenger;
    public $board;
    public $group;

    public function __construct(ContainerInterface $container, User $user, Profile $profile, Contact $contact, Messenger $messenger, Board $board, Group $group)
    {
        $this->container = $container;
        $this->user = $user;
        $this->profile = $profile;
        $this->contact = $contact;
        $this->messenger = $messenger;
        $this->board = $board;
        $this->group = $group;
    }


    public function myprofile(Request $request, Response  $response, array $args)
    {
            $username = $args['username'];
            $info = $this->user->allUsersByUsername($username);
            $uname = $this->user->allUsersOnlyUsername($username);
            return $this->container->get('view')->render($response, 'profiles/myprofile.twig', ['uname' => $uname, 'info' => $info]);
    }


    public function signUp(Request $request, Response  $response)
    {
        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $_SESSION['token'] = ['nameKey' => $nameKey, 'valueKey' => $valueKey, 'name' => $name, 'value' => $value];

        $token = $_SESSION['token'];
        return $this->container->get('view')->render($response, 'users/signUp.twig', ['token' => $token]);
    }


    public function signIn(Request $request, Response  $response)
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
            return $response->withHeader('Location', "/myprofile/".$_SESSION['session_login']);
        }
        else
        {
            return $this->container->get('view')->render($response, 'users/signIn.twig', ['token' => $token]);
        }
    }


    public function signOut(Request $request, Response  $response)
    {
        if(isset($_SESSION['session_login']))
        {
            $this->user->updateStatus($_SESSION['session_login'], "hors ligne");
            session_destroy();
        }
        return $response->withHeader('Location', '/');
    }


    public function authenticate(Request $request, Response  $response)
    {
        if (isset($_POST['login']) && isset($_POST['password']))
        {
            $login = htmlentities($_POST['login']);
            $password = htmlentities($_POST['password']);

            if ($this->user->allUsersByUsernamePwd($login, md5($password)))
            {
                $_SESSION['session_login'] = $login;
                $_SESSION['session_password'] = $password;
                $status = "en ligne";
                if(isset($_SESSION['session_login']))
                {
                    $session = $_SESSION['session_login'];
                    $this->user->updateStatus($login, $status);
                    return $response->withHeader('Location', "/myprofile/$session");
                }
            }
            else
            {
                $error = "Incorrect username or password !";
                return $this->container->get('view')->render($response, 'users/signIn.twig', ['message' => $error]);
            }
        }
        else
        {
            $error = "Username doesn't exist !";
            return $this->container->get('view')->render($response, 'users/signIn.twig', ['message' => $error]);

        }
        exit;
    }


    public function addUser(Request $request, Response  $response)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if ( isset($_POST['login']) && isset($_POST['password']) && isset($_POST['verifypwd']) && isset($_POST['email']) && isset($_POST['infected']))
            {
                $login = htmlentities($_POST['login']);
                $password = htmlentities($_POST['password']);
                $verifypwd = htmlentities($_POST['verifypwd']);
                $email = htmlentities($_POST['email']);
                $infected = htmlentities($_POST['infected']);
                $hash = md5($password);

                if ($password != $verifypwd)
                {
                    $error = "Confirmation password incorrect!";
                    return $this->container->get('view')->render($response, 'users/signUp.twig', ['message' => $error]);
                }
                else if (empty($login) || empty($password) || empty($verifypwd))
                {
                    $error = "You haven't fill in the form above!";
                    return $this->container->get('view')->render($response, 'users/signUp.twig', ['message' => $error]);
                }
                else if ($this->user->addUser($login, $hash, $email, $infected))
                {
                    $this->profile->addUser($login);
                    return $response->withHeader('Location', '/signUp');
                }
            }
        }
        exit;
    }

    public function addLocation(Request $request, Response  $response)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            $login = $_SESSION['session_login'];
            $lat = htmlentities($_POST['lat']);
            $long = htmlentities($_POST['long']);

            $this->user->addLocation($login, $lat, $long);
            return $response->withHeader('Location', "/myprofile/$login");

        }
        exit;
    }


    public function deleteUser(Request $request, Response  $response)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET' && $_SESSION['session_login'] == true)
        {
            $loggedin = $_SESSION["session_login"];
            $this->board->deletePostByUsername($loggedin);
            $this->group->quitGroupByUsername($loggedin);
            $this->messenger->deleteAllChatMe($loggedin);
            $this->messenger->deleteAllChatFriend($loggedin);
            $this->contact->deleteAllContact($loggedin);
            $this->contact->deleteUser($loggedin);
            $this->profile->deleteProfile($loggedin);
            $this->user->deleteUser($loggedin);
            $deleted = "Your account have been deleted successfully!";
            return $this->container->get('view')->render($response, 'users/signIn.twig', ['message' => $deleted]);
        }
        else
        {
            return $response->withHeader('Location', '/');
        }
    }

    public function formUpdatePwd(Request $request, Response  $response)
    {
        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $_SESSION['token'] = ['nameKey' => $nameKey, 'valueKey' => $valueKey, 'name' => $name, 'value' => $value];

        $token = $_SESSION['token'];

        $session = $_SESSION['session_login'];

        return $this->container->get('view')->render($response, 'users/formpassword.twig', ['token' => $token, 'session' => $session]);

    }

    public function updatePwd(Request $request, Response  $response)
    {
        $session = $_SESSION["session_login"];

        if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['session_login'] == true)
        {
            if (isset($_POST['newpassword']) && isset($_POST['verifynewpwd']))
            {
                $loggedin = $_SESSION['session_login'];
                $newpassword = htmlentities($_POST['newpassword']);
                $verifynewpwd = htmlentities($_POST['verifynewpwd']);
                $hash = md5($newpassword);

                if ($newpassword == $verifynewpwd)
                {
                    $this->user->updatePwd($loggedin, $hash);
                    return $response->withHeader('Location', "/");
                }
                else if ($newpassword != $verifynewpwd)
                {
                    $error = "Confirmation new password doesn't match";
                    return $this->container->get('view')->render($response, 'users/formpassword.twig', ['message' => $error]);
                }
            }
            else
            {
                return $this->container->get('view')->render($response, 'users/formpassword.twig', ['session' => $session]);
            }
        }
        else
        {
            return $this->container->get('view')->render($response, 'users/formpassword.twig', ['session' => $session]);
        }

        return $this->container->get('view')->render($response, 'users/formpassword.twig', ['session' => $session]);

    }

    public function formPositive(Request $request, Response  $response)
    {
        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $_SESSION['token'] = ['nameKey' => $nameKey, 'valueKey' => $valueKey, 'name' => $name, 'value' => $value];

        $token = $_SESSION['token'];
        $session = $_SESSION["session_login"];
        return $this->container->get('view')->render($response, 'infected/forminfected.twig', ['token' => $token, 'session' => $session]);

    }

    public function reportPositive(Request $request, Response  $response)
    {
        $session = $_SESSION["session_login"];
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['session_login']))
        {
                $pcrTest = htmlentities($_POST['test']);
                $infected = htmlentities($_POST['infected']);

                if ($pcrTest == 'yes')
                {
                    $this->user->updateInfected($session, $infected);
                    $message = "Your report have been sent successfully!";
                    return $this->container->get('view')->render($response, 'infected/forminfected.twig', ['message' => $message, 'session' => $session]);
                }
                else if( $pcrTest == 'no')
                {
                    $message = "Please take a PCR-RT test before reporting!";
                    return $this->container->get('view')->render($response, 'infected/forminfected.twig', ['message' => $message, 'session' => $session]);
                }

        }
        else if(!(isset($_SESSION['session_login'])))
        {
            $error = "Please sign in first before reporting!";
            return $this->container->get('view')->render($response, 'users/signin.twig', ['message' => $error]);
        }
        exit;
    }

    public function listUsers(Request $request, Response  $response)
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
            $list = $this->user->allUsers($_SESSION['session_login'], 'Anonymous');
            return $this->container->get('view')->render($response, 'users/listUsers.twig', ['list' => $list, 'token' => $token]);
        }
        else
        {
            $list = $this->user->allUsersUsername();
            return $this->container->get('view')->render($response, 'users/listUsers.twig', ['list' => $list, 'token' => $token]);

        }
    }

    public function searchUser(Request $request, Response  $response)
    {
            if(isset($_POST['search']) && isset($_SESSION['session_login']))
            {
                $search = htmlentities($_POST['search']);
                $listSearch = $this->user->searchUser($search, $_SESSION['session_login']);
                return $this->container->get('view')->render($response, 'users/listUsers.twig', ['listSearch' => $listSearch]);

            }
            else if(!(isset($_SESSION['session_login'])))
            {
                $search = htmlentities($_POST['search']);
                $listSearch = $this->user->searchAllUsers($search);
                return $this->container->get('view')->render($response, 'users/listUsers.twig', ['listSearch' => $listSearch]);
            }

        exit;
    }

    public function listInfectedByLocation(Request $request, Response  $response)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $locations = $this->user->allUsersByLocation();
            return $this->container->get('view')->render($response, 'infected/mapInfected.twig', ['locations' => $locations]);
        }
        else
        {
            return $response->withHeader('Location', '/');
        }
    }

}


