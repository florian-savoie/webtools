<?php

namespace App\Controllers;
use Config\Database;


class HomeController extends BaseController
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
        $this->db = \Config\Database::connect();

        // Vérifier la connexion à la base de données
        try {
            $this->db->initialize();
            $this->db->connect();
        } catch (\Exception $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    public function Home()
    {
        $sessionExistsAndTrue = false;
        $vuechat = true;

        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ( $autoriser === true) {
            $sessionExistsAndTrue = true;
        }
        if ( !empty($_SESSION['role']) ) {
            $sessionExistsAndTrue = true;
            $vuechat = false;

        }
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);
        return $this->twig->render('aceuil.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'session' => $_SESSION,
            'propagande' => $existingData['Propagande'],
             'messageFlash' =>  $this->session->getFlashData('message'),
             'vuechat' => $vuechat


        ]);
    }

}
