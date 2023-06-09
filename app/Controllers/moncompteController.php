<?php

namespace App\Controllers;
use Config\Database;


class moncompteController extends BaseController
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

    public function moncompte()
    {
        $sessionExistsAndTrue = false;


        // Vérifier si la session existe et est vraie
        if (!empty($_SESSION['role'])) {
            $sessionExistsAndTrue = true;
        }else {
            header("Location: ./");
            exit(0);
        }

        $builder = $this->db->table('users');
        $query = $builder->getWhere(['id' => $_SESSION['iduser']], 1);
        $user = $query->getResultArray();

        if(isset($_POST['postPhotoProfil'])){
            $uploadedFile = $_FILES['PhotoProfil']; // remplacez "name" par le nom de votre input file
            $targetDir = "Data/img_profil_users/"; // dossier cible pour enregistrer le fichier
            $targetFile = $targetDir .$_SESSION['iduser'].".jpg"; // chemin absolu du fichier cible

// déplacer le fichier temporaire vers le dossier cible
            if (move_uploaded_file($uploadedFile["tmp_name"], $targetFile)) {
                echo "Le fichier " . htmlspecialchars(basename($uploadedFile["name"])) . " a été téléchargé avec succès.";
                echo  $targetFile ;
                $data = ['profile_image' => $_SESSION['iduser'].".jpg"

                ];
                $updated = $this->db->table('users')->update($data, ['id' => $_SESSION['iduser']]);

                header('Location: ./moncompte');
                exit(0);
            } else {
                echo "Désolé, une erreur s'est produite lors de l'envoi de votre fichier.";
            }

        }
        $hiddenchat = true ;
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);

        return $this->twig->render('moncompte.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue ,
            'user' => $user,
            'session' => $_SESSION,
            'propagande' => $existingData['Propagande']


        ]);
    }

}