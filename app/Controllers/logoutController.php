<?php

namespace App\Controllers;
use Config\Database;


class logoutController extends BaseController
{

    /**
     * @var \Twig\Environment
     */
    protected $twig;



    /**
     * @var \CodeIgniter\Session\Session
     */
    protected $session;

    /**
     * @var \CodeIgniter\Database\BaseConnection
     */
    protected $db;

    public function __construct()
    {
        $appPaths = new \Config\Paths();
        $appViewPaths = $appPaths->viewDirectory;
        $loader = new \Twig\Loader\FilesystemLoader($appViewPaths);
        $this->twig = new \Twig\Environment($loader, [
            'cache' => false,
        ]);
        $this->session = \Config\Services::session();

    }

    public function logout()
    {
        // Détruire la session existante
        session_start();
        session_destroy();

        // Rediriger vers la page de connexion ou une autre page appropriée
        header("Location: /");
        exit();
    }
}
