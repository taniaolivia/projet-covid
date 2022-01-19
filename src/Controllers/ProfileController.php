<?php

namespace App\Controllers;

use App\Models\Profile;
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

class ProfileController
{
    private $container;
    public $profile;
    public $user;

    public function __construct(ContainerInterface $container, Profile $profile, User $user)
    {
        $this->container = $container;
        $this->profile = $profile;
        $this->user = $user;
    }


    public function profiles(Request $request, Response  $response, array $args)
    {
        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $_SESSION['token'] = ['nameKey' => $nameKey, 'valueKey' => $valueKey, 'name' => $name, 'value' => $value];

        $token = $_SESSION['token'];
        $username = $args['username'];
        $uname =$this->profile->profileByUsername($username);
        $info = $this->user->allUsersByUsername($username);
        $location = $this->user->listLocationByUsername($username);
        var_dump($location);
        return $this->container->get('view')->render($response, 'profiles/profiles.twig', ["location" => $location, "uname" => $uname, "info" => $info, 'token' => $token]);
    }

    public function contactProfiles(Request $request, Response  $response, array $args)
    {
        $username = $args['username'];
        $uname =$this->profile->profileByUsername($username);
        $info = $this->user->allUsersByUsername($username);

        return $this->container->get('view')->render($response, 'profiles/contactProfiles.twig', ["uname" => $uname, "info" => $info]);
    }

}


