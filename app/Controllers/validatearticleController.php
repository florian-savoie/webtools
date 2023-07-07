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

        // Vérifier si la session existe et est vraie
        if (!empty($_SESSION['role']) && $_SESSION['role'] == 'admin' || $_SESSION['role'] == 'moderateur') {
            $sessionExistsAndTrue = true;
        }else {
            header("Location: /showarticle");
            exit(0);
        }

        $idUser = $_SESSION['iduser'] ;
        $builder = $this->db->table('articles');
        $builder->select('articles.*, categories.name as category_name, categories.color as category_color,subcategories.name as subcategories_name');
        $builder->join('categories', 'categories.id = articles.category_id', 'left');
        $builder->join('subcategories', 'subcategories.id = articles.subcategory_id', 'left');
        $builder->where('is_approved' , 0);
        $builder->orderBy('id', 'DESC');
        $query = $builder->get();
        $articles = $query->getResultArray();


        $builder = $this->db->table('categories');
        $query = $builder->getWhere(['id' => 10]);
        $nbrarticle = $query->getResultArray();



        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);

        return $this->twig->render('validatearticle.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue ,
            'articles' => $articles,
            'session' => $_SESSION,
            'propagande' => $existingData['Propagande']


        ]);
    }


    public function validatearticle()
    {

        if(isset($_POST['deleteArticle'])){
            $builder = $this->db->table('articles');
            $builder->where('id', $_POST['idDeleteArticle']);
            $builder->delete();
            header("Location: /validatearticle");
            exit(0);
        }


        if (isset($_POST['idarticle'])) {
            $idarticle = $_POST['idarticle'];
            $valeur = $_POST['hidden'];

            $data = ['is_approved' => $valeur
            ];
            $updated = $this->db->table('articles')->update($data, ['id' => $idarticle ]);

            $builder = $this->db->table('articles');
            $query = $builder->getWhere(['id' => $idarticle]);
            $users = $query->getResultArray();
            $idmenber  = $users[0]['user_id'];

            $builder = $this->db->table('users');
            $query = $builder->getWhere(['id' => $idmenber]);
            $valuepublication = $query->getResultArray();

            $builder = $this->db->table('categories');
            $query = $builder->getWhere(['id' => $users[0]['category_id']]);
            $nbrarticle = $query->getResultArray();

            if ($valeur == 1){
                $data = ['article_count' => $valuepublication[0]['article_count']+1,
                    'propagande' => $valuepublication[0]['propagande']+1,
                ];
                $updatedstat = $this->db->table('users')->update($data, ['id' => $idmenber ]);


                $data = ['articles' => $nbrarticle[0]['articles']+1
                ];
                $updatedstat = $this->db->table('categories')->update($data, ['id' => $users[0]['category_id'] ]);


            }else {
                $data = ['article_count' => $valuepublication[0]['article_count']-1,
                    'propagande' => $valuepublication[0]['propagande']-1,
                ];
                $updatedstat = $this->db->table('users')->update($data, ['id' => $idmenber ]);


                $data = ['articles' => $nbrarticle[0]['articles']-1
                ];
                $updatedstat = $this->db->table('categories')->update($data, ['id' => $users[0]['category_id'] ]);
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
}