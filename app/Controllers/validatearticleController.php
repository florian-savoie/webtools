<?php

namespace App\Controllers;
use Config\Database;


class validatearticleController extends BaseController
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

    public function home()
    {
        $sessionExistsAndTrue = false;

        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ( $autoriser === true) {
            $sessionExistsAndTrue = true;
        }else {
            header("Location: /login");
            exit(0);
        }

        $idUser = $_SESSION['iduser'] ;
        $builder = $this->db->table('articles');
        $builder->where('is_approved' , 0);
        $builder->orderBy('id', 'DESC');
        $query = $builder->get();
        $articles = $query->getResultArray();




        return $this->twig->render('validatearticle.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue ,
            'articles' => $articles,
            'session' => $_SESSION

        ]);
    }


    public function validatearticle()
    {
        if (isset($_POST['idarticle'])) {
            $idarticle = $_POST['idarticle'];
            $valeur = $_POST['hidden'];

            $data = ['is_approved' => $valeur
            ];
            $updated = $this->db->table('articles')->update($data, ['id' => $idarticle ]);

            if ($this->db->error()) {
                $error = $this->db->error();
                $response = "Erreur lors de la mise à jour : " . $error['message'];
            } else {
                $response = "note effectuée avec succès.";
            }
        } else {
            $response = "Erreur lors de la note.";
        }

        // Renvoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }
}