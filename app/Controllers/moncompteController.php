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


      
// Paramètres SMTP
$smtpHost = 'smtp.hostinger.com';
$smtpPort = 465; // Port SMTP de Hostinger

// Paramètres de l'e-mail
$fromEmail = 'iciMacron@msn.com';
$toEmail = 'floriansavoie30@hotmail.com';
$subject = '[WEBOTOOLS] Réinitialisation de votre mot de passe';
$message = '<div style="background-color:#F6FAFD"><div style="background:#ffffff; background-color:#ffffff; margin:0px auto; max-width:600px"><table align="center" border="0" cellpadding="0"
 cellspacing="0" role="presentation" style="background:#ffffff; background-color:#ffffff; width:100%"><tbody><tr><td style="direction:ltr; font-size:0px; padding:20px 0px 20px 0px;
  text-align:center; vertical-align:top"><div class="x_mj-column-per-100 x_outlook-group-fix" style="font-size:13px; text-align:left; direction:ltr; display:inline-block; vertical-align:top; 
  width:100%"><table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="vertical-align:top"><tbody><tr><td>
  <img data-imagetype="External" src="https://www.om.fr/sites/default/files/2019-06/Logo%20Marseille.png" alt="logo OM" title="logoOM" style="object-fit:contain; border:none;
   display:block; outline:none; text-decoration:none; height:70px; margin:auto; margin-bottom:24px"> </td></tr><tr><td align="left" style="font-size:0px; padding:0px 25px 0px 25px;
    padding-top:0px; padding-bottom:0px; word-break:break-word"><div style="font-size:13px; line-height:22px; text-align:left; color:#55575d">
    <h1 style="font-size:20px; font-weight:bold; text-align:center">Demande de réinitialisation de mot de passe. </h1></div></td></tr><tr><td><div style= font-size:16px; line-height:22px; 
    text-align:left; color:#55575d"><p style="font-size:13px; margin:16px 48px; text-align:center">Pour réinitialiser votre mot de passe, suivez le lien ci-dessous. </p><p style="font-size:16px;
     margin:24px 0; text-align:center"><a href="https://t.newsletter.om.fr/fw19c3/27884727/475/2480143937.html?h=f6877f2259b22c149223e84f103c9710&amp;s=4Iq8x4daOb6M&amp;u=https%3A%2F%2Fconnect.om.fr%2Freset-password%3Fverification_code%3D670373730%26email%3Dfloriansavoie30%2540hotmail.com" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="text-decoration:none; color:#FFFFFF; font-size:16px; background-color:#0E96D2; border:none; border-radius:8px; -webkit-border-radius:8px; padding:8px 24px" data-linkindex="0">
     Réinitialiser mon mot de passe</a> </p></div></td></tr><tr><td><div style=" font-size:16px; line-height:22px; text-align:left; color:#55575d"><p style="font-size:12px; margin:16px 48px;
      text-align:center">Si vous netes pas à lorigine de cette demande, vous pouvez ignorer ce mail en toute sécurité. </p></div></td></tr><tr><td align="left" style="font-size:0px;
       padding:0px 25px 0px 25px; padding-top:0px; padding-bottom:0px; word-break:break-word"><div style=" font-size:16px; line-height:22px; text-align:left; color:#55575d"><p style="font-size:12px;
        margin:0px 0px 0px 0px" aria-hidden="true">&nbsp;</p><p style="text-align:center; font-size:14px; margin:0px 0px 0px 0px"></p><p style="font-size:12px; margin:16px 48px; 
        text-align:center">Si vous avez une question, veuillez visiter notre <a href="https://t.newsletter.om.fr/fw19c3/27884727/475/2480143937.html?h=401d1ca635797bb3e9878f94d07e97cc&amp;s=YhqNvTQ28o0n&amp;u=https%3A%2F%2Fom.fr%2Faide" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:#0E96D2" data-linkindex="1">
        centre daide</a>. Vous pouvez également contacter notre
         <a href="https://t.newsletter.om.fr/fw19c3/27884727/475/2480143937.html?h=48dcd4266a473e8b403bc95b370f6690&amp;s=SYlL4Q7SuczF&amp;u=https%3A%2F%2Fom.fr%2Fcontact" target="_blank"
          rel="noopener noreferrer" data-auth="NotApplicable" style="color:#0E96D2" data-linkindex="2">service client OM</a>. </p></div></td></tr></tbody></table></div></td></tr></tbody>
          </table></div><div style="background:#ffffff; background-color:#ffffff; margin:0px auto; max-width:600px"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
           style="background:#ffffff; background-color:#ffffff; width:100%"><tbody><tr><td style="direction:ltr; font-size:0px; padding:20px 0; padding-bottom:0px; padding-top:0px; text-align:center;
            vertical-align:top"><div class="x_mj-column-per-100 x_outlook-group-fix" style="font-size:13px; text-align:left; direction:ltr; display:inline-block; vertical-align:top; width:100%">
            <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="vertical-align:top"><tbody><tr><td align="center" style="font-size:0px; padding:10px 25px;
             padding-top:0px; padding-right:0px; padding-bottom:0px; padding-left:0px; word-break:break-word"><table border="0" cellpadding="0" cellspacing="0" role="presentation" 
             style="border-collapse:collapse; border-spacing:0px"><tbody><tr><td style="width:600px; margin:40px"><hr style="border:1px solid #C5A459"><div style=" font-size:12px; line-height:22px;
              text-align:center; color:#55575d"><p style="margin:16px 0">Envoyé par <a href="https://t.newsletter.om.fr/fw19c3/27884727/475/2480143937.html?h=9130888b72386cae8167d2ca4637e50e&amp;s=3c6Wc8DRNS4j&amp;u=https%3A%2F%2Fom.fr" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:#0E96D2; text-decoration:none" data-linkindex="3">Olympique de Marseille</a> </p></div><hr style="border:2px solid #0E96D2"></td></tr></tbody></table></td></tr></tbody></table></div></td></tr></tbody></table></div><div style="margin:0px auto; max-width:600px"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%"><tbody><tr><td style="direction:ltr; font-size:0px; padding:0 0 20px 0; text-align:center; vertical-align:top"><div class="x_mj-column-per-100 x_outlook-group-fix" style="font-size:13px; text-align:left; direction:ltr; display:inline-block; vertical-align:top; width:100%"><table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%"
               style="vertical-align:top"><tbody><tr><td align="center" style="font-size:0px; padding:0px; word-break:break-word"></td></tr></tbody></table></div></td></tr></tbody></table></div>
               <div style="margin:0px auto; max-width:600px"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%"><tbody><tr><td style="direction:ltr;
                font-size:0px; padding:20px 0px 20px 0px; text-align:center; vertical-align:top"><div class="x_mj-column-per-100 x_outlook-group-fix" style="font-size:13px; text-align:left; direction:ltr;
                 display:inline-block; vertical-align:top; width:100%"><table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%"><tbody><tr><td style="vertical-align:top; padding:0">
                 </td></tr></tbody></table></div></td></tr></tbody></table></div></div>';

// Construction de l'en-tête de l'e-mail
$headers = "From: $fromEmail\r\n";
$headers .= "Reply-To: $fromEmail\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Envoi de l'e-mail
if (mail($toEmail, $subject, $message, $headers)) {
    echo 'E-mail envoyé avec succès';
} else {
    echo 'Erreur lors de l\'envoi de l\'e-mail';
}
die;
        // Vérifier si la session existe et est vraie
        if (!empty($_SESSION['role'])) {
            $sessionExistsAndTrue = true;
        }else {
            header("Location: ./showarticle");
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