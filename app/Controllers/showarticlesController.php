<?php

namespace App\Controllers;
use Config\Database;


class showarticlesController extends BaseController
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

    public function showarticle()
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
        $builder->where('is_approved' , 1);
        $builder->orderBy('id', 'DESC');
        $query = $builder->get();
        $articles = $query->getResultArray();

        $builder = $this->db->table('favorites');
        $builder->orderBy('id', 'DESC');
        $builder->where('user_id', $idUser); // Remplacez $id par l'ID que vous voulez rechercher
        $query = $builder->get();
        $likes = $query->getResultArray();

        $builder = $this->db->table('votes');
        $builder->orderBy('id', 'DESC');
        $builder->where('user_id', $idUser); // Remplacez $id par l'ID que vous voulez rechercher
        $query = $builder->get();
        $votes = $query->getResultArray();


        return $this->twig->render('showarticles.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue ,
            'articles' => $articles,
            'likes' => $likes,
            'votes' => $votes,
            'session' => $_SESSION

        ]);
    }

    public function votestar()
    {
        if (isset($_POST['note']) && isset($_POST['idarticle'])) {
            $note = $_POST['note'];
            $idarticle = $_POST['idarticle'];


            $builder = $this->db->table('votes');
            $builder->where('user_id', $_SESSION['iduser']);
            $builder->where('article_id', $idarticle);
            $query = $builder->get();
            $result = $query->getResultArray();

            if($result){
                $data = ['vote' => $note
                ];
                $updated = $this->db->table('votes')->update($data, ['article_id' => $idarticle, 'user_id' => $_SESSION['iduser'] ]);
            }else {
                $data = [
                    'article_id' => $idarticle,
                    'vote' => $note,
                    'user_id' => $_SESSION['iduser'],
                ];
                $builder = $this->db->table('votes');
                $insert = $builder->insert($data);
            }




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
    public function addfavorite()
    {
        if (isset($_POST['idarticle'])) {
            $idarticle = $_POST['idarticle'];

            $data = [
                'article_id' => $idarticle,
                'user_id' => $_SESSION['iduser'],
            ];
            $builder = $this->db->table('favorites');
            $insert = $builder->insert($data);

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
    public function deletefavorite()
    {
        if (isset($_POST['idarticle'])) {
            $idarticle = $_POST['idarticle'];

            $builder = $this->db->table('favorites');
            $builder->where('article_id' , $idarticle);
            $builder->where('user_id', $_SESSION['iduser']);
            $builder->delete();

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