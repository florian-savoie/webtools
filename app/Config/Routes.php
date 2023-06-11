<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/aceuil', 'HomeController::Home');
$routes->get('/login', 'loginController::login');
$routes->post('/login', 'loginController::login');
$routes->get('/register', 'registerController::register');
$routes->post('/register', 'registerController::register');
$routes->get('/logout', 'logoutController::logout');
$routes->post('/moncompte', 'moncompteController::moncompte');
$routes->get('/moncompte', 'moncompteController::moncompte');
$routes->get('/showcategory', 'showcategoryController::category');
$routes->post('/showcategory', 'showcategoryController::category');
$routes->post('/delete-category', 'showcategoryController::deletecategory');
$routes->post('/delete-souscategory', 'showcategoryController::deletesouscategory');
$routes->post('/add-category', 'showcategoryController::addcategory');
$routes->post('/update-category', 'showcategoryController::updatecategory');
$routes->post('/add-souscategory', 'showcategoryController::addsouscategory');
$routes->post('/update-souscategory', 'showcategoryController::updatesouscategory');
$routes->get('/addarticle', 'articleController::addarticle');
$routes->post('/addarticle', 'articleController::addarticle');
$routes->post('/show-souscategory', 'articleController::addarticle');
$routes->get('/showarticle', 'showarticlesController::showarticle');
$routes->post('/votestar', 'showarticlesController::votestar');
$routes->post('/addfavorite', 'showarticlesController::addfavorite');
$routes->post('/deletefavorite', 'showarticlesController::deletefavorite');
$routes->post('/validatearticle', 'validatearticleController::validatearticle');
$routes->get('/validatearticle', 'validatearticleController::home');
$routes->get('/showmembres', 'controlmembreController::home');
$routes->post('/showmembres', 'controlmembreController::updatemembres');
$routes->post('/updaterole', 'controlmembreController::updaterole');
$routes->post('/deleteuser', 'controlmembreController::deleteuser');
$routes->post('/chat', 'chatController::chat');
$routes->get('/chat', 'chatController::chat');
$routes->post('/addchat', 'chatController::addchat');
$routes->get('/get-messages', 'chatController::getmessages');
$routes->get('/getSessionData', 'chatController::getSessionData');
$routes->get('/list', 'listController::list');
$routes->get('/showlist', 'listController::showlist');
$routes->get('/messagerie', 'messagerieController::messagerie');
$routes->post('/messagerie', 'messagerieController::messagerie');
$routes->post('/messagerieaddfavorite', 'messagerieController::addfavorite');
$routes->post('/messageriedeletevaforite', 'messagerieController::deletefavorite');



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
