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
        // Vérifier si la session existe et est vraie
        if (!empty($_SESSION['role'])) {
            $sessionExistsAndTrue = true;
        }else {
            header("Location: ./showarticle");
            exit(0);
        }

        $builder = $this->db->table('messages_prives');
        $messagerecu = $builder->where('destinataire', $_SESSION['iduser'])
            ->where('hidden', 0)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResult();

        $builder = $this->db->table('messages_prives');
        $messageenvoyer = $builder->where('emetteur', $_SESSION['iduser'])
            ->orderBy('id', 'DESC')
            ->get()
            ->getResult();

        $message = "";

        if(isset($_POST['emaildelete'])){

            $data = ['hidden' => 1];
            $updated = $this->db->table('messages_prives')->whereIn('id', $_POST['emaildelete'])->update($data);
            header("Location: /messagerie");
            exit(0);

        }


        if (isset($_POST['sendMessage'])) {
            if (isset($_POST['destinataire']) && isset($_POST['sujet']) && isset($_POST['contenu'])) {

                $builder = $this->db->table('users');
                $destinataireIsset = $builder->getWhere(['pseudo' => $_POST['destinataire']])->getResult();

                if ($destinataireIsset && $_SESSION['pseudo'] != $_POST['destinataire']) {
                    $data = [
                        'pseudo_emetteur' => $_SESSION['pseudo'],
                        'pseudo_destinataire' => $_POST['destinataire'],
                        'destinataire' => $destinataireIsset[0]->id,
                        'emetteur' => $_SESSION['iduser'],
                        'contenu' => $_POST['contenu'],
                        'sujet' => $_POST['sujet'],
                    ];
                    $builder = $this->db->table('messages_prives');
                    $insert = $builder->insert($data);
                    $this->session->setFlashData('message', "success");
                    header("Location: ./messagerie");
                    exit();
                } else {
                    $this->session->setFlashData('message', "error");
                }

            } else {
                $this->session->setFlashData('message', "error");
            }
        }


        if (isset($_POST['replysend'])) {

            $data = [
                'pseudo_emetteur' => $_SESSION['pseudo'],
                'pseudo_destinataire' => $_POST['emetteur'],
                'destinataire' => $_POST['idemetteur'],
                'emetteur' => $_SESSION['iduser'],
                'contenu' => $_POST['contenu'],
                'sujet' => $_POST['sujet'],
            ];
            $builder = $this->db->table('messages_prives');
            $insert = $builder->insert($data);
            $this->session->setFlashData('message', "success");
            header("Location: ./messagerie");
            exit();

        }
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);

        $message = $this->session->getFlashData('message');

        return $this->twig->render('messagerie.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'session' => $_SESSION,
            'messagerecu' => $messagerecu,
            'messageenvoyer' => $messageenvoyer,
            'messageFlash' => $this->session->getFlashData('message'),
            'propagande' => $existingData['Propagande']
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
        $data = ['hidden' => 1];
        $updated = $this->db->table('messages_prives')->where('id', $_POST['idmessage'])->update($data);

        if ($updated) {
            $response = ['status' => 'success', 'message' => 'message supprimé avec succès.'];
            $this->session->setFlashData('message', "success");

        } else {
            $response = ['status' => 'error', 'message' => 'Impossible de supprimer le message.'];
            $this->session->setFlashData('message', "error");

        }

        return response()->json($response);

    }

    public function readok()
    {
        $data = ['vue' => 1];
        $updated = $this->db->table('messages_prives')->where('id', $_POST['idmessage'])->update($data);

        if ($updated) {
            $response = ['status' => 'success', 'message' => 'message supprimé avec succès.'];

        } else {
            $response = ['status' => 'error', 'message' => 'Impossible de supprimer le message.'];

        }

        return response()->json($response);
    }


}
