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
        $response = "";
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


        if (isset($_POST['publication'])) {
            $title = $_POST['title'];
            $url = $_POST['url'];
            $content = $_POST['description'];
            $category_id = $_POST['category'];

            if (isset($_POST['souscat'])){
                $subcategory_id = $_POST['souscat'];
            }else{
                $subcategory_id = 0;

            }
            // Vérifier si les champs ont été remplis
                // Insérer les données dans la base de données
                // ...

                $builder = $this->db->table('articles');
                $insertok = $builder->insert([
                    'user_id' => $_SESSION['iduser'],
                    'category_id' => $category_id,
                    'subcategory_id' => $subcategory_id,
                    "title" => $title,
                    "content" => $content,
                    "image" => $url,
                    "link" => $url,
                ]);
            }     if ($this->db->error()) {
                $error = $this->db->error();
                $response = "Erreur lors de la mise à jour : " . $error['message'];
            } else {
                $response = "Suppression effectuée avec succès.";
            }
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);
        return $this->twig->render('addarticle.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'session' => $_SESSION,
            'categorys' => $categorys,
            "response" => $response ,
            'propagande' => $existingData['Propagande']

        ]);
    }


}
