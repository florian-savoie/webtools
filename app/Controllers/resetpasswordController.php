<?php

namespace App\Controllers;

use Config\Database;


class resetpasswordController extends BaseController
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

    public function resetpassword()
    {
        if (!empty($_SESSION['role'])) {
            header("Location: ./");
            exit(0);
        }
        $message = "";
        if (!empty($_GET['token'])) {
            $builder = $this->db->table('forgotpassword');
            $builder->where(['token' => $_GET['token']]);
            $builder->where('time >=', date('Y-m-d H:i:s', strtotime('-30 minutes')));

            $query = $builder->get();
            $reset = $query->getResultArray();

            if (isset($reset[0])) {
                if (
                    isset($_POST['password']) && !empty($_POST['password']) &&
                    isset($_POST['password2']) && !empty($_POST['password2']) && $_POST['password'] === $_POST['password2']) {
                    $data = [
                        'password' =>sha1($_POST['password'])
                    ];
                    $updated = $this->db->table('users')->update($data, ['email' => $reset[0]['email']]);

                    $message = [0 => 'success', 1 => 'Mot de passe modification  avec succes .'];
                }

            } else {
                $message = [0 => 'danger', 1 => 'Une erreur s\'est produite. Veuillez réessayer.'];

            }
        }else{
            header("Location: ./");
            exit(0);
        }

        return $this->twig->render('reset-password.html.twig', [
            'message' => $message


        ]);
    }

}