<?php
namespace App\Controllers;
/* include '/home/webtools/app/Libraries/Objettest.php';*/

use Config\Database;
use Libraries\Objettest;
use Config\App;

class loginController extends BaseController
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

    public function login()
    {
      /*  Objettest::test();*/
/*  echo App::TEST ; 
die();  */
if (!empty($_SESSION['role'])) {
    header("Location: ./");
    exit(0);
}

$message = "";
        if (isset($_POST['login'])) {
            $email = $_POST['email'];

            $builder = $this->db->table('users');
            $query = $builder->getWhere(['email' => $email], 1);
            $users = $query->getResultArray();
            if (isset($users[0])
                && isset($users[0]['email'])
                && $users[0]['email'] == $email) {
                if ($users[0]['password'] == sha1($_POST['password'])) {

                    $this->session->set([
                        'Autoriser' => true,
                        'iduser' => $users[0]['id'],
                        'role' => $users[0]['role'],
                        'pseudo' => $users[0]['pseudo'],
                        'profile_image' => $users[0]['profile_image']
                    ]);
                    $this->session->setFlashData('message', "success");

                    // Rediriger vers la page de connexion ou une autre page appropriée
                    header("Location: ./");
                    exit();

                } else {
                    $message = "Identifiants invalides";
                }
            } else {
                $message = "Identifiants invalides";
            }
        }


        return $this->twig->render('login.html.twig', [
            "message" => $message
        ]);
    }

}
