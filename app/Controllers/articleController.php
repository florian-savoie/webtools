<?php

namespace App\Controllers;
use Config\Database;


class articleController extends BaseController
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

    public function addarticle()
    {
        $sessionExistsAndTrue = false;

        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ($autoriser === true) {
            $sessionExistsAndTrue = true;
        }

        $builder = $this->db->table('categories');
        $categorys = $builder->get()->getResultArray();

        if ($this->request->isAJAX()) {
            if ($this->request->getPost('action') === 'show-souscategory') {
                $idcategory = $this->request->getPost('idcategory');
                $builder = $this->db->table('subcategories');
                $query = $builder->getWhere(['category_id' => $idcategory]);
                $souscategorys = $query->getResultArray();
                return $this->response->setJSON($souscategorys);
            }
        }

        return $this->twig->render('addarticle.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'session' => $_SESSION,
            'categorys' => $categorys,
        ]);
    }

}
