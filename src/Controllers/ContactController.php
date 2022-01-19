<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Models\Messenger;
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

class ContactController
{
    private $container;
    public $contact;
    public $user;
    public $messenger;

    public function __construct(ContainerInterface $container, Contact $contact, User $user, Messenger $messenger)
    {
        $this->container = $container;
        $this->contact = $contact;
        $this->user = $user;
        $this->messenger = $messenger;
    }

    public function myContact(Request $request, Response  $response)
    {
        if (isset($_SESSION['session_login']))
        {
            $session = $_SESSION['session_login'];

            $list = $this->contact->allContactsOnlyMine($session);

            return $this->container->get('view')->render($response, 'contacts/mycontact.twig', ['list' => $list]);
        }
        else
        {
            return $response->withHeader('Location', '/');
        }
    }

    public function addToContact(Request $request, Response  $response)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if (isset($_POST['username']) && isset($_SESSION['session_login']))
            {
                $username = htmlentities($_POST['username']);
                $user_username = $_SESSION['session_login'];

                if($this->contact->allContactsByUsername($user_username, $username, $username))
                {
                    return $response->withHeader('Location', "/myContact");
                }
                else
                {
                    $this->contact->addToContact($username, $user_username, $username);
                    $this->contact->addToContact($user_username, $username, $user_username);

                    return $response->withHeader('Location', "/myContact");
                }
            }
            else
            {
                return $response->withHeader('Location', '/');
            }
        }
        exit;
    }

    public function deleteContact(Request $request, Response  $response)
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET")
        {
            if (isset($_SESSION['session_login']))
            {
                $username = htmlentities($_GET['deleteContact']);
                $session = $_SESSION['session_login'];
                $this->messenger->deleteChat($username, $session);
                $this->messenger->deleteChat($session, $username);
                $this->contact->deleteContact($username, $session);
                $this->contact->deleteContact($session, $username);
                $deleted = "This user has been deleted successfully from your contact!";

                return $this->container->get('view')->render($response, 'profiles/contactProfiles.twig', ['message' => $deleted]);
            }
            else
            {
                return $response->withHeader('Location', '/');
            }
        }
        exit;
    }

}


