<?php

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Views\TwigMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Csrf\Guard;


require '../vendor/autoload.php';

session_start();

//Création de Container
$containerBuilder = new ContainerBuilder();

$settings = require __DIR__.'/../config/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__.'/../config/dependencies.php';
$dependencies($containerBuilder);

$container = $containerBuilder->build();

//Set view en Container
$container->set('view', function () {
    return \Slim\Views\Twig::create('../src/Vues', ['cache' => false]);
});


//Création de "App"
AppFactory::setContainer($container);
$app = AppFactory::create();
$responseFactory = $app->getResponseFactory();


// Register Middleware On Container
$container->set('csrf', function () use ($responseFactory) {
    return new Guard($responseFactory);
});

//$app->add('csrf');

// Ajout "Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app));


// Ajout des routes
$app->get('/myprofile/{username}', \App\Controllers\UserController::class . ':myprofile')->setName('myprofile');

$app->get('/', \App\Controllers\UserController::class . ':signIn')->setName('signin');
$app->post('/', \App\Controllers\UserController::class . ':authenticate')->setName('authenticate');

$app->get('/signUp', \App\Controllers\UserController::class . ':signUp')->setName('signup');
$app->post('/signUp', \App\Controllers\UserController::class . ':addUser')->setName('adduser');

$app->get('/signOut', \App\Controllers\UserController::class . ':signOut')->setName('signout');

$app->get('/change_password', \App\Controllers\UserController::class . ':formUpdatePwd')->setName('form_pwd');
$app->post('/change_password', \App\Controllers\UserController::class . ':updatePwd')->setName('change_password');

$app->get('/delete', \App\Controllers\UserController::class . ':deleteUser');
$app->delete('/delete', \App\Controllers\UserController::class . ':deleteUser')->setName('deleteuser');

$app->get('/positiveCovid', \App\Controllers\UserController::class . ':formPositive')->setName('formPositive');
$app->post('/positiveCovid', \App\Controllers\UserController::class . ':reportPositive')->setName('reportPositive');

$app->get('/myContact', \App\Controllers\ContactController::class . ':myContact')->setName('myContact');
$app->post('/myContact', \App\Controllers\ContactController::class . ':addToContact')->setName('addToContact');

$app->get('/myContact/messenger/{friend}', \App\Controllers\MessengerController::class . ':createChatContact')->setName('createChatContact');
$app->post('/myContact/messenger', \App\Controllers\MessengerController::class . ':addChatContact')->setName('addChatContact');

$app->get('/groups/messenger/{name}', \App\Controllers\MessengerController::class . ':createChatGroup')->setName('createChatGroup');
$app->post('/groups/messenger', \App\Controllers\MessengerController::class . ':addChatGroup')->setName('addChatGroup');

$app->get('/myGroup', \App\Controllers\GroupController::class . ':myGroup')->setName('myGroup');

$app->get('/listUsers', \App\Controllers\UserController::class . ':listUsers')->setName('listUsers');
$app->post('/listUsers', \App\Controllers\UserController::class . ':searchUser')->setName('searchUser');

$app->get('/listPosts/{group}', \App\Controllers\BoardController::class . ':listPosts')->setName('listPosts');
$app->post('/listPosts', \App\Controllers\BoardController::class . ':searchPost')->setName('searchPost');

$app->get('/listGroups', \App\Controllers\GroupController::class . ':listGroups')->setName('listGroups');
$app->post('/listGroups', \App\Controllers\GroupController::class . ':searchGroup')->setName('searchGroup');

$app->get('/joinGroup', \App\Controllers\GroupController::class . ':joinGroup');
$app->post('/joinGroup', \App\Controllers\GroupController::class . ':joinGroup')->setName('joinGroup');

$app->get('/createGroup', \App\Controllers\GroupController::class . ':createGroup')->setName('createGroup');
$app->post('/createGroup', \App\Controllers\GroupController::class . ':addGroup')->setName('addGroup');

$app->get('/groups/{group}/createPost', \App\Controllers\BoardController::class . ':createPost')->setName('createPost');
$app->post('/createPost', \App\Controllers\BoardController::class . ':addPost')->setName('addPost');

$app->get('/profiles/{username}', \App\Controllers\ProfileController::class . ':profiles')->setName('profiles');
$app->get('/groups/{name}', \App\Controllers\GroupController::class . ':groups')->setName('groups');
$app->get('/posts/{group}/{title}', \App\Controllers\BoardController::class . ':posts')->setName('posts');

$app->get('/myContact/profile/{username}', \App\Controllers\ProfileController::class . ':contactProfiles')->setName('contactProfiles');

$app->get('/deleteContact', \App\Controllers\ContactController::class . ':deleteContact');
$app->delete('/deleteContact', \App\Controllers\ContactController::class . ':deleteContact')->setName('deleteContact');

$app->get('/quitGroup', \App\Controllers\GroupController::class . ':quitGroup');
$app->delete('/quitGroup', \App\Controllers\GroupController::class . ':quitGroup')->setName('quitGroup');

$app->get('/deletePost', \App\Controllers\BoardController::class . ':deletePost');
$app->delete('/deletePost', \App\Controllers\BoardController::class . ':deletePost')->setName('deletePost');

$app->get('/addLocation', \App\Controllers\UserController::class . ':addLocation');
$app->post('/addLocation', \App\Controllers\UserController::class . ':addLocation')->setName('addLocation');

$app->get('/infectedMap', \App\Controllers\UserController::class . ':listInfectedByLocation')->setName('listUsersByLocation');

$app->run();




