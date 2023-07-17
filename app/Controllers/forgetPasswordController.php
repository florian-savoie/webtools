<?php

namespace App\Controllers;
use Config\Database;


class forgetPasswordController extends BaseController
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

    public function forgetpassword()
    {
         // si l'utilisateur est connecter on le redirige a l'acceuil 
        if (!empty($_SESSION['role'])) {
            header("Location: ./");
            exit(0);
        }
           
        // initialisation de la variable qui contiendra un message a retourner dans la vue
        $message = "";
    
        // Vérification si le formulaire a été soumis
        if (!empty($_POST['email'])) {
            $email = $_POST['email'];
    
            // Requête pour récupérer les utilisateurs correspondant à l'adresse e-mail
            $builder = $this->db->table('users');
            $query = $builder->getWhere(['email' => $email], 1);
            $users = $query->getResultArray();
    
            // Vérification si l'utilisateur existe
            if (isset($users[0])) {
                // Paramètres SMTP
                $smtpHost = 'smtp.hostinger.com';
                $smtpPort = 465; // Port SMTP de Hostinger
    
                // Paramètres de l'e-mail
                $fromEmail = 'webtools@floriansavoie.fr';
                $toEmail = $email;
                $subject = '[WEBOTOOLS] Réinitialisation de votre mot de passe';
    
                // Génération d'un jeton unique pour le lien de réinitialisation du mot de passe
                $token = uniqid();
    
                // Durée de validité du lien en minutes
                $validityDuration = 30;
    
                // Construction du lien avec le token
                $resetLink = 'https://floriansavoie.fr/webtools/reset-password?token=' . $token;
    
                // Contenu de l'e-mail avec le lien de réinitialisation
                $message = '<html>
                <head>
                    <title>' . $subject . '</title>
                    <style>
                        .button {
                            display: inline-block;
                            padding: 10px 20px;
                            background-color: #3498db;
                            color: #fff;
                            text-decoration: none;
                            border-radius: 4px;
                        }
                    </style>
                </head>
                <body>
                    <h1>Réinitialisation du mot de passe</h1>
                    <p>Bonjour,</p>
                    <p>Vous avez demandé la réinitialisation de votre mot de passe. Veuillez cliquer sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
                    <p><a class="button" href="' . $resetLink . '">Réinitialiser mon mot de passe</a></p>
                    <p>Le lien sera valide pendant ' . $validityDuration . ' minutes.</p>
                    <p>Si vous n\'avez pas demandé cette réinitialisation, veuillez ignorer cet e-mail.</p>
                    <p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p>
                    <p>Cordialement,</p>
                    <p>L\'équipe WEBTOOLS</p>
                </body>
                </html>';
    
                // Construction de l'en-tête de l'e-mail
                $headers = "From: $fromEmail\r\n";
                $headers .= "Reply-To: $fromEmail\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
                // Envoi de l'e-mail
                if (mail($toEmail, $subject, $message, $headers)) {
                    $data = [
                        'email' => $email,
                        'token' => $token,
                    ];
    
                    // Insertion du jeton dans la table 'forgotpassword'
                    $builder = $this->db->table('forgotpassword');
                    $insert = $builder->insert($data);
    
                    $message = [0 => 'success', 1 => 'E-mail envoyé avec succès, veuillez vérifier vos spams'];
                } else {
                    $message = [0 => 'danger', 1 => 'Erreur lors de l\'envoi de l\'e-mail.'];
                }
            } else {
                $message = [0 => 'danger', 1 => 'Aucune adresse trouvée.'];
            }
        }
    
        // Rendu du template 'forgotpassword.html.twig' avec les variables
        return $this->twig->render('forgotpassword.html.twig', [
            'message' => $message
        ]);
    }
}