<?php

namespace App\Controllers;

use Config\Database;


class ShowcategoryController extends BaseController
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

    public function category()
    {
        $sessionExistsAndTrue = false;
        $role = $_SESSION["role"];
        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ($autoriser === true && $role === "admin" || $role === "moderateur") {
            $sessionExistsAndTrue = true;

            $builder = $this->db->table('categories');
            $categorys = $builder->get()->getResultArray();

            $builder = $this->db->table('subcategories');
            $souscategorys = $builder->get()->getResultArray();

            return $this->twig->render('showcategory.html.twig', [
                'sessionExistsAndTrue' => $sessionExistsAndTrue,
                'session' => $_SESSION,
                "categorys" => $categorys,
                "souscategorys" => $souscategorys


            ]);
        } else {
            header("Location: /aceuil");
            exit(0);

        }
    }

    public function addcategory()
    {
        if (isset($_POST['action']) && $_POST['action'] === 'add-category') {
            $namecategory = $_POST['namecategory'];
            $colortegory = $_POST['colortegory'];

// Vérifier si le nom de catégorie existe déjà
            $query = $this->db->query("SELECT * FROM categories WHERE name = ?", [$namecategory]);
            if (count($query->getResult()) > 0) {
                // Le nom de catégorie existe déjà, renvoyer une réponse d'erreur
                $response = "Le nom de catégorie existe déjà.";
            } else {
                // Le nom de catégorie est unique, insérer les informations de la nouvelle catégorie
                $builder = $this->db->table('categories');
                $insertok = $builder->insert([
                    'name' => $namecategory,
                    'color' => $colortegory
                ]);

                // Vérifier si l'insertion a réussi
                if ($this->db->error()) {
                    $error = $this->db->error();
                    $response = "Erreur lors de la mise à jour : " . $error['message'];
                } else {
                    $response = "Ajout effectué avec succès.";
                }
            }

// Renvoyer la réponse au format JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }
    public function deletecategory()
    {
        if (isset($_POST['action']) && $_POST['action'] === 'delete-category') {
            $id = $_POST['idligne'];

            $builder = $this->db->table('categories');
            $builder->where('id', $id);
            $builder->delete();

            $builder = $this->db->table('subcategories');
            $builder->where('category_id', $id);
            $builder->delete();


            if ($this->db->error()) {
                $error = $this->db->error();
                $response = "Erreur  fds lors de la mise à jour : " . $error['message'];
            } else {
                $response = "supression  effectuée avec succès.";
            }
        } else {
            $response = "erreur lors de la supresion.";

        }
        // Renvoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }

    public function deletesouscategory()
    {
        if (isset($_POST['action']) && $_POST['action'] === 'delete-souscategory') {
            $name = $_POST['name'];
            $idcategory = $_POST['idcategory'];

            $builder = $this->db->table('subcategories');
            $builder->where('name', $name);
            $builder->where('category_id', $idcategory);
            $builder->delete();


            if ($this->db->error()) {
                $error = $this->db->error();
                $response = "Erreur  fds lors de la mise à jour : " . $error['message'];
            } else {
                $response = "supression  effectuée avec succès.";
            }
        } else {
            $response = "erreur lors de la supresion.";

        }
        // Renvoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }


    public function updatecategory()
    {
        if (isset($_POST['action']) && $_POST['action'] === 'update-category') {
            $newnamecategory = $_POST['newname'];
            $idligne = $_POST['idligne'];
            $color = $_POST['color'];

            $data = ['name' => $newnamecategory,
                'color' => $color
                ];
            $updated = $this->db->table('categories')->update($data, ['id' => $idligne]);

            // Vérifier si la mise à jour a réussi
            if (!$updated) {
                $error = $this->db->error();
                $response = "Erreur lors de la mise à jour : " . $error['message'];
            } else {
                $response = "Mise à jour effectuée avec succès.";
            }

            // Renvoyer la réponse au format JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }

}


