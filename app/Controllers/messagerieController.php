<?php

namespace App\Controllers;
use Config\Database;


class messagerieController extends BaseController
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

    public function messagerie()
    {
        $sessionExistsAndTrue = false;

        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ( $autoriser === true) {
            $sessionExistsAndTrue = true;
        }
        $builder = $this->db->table('messages_prives');
        $messagerecu = $builder->getWhere(['destinataire' => $_SESSION['iduser'], 'hidden' => 0])->getResult();

        $builder = $this->db->table('messages_prives');
        $messageenvoyer = $builder->getWhere(['emetteur' => $_SESSION['iduser']])->getResult();

        if (isset($_POST['sendMessage'])){
            var_dump($_POST);
            die();
        }

        return $this->twig->render('messagerie.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'session' => $_SESSION,
            'messagerecu' => $messagerecu,
            'messageenvoyer' => $messageenvoyer
        ]);
    }
    public function addfavorite()
    {
        $data = ['favorite' => $_POST['addfavorite']];
        $updated = $this->db->table('messages_prives')->where('id', $_POST['idmail'])->update($data);

        if ($updated) {
            $response = ['status' => 'success', 'message' => 'Favori ajouté avec succès.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Impossible d\'ajouter le favori.'];
        }

        return response()->json($response);
    }

    public function deletefavorite()
    {
        $data = ['favorite' => $_POST['deletefavorite']];
        $updated = $this->db->table('messages_prives')->where('id', $_POST['idmail'])->update($data);

        if ($updated) {
            $response = ['status' => 'success', 'message' => 'Favori supprimé avec succès.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Impossible de supprimer le favori.'];
        }

        return response()->json($response);
    }

    public function deletemsg()
    {
        $data = ['hidden' => 1 ];
        $updated = $this->db->table('messages_prives')->where('id', $_POST['idmessage'])->update($data);

        if ($updated) {
            $response = ['status' => 'success', 'message' => 'message supprimé avec succès.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Impossible de supprimer le message.'];
        }

        return response()->json($response);

    }    public function readok()
    {
        $data = ['vue' => 1 ];
        $updated = $this->db->table('messages_prives')->where('id', $_POST['idmessage'])->update($data);

        if ($updated) {
            $response = ['status' => 'success', 'message' => 'message supprimé avec succès.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Impossible de supprimer le message.'];
        }

        return response()->json($response);
    }
}
