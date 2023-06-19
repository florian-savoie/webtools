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

        $builder = $this->db->table('categories');
        $query = $builder->get();
        $categories = $query->getResultArray();

        $builder = $this->db->table('subcategories');
        $query = $builder->get();
        $souscategories = $query->getResultArray();

        if (isset($_GET['category']) && isset($_GET['souscat'])) {
            $category_id = (int)$_GET['category'];
            $subcategory_id = (int)$_GET['souscat'];

            $builder = $this->db->table('articles');
            $builder->select('articles.*, categories.name as category_name, categories.color as category_color');
            $builder->join('categories', 'categories.id = articles.category_id', 'left');
            $builder->where('category_id', $category_id);
            $builder->where('subcategory_id', $subcategory_id);
            $builder->orderBy('id', 'DESC');
            $query = $builder->get();
            $articles = $query->getResultArray();

        }else if (isset($_GET['category']) ){
            $category_id = (int)$_GET['category'];

            $builder = $this->db->table('articles');
            $builder->select('articles.*, categories.name as category_name, categories.color as category_color');
            $builder->join('categories', 'categories.id = articles.category_id', 'left');
            $builder->where('is_approved', 1);
            $builder->where('category_id', $category_id);
            $builder->orderBy('id', 'DESC');
            $query = $builder->get();
            $articles = $query->getResultArray();
        }else{
            if (empty($articles)) { // Ajout de la vérification si $articles est vide
                $builder = $this->db->table('articles');
                $builder->select('articles.*, categories.name as category_name, categories.color as category_color,subcategories.name as subcategories_name');
                $builder->join('categories', 'categories.id = articles.category_id', 'left');
                $builder->join('subcategories', 'subcategories.id = articles.subcategory_id', 'left');
                $builder->where('articles.is_approved', 1);
                $builder->orderBy('articles.id', 'DESC');
                $query = $builder->get();
                $articles = $query->getResultArray();
            }
        }
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);
        return $this->twig->render('showarticles.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue ,
            'articles' => $articles,
            'likes' => $likes,
            'votes' => $votes,
            'categories' => $categories,
            'souscategories' => $souscategories,
            'session' => $_SESSION,
            'propagande' => $existingData['Propagande']


        ]);
    }

    public function showarticleadmin()
    {
        $sessionExistsAndTrue = false;

        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ( $autoriser === true && $_SESSION['role'] == 'admin') {
            $sessionExistsAndTrue = true;
        }else {
            header("Location: /login");
            exit(0);
        }

        $idUser = $_SESSION['iduser'] ;


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

        $builder = $this->db->table('categories');
        $query = $builder->get();
        $categories = $query->getResultArray();

        $builder = $this->db->table('subcategories');
        $query = $builder->get();
        $souscategories = $query->getResultArray();

        if(isset($_GET['category']) && isset($_GET['souscat'])){
            $builder = $this->db->table('articles');
            $builder->where('is_approved' , 1);
            $builder->where('category_id' , $_GET['category']);
            $builder->where('subcategory_id' , $_GET['souscat']);
            $builder->orderBy('id', 'DESC');
            $query = $builder->get();
            $articles = $query->getResultArray();

        }else if (isset($_GET['category']) ){
            $builder = $this->db->table('articles');
            $builder->where('is_approved' , 1);
            $builder->where('category_id' , $_GET['category']);
            $builder->orderBy('id', 'DESC');
            $query = $builder->get();
            $articles = $query->getResultArray();
        }else{
            if (empty($articles)) { // Ajout de la vérification si $articles est vide
                $builder = $this->db->table('articles');
                $builder->where('is_approved', 1);
                $builder->orderBy('id', 'DESC');
                $query = $builder->get();
                $articles = $query->getResultArray();
            }
        }
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);

        if(isset($_POST['deleteArticle'])){
            $builder = $this->db->table('articles');
            $builder->where('id', $_POST['idDeleteArticle']);
            $builder->delete();
            header("Location: /showarticleadmin");
            exit(0);
        }
        return $this->twig->render('showarticlesadmin.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue ,
            'articles' => $articles,
            'likes' => $likes,
            'votes' => $votes,
            'categories' => $categories,
            'souscategories' => $souscategories,
            'session' => $_SESSION,
            'propagande' => $existingData['Propagande']

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