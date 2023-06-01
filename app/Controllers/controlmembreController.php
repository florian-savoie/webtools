<?php

namespace App\Controllers;
use Config\Database;


class controlmembreController extends BaseController
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

        $autoriser = $_SESSION['Autoriser'];
        $role = $_SESSION['role'];

        // Vérifier si la session existe et est vraie
        if ( $autoriser === true && $role === "admin") {
            $sessionExistsAndTrue = true;
        }else {
            header("Location: /showarticle");
            exit(0);
        }

        $idUser = $_SESSION['iduser'] ;
        $builder = $this->db->table('users');
        $query = $builder->get();
        $users = $query->getResultArray();




        return $this->twig->render('listemenbers.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue ,
            'users' => $users,
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

    public function updaterole()
{
    if (isset($_POST['idmember'])) {
        $idmember = $_POST['idmember'];
        $role = $_POST['role'];

        $data = [
            'role' => $role
        ];
        $updated = $this->db->table('users')->update($data, ['id' => $idmember]);

        if ($this->db->error()) {
            $error = $this->db->error();
            $response = "Erreur lors de la mise à jour : " . $error['message'];
        } else {
            $response = "Mise à jour effectuée avec succès.";
        }
    } else {
        $response = "Erreur lors de la mise à jour.";
    }

    // Renvoyer la réponse au format JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    return;
}    public function deleteuser()
{
    if (isset($_POST['idmember'])) {
        $idmember = $_POST['idmember'];

        $builder = $this->db->table('users');
        $builder->where('id', $idmember);
        $builder->delete();

        if ($this->db->error()) {
            $error = $this->db->error();
            $response = "Erreur lors de la mise à jour : " . $error['message'];
        } else {
            $response = "Mise à jour effectuée avec succès.";
        }
    } else {
        $response = "Erreur lors de la mise à jour.";
    }

    // Renvoyer la réponse au format JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    return;
}
}