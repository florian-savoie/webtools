<?php

namespace App\Controllers;

use Config\Database;

class propagandeController extends BaseController
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
    const PROPAGANDE_PATH = "assets/json/propagande/";

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

    public function propagande()
    {
        $sessionExistsAndTrue = false;

        // Vérifier si la session existe et est vraie
        if (!empty($_SESSION['role'])) {
            $sessionExistsAndTrue = true;
        }else {
            header("Location: /showarticle");
            exit(0);
        }
        $existingContent = file_get_contents(self::PROPAGANDE_PATH . "propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);

        $builder = $this->db->table('users');
        $query = $builder->getWhere(['pseudo' => $_SESSION['pseudo']], 1);
        $users = $query->getRow();

        if (isset($_POST['propagande'])) {
            $publication = $_POST['propagande'];

            if (strlen($publication) < 101 && $users->propagande >= 1) {
                $newpointpropagande = $users->propagande - 1;
                $propagandeTotal = $users->propagande_count + 1;
                // Récupérer les propagande actuelle
                $existingContent = file_get_contents(self::PROPAGANDE_PATH . "propagande.json");
// Convertir le contenu existant en tableau associatif ou objet
                $existingData = json_decode($existingContent, true);

                // archive propagande
                $archivepropa = file_get_contents(self::PROPAGANDE_PATH . "archivepropagande.json");
                $archiveData = json_decode($archivepropa, true);
                $newarchive = ['pseudo' => $_SESSION['pseudo'], // Remplacez "Nouveau pseudo" par le pseudo que vous souhaitez
                    'message' => $publication];

                // Ajoutez la nouvelle entrée au tableau $archiveData
                $archiveData[] = $newarchive;

                file_put_contents(self::PROPAGANDE_PATH . "archivepropagande.json", json_encode($archiveData));


                $propagande = [
                    'Propagande' => [
                        [
                            'pseudo' => $existingData['Propagande'][1]['pseudo'],
                            'message' => $existingData['Propagande'][1]['message']
                        ],
                        [
                            'pseudo' => $existingData['Propagande'][2]['pseudo'],
                            'message' => $existingData['Propagande'][2]['message']
                        ],
                        [
                            'pseudo' => $existingData['Propagande'][3]['pseudo'],
                            'message' => $existingData['Propagande'][3]['message']
                        ],
                        [
                            'pseudo' => $existingData['Propagande'][4]['pseudo'],
                            'message' => $existingData['Propagande'][4]['message']
                        ],
                        [
                            'pseudo' => $existingData['Propagande'][5]['pseudo'],
                            'message' => $existingData['Propagande'][5]['message']
                        ],
                        [
                            'pseudo' => $_SESSION['pseudo'], // Remplacez "Nouveau pseudo" par le pseudo que vous souhaitez
                            'message' => $publication
                        ]
                    ]
                ];


                $data = ['propagande' => $newpointpropagande,
                    'propagande_count'=> $propagandeTotal
                ];
                $updated = $this->db->table('users')->update($data, ['pseudo' => $_SESSION['pseudo']]);

// Convertir le tableau associatif ou objet en format JSON et réécrire le fichier
                file_put_contents(self::PROPAGANDE_PATH . "propagande.json", json_encode($propagande));
                $this->session->setFlashData('message', "success");


                header("Location: /propagande");
                exit(0);
            } else {
                $this->session->setFlashData('message', "error");
            }


        }


        return $this->twig->render('propagande.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'session' => $_SESSION,
            'propagande' => $existingData['Propagande'],
            'messageFlash' =>  $this->session->getFlashData('message'),
            'points' => $users->propagande

        ]);
    }

}
