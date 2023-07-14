<?php

function addarticleajax()
{
    // Vérifier si la requête est une requête POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérifier si toutes les données requises sont présentes et non vides
        if (isset($_POST['title'], $_POST['url'], $_POST['description'], $_POST['category'], $_POST['souscat'])) {
            $title = $_POST['title'];
            $url = $_POST['url'];
            $content = $_POST['description'];
            $category_id = $_POST['category'];
            $souscat = $_POST['souscat'];

            // Vérifier si les champs ont été remplis
            if (!empty($title) && !empty($url) && !empty($content) && !empty($category_id)) {

                // Vérifier si l'utilisateur est connecté
                if (!empty($_SESSION['iduser'])) {
                    $user_id = $_SESSION['iduser'];

                    // Vérifier si l'image compressée est présente
                    if (isset($_POST['image'])) {
                        $compressedImage = $_POST['image'];

                        // Générer un nom de fichier unique pour chaque image
                        $fileName = uniqid('') . '.jpg';

                        // Convertir les données Base64 en binaire
                        $imageData = base64_decode(str_replace('data:image/jpeg;base64,', '', $compressedImage));

                        // Chemin de destination pour enregistrer l'image
                        $destinationPath = 'assets/imgArticle/' . $fileName;

                        // Enregistrer l'image sur le serveur
                        if (file_put_contents($destinationPath, $imageData)) {
                      

                            // Insérer les données de l'article dans la base de données
                            $builder = $this->db->table('articles');
                            $insertok = $builder->insert([
                                'user_id' => $user_id,
                                'category_id' => $category_id,
                                'subcategory_id' => $souscat,
                                'title' => $title,
                                'content' => $content,
                                'image' => $destinationPath,
                                'link' => $url,
                            ]);

                            if ($insertok) {
                                $response = array(
                                    'etat' => 'success',
                                    'message' => 'L\'article a été enregistré avec succès.'
                                );
                            }
                        }
                    }
                }
            }
        }
    }
    // Vérifier si la variable $response est vide ( si l'insertion n'a pas fonctionnée)
    if (empty($response)) {
        $response = array(
            'etat' => 'error',
            'message' => 'Une erreur c\'est produite lors de l\'insertion.'
        );
    }

    // Envoyer la réponse JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}