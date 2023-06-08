<?php

namespace App\Controllers;
use Config\Database;


class listController extends BaseController
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

    public function list()
    {
        $sessionExistsAndTrue = false;

        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ( $autoriser === true) {
            $sessionExistsAndTrue = true;
        }

        $builder = $this->db->table('articles');
        $builder->where('is_approved', 1);
        $builder->orderBy('id', 'DESC');
        $query = $builder->get();
        $articles = $query->getResultArray();
        $dir ="assets/json/articles.json";


        return $this->twig->render('list.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'session' => $_SESSION
        ]);
    }

    public function showlist()
    {
        $sessionExistsAndTrue = false;

        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ( $autoriser === true) {
            $sessionExistsAndTrue = true;
        }

        $builder = $this->db->table('articles');
        $builder->where('is_approved', 1);
        $builder->orderBy('id', 'DESC');
        $query = $builder->get();
        $articles = $query->getResultArray();
        // Renvoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($articles);
        return;


    }

}
