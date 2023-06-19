<?php

namespace App\Controllers;
use Config\Database;


class chatController extends BaseController
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

    public function chat()
    {
        $sessionExistsAndTrue = false;

        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ( $autoriser === true) {
            $sessionExistsAndTrue = true;
        }
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);
        $vuechat = true;

        return $this->twig->render('chat.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'session' => $_SESSION,
            'propagande' => $existingData['Propagande'],
            'vuechat' => $vuechat

        ]);
    }    public function addchat()
{
    if (isset($_POST['action']) && $_POST['action'] === 'add-chat') {
        $message = $_POST['message'];

        $builder = $this->db->table('chat');
        $insertok = $builder->insert([
            'message' => $message,
            'id_user' => $_SESSION['iduser']
        ]);


        if ($this->db->error()) {
            $error = $this->db->error();
            $response = "Erreur  fds lors de l'ajout' : " . $error['message'];
        } else {
            $response = "ajout du message effectuée avec succès.";
        }
    } else {
        $response = "erreur lors de l'ajout.";

    }
    // Renvoyer la réponse au format JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    return;
}
    public function getmessages()
    {

        $builder = $this->db->table('chat');
        $builder->orderBy('id', 'asc');
        $chats = $builder->get()->getResultArray();


        // Renvoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($chats);

        return;
    }
    public function getSessionData()
    {
        header('Content-Type: application/json');
        echo json_encode($_SESSION);
        return;
    }
}