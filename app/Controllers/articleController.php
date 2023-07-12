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
                $idcategory = $this->request->getPost('idCategory');
                $builder = $this->db->table('subcategories');
                $query = $builder->getWhere(['category_id' => $idcategory]);
                $souscategorys = $query->getResultArray();
                return $this->response->setJSON($souscategorys);
            }
        }
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);




        return $this->twig->render('addarticle.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'session' => $_SESSION,
            'categorys' => $categorys,
            'propagande' => $existingData['Propagande'],
            'messageFlash' =>  $this->session->getFlashData('message'),


        ]);
    }

    function addarticleajax(){
        if (isset($_POST['title'])) {
            $title = $_POST['title'];
            $url = $_POST['url'];
            $content = $_POST['description'];
            $category_id = $_POST['category'];
            $souscat = $_POST['souscat'];

            // Vérifier si les champs ont été remplis
            if ($title && $url && $content && $category_id) {

                // Récupérer les données de l'image compressée de FormData
                $compressedImage = $_POST['image'];

                // Générer un nom de fichier unique pour chaque image
                $fileName = uniqid('') . '.jpg';

                // Convertir les données Base64 en binaire
                $imageData = base64_decode(str_replace('data:image/jpeg;base64,', '', $compressedImage));

                // Chemin de destination pour enregistrer l'image
                $destinationPath ='assets/imgArticle/' . $fileName;

                // Enregistrer l'image sur le serveur
                if (file_put_contents($destinationPath, $imageData)) {
                    // L'image a été enregistrée avec succès
                    $this->session->setFlashData('message', "success");
                } else {
                    // Une erreur s'est produite lors de l'enregistrement de l'image
                    $this->session->setFlashData('message', "success");
                }

                // Insérer les données de l'article dans la base de données
                $builder = $this->db->table('articles');
                $insertok = $builder->insert([
                    'user_id' => $_SESSION['iduser'],
                    'category_id' => $category_id,
                    'subcategory_id' => $souscat,
                    "title" => $title,
                    "content" => $content,
                    "image" => $destinationPath,
                    "link" => $url,
                ]);

                if ($insertok) {
                    $response = array(
                        'success' => true,
                        'message' => 'La réponse a été renvoyée avec succès.'
                    );

                    header('Content-Type: application/json');
                    echo json_encode($response);                } else {
                    $this->session->setFlashData('message', "success");         }
            } else {
                $this->session->setFlashData('message', "success");         }

            // Envoyer une réponse à la demande AJAX
            $this->session->setFlashData('message', "success");
        }
    }


}
