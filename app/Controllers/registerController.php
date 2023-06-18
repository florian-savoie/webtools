<?php

namespace App\Controllers;
use Config\Database;


class registerController extends BaseController
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

    public function register()
    {
        $message = "";
        if (isset($_POST['register'])) {
            if (
                isset($_POST['email']) && !empty($_POST['email']) &&
                isset($_POST['pseudo']) && !empty($_POST['pseudo']) &&
                isset($_POST['password']) && !empty($_POST['password']) &&
                isset($_POST['password2']) && !empty($_POST['password2'])
            ) {
                if ($_POST['password'] === $_POST['password2']) {

                    // Vérifier si le pseudo ou le mail est déjà utilisé
                    $builder = $this->db->table('users');
                    $builder->select('*');
                    $builder->where('pseudo', $_POST['pseudo']); // Remplacez $_POST['pseudo'] par la variable qui contient le pseudo soumis par le formulaire.
                    $builder->orWhere('email', $_POST['email']); // Remplacez $_POST['email'] par la variable qui contient l'adresse e-mail soumise par le formulaire.
                    $query = $builder->get();

                    if ($query->getNumRows() > 0) {
                        $message = "Email ou mot de passe deja utilisé";
                    } else {


                    $data = [
                        'pseudo' => $_POST['pseudo'],
                        'email' => $_POST['email'],
                        'password' => sha1($_POST['password']),
                    ];


                    $builder = $this->db->table('users');
                    $insert = $builder->insert($data);
                    $message = "Enregistrement réussi.";

// Ajouter membre a la liste des recherches rapide
                        $listeMembres = file_get_contents("assets/json/menbres/listemenbres.json");
                        $nouveauxMembres = json_decode($listeMembres, true);

                        array_push($nouveauxMembres, $_POST["pseudo"]);

                        file_put_contents("assets/json/menbres/listemenbres.json", json_encode($nouveauxMembres));

                        // Rediriger vers la page de connexion ou une autre page appropriée
                    header("Location: /login");
                    exit(); }
                } else {
                    $message = "Les mots de passe ne correspondent pas.";
                }
            } else {
                $message = "Veuillez remplir tous les champs.";
            }
        }
        return $this->twig->render('register.html.twig', [
            "message" => $message
        ]);
    }

}
