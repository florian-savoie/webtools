<?php

namespace App\Controllers;

use Config\Database;


class showarticlesController extends BaseController
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

    public function showarticle()
    {
        $sessionExistsAndTrue = false;
        $articlesPage = 18;
        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ($autoriser === true) {
            $sessionExistsAndTrue = true;
        } else {
            $sessionExistsAndTrue = false;
        $builder = $this->db->table('categories');
        $query = $builder->get();
        $categories = $query->getResultArray();

        $builder = $this->db->table('subcategories');
        $query = $builder->get();
        $souscategories = $query->getResultArray();

        if (isset($_GET['category']) && isset($_GET['souscat'])) {
            if (!empty($_GET['pages'])) {
                $show = (int) ($_GET['pages'] * 18) - 18;
            } else {
                $show = 0;
            }
            $category_id = (int) $_GET['category'];
            $subcategory_id = (int) $_GET['souscat'];

            $builder = $this->db->table('articles');
            $builder->select('articles.*, categories.name as category_name, categories.color as category_color');
            $builder->join('categories', 'categories.id = articles.category_id', 'left');
            $builder->where('category_id', $category_id);
            $builder->where('subcategory_id', $subcategory_id);
            $builder->orderBy('id', 'DESC');

            $queryTotal = clone $builder; // Clonez le constructeur de requête pour obtenir le nombre total d'articles
            $numArticles = $queryTotal->countAllResults();

            $builder->limit($articlesPage, $show); // Limite les résultats à 18 éléments, en commençant par l'élément 18
            $query = $builder->get();
            $articles = $query->getResultArray();

            $pages = ceil($numArticles / 18); // Calcule le nombre total de pages en fonction du nombre total d'articles
        } else if (isset($_GET['category']) && $_GET['category'] != "favoris") {
            if (!empty($_GET['pages'])) {
                $show = (int) ($_GET['pages'] * 18) - 18;
            } else {
                $show = 0;
            }
            $category_id = (int) $_GET['category'];
            $category_id = (int) $_GET['category'];

            $builder = $this->db->table('articles');
            $builder->select('articles.*, categories.name as category_name, categories.color as category_color');
            $builder->join('categories', 'categories.id = articles.category_id', 'left');
            $builder->where('is_approved', 1);
            $builder->where('category_id', $category_id);
            $builder->orderBy('id', 'DESC');

            $queryTotal = clone $builder; // Clonez le constructeur de requête pour obtenir le nombre total d'articles
            $numArticles = $queryTotal->countAllResults();

            $builder->limit($articlesPage, $show); // Limite les résultats à 18 éléments, en commençant par l'élément 18
            $query = $builder->get();
            $articles = $query->getResultArray();

            $pages = ceil($numArticles / 18); // Calcule le nombre total de pages en fonction du nombre total d'articles




        }  else {
            if (empty($articles)) { // Ajout de la vérification si $articles est vide

                if (!empty($_GET['pages'])) {
                    $show = (int) ($_GET['pages'] * 18) - 18;
                } else {
                    $show = 0;
                }
                $builder = $this->db->table('articles');
                $builder->select('articles.*, categories.name as category_name, categories.color as category_color, subcategories.name as subcategories_name');
                $builder->join('categories', 'categories.id = articles.category_id', 'left');
                $builder->join('subcategories', 'subcategories.id = articles.subcategory_id', 'left');
                $builder->where('articles.is_approved', 1);
                $builder->orderBy('articles.id', 'DESC');

                $queryTotal = clone $builder; // Clonez le constructeur de requête pour obtenir le nombre total d'articles
                $numArticles = $queryTotal->countAllResults();

                $builder->limit($articlesPage, $show); // Limite les résultats à 18 éléments, en commençant par l'élément 18
                $query = $builder->get();
                $articles = $query->getResultArray();

                $pages = ceil($numArticles / 18); // Calcule le nombre total de pages en fonction du nombre total d'articles

            }
        }
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);
        return $this->twig->render('showarticles.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'articles' => $articles,
            'categories' => $categories,
            'souscategories' => $souscategories,
            'propagande' => $existingData['Propagande'],
            'pages' => $pages,
            'numberpage' => $_GET['pages'] ?? 1

        ]);
        die();
        }

        $idUser = $_SESSION['iduser'];



        $builder = $this->db->table('favorites');
        $builder->select('favorites.*, articles.title as title , articles.link as link');
        $builder->join('articles', 'favorites.article_id = articles.id', 'left');
        $builder->where('favorites.user_id', $idUser);
        $builder->where('articles.is_approved', 1);
        $builder->orderBy('id', 'DESC');
        $query = $builder->get();
        $likes = $query->getResultArray();

        $builder = $this->db->table('votes');
        $builder->orderBy('id', 'DESC');
        $builder->where('user_id', $idUser); // Remplacez $id par l'ID que vous voulez rechercher
        $query = $builder->get();
        $votes = $query->getResultArray();

        $builder = $this->db->table('categories');
        $query = $builder->get();
        $categories = $query->getResultArray();

        $builder = $this->db->table('subcategories');
        $query = $builder->get();
        $souscategories = $query->getResultArray();

        if (isset($_GET['category']) && isset($_GET['souscat'])) {
            if (!empty($_GET['pages'])) {
                $show = (int) ($_GET['pages'] * 18) - 18;
            } else {
                $show = 0;
            }
            $category_id = (int) $_GET['category'];
            $subcategory_id = (int) $_GET['souscat'];

            $builder = $this->db->table('articles');
            $builder->select('articles.*, categories.name as category_name, categories.color as category_color');
            $builder->join('categories', 'categories.id = articles.category_id', 'left');
            $builder->where('category_id', $category_id);
            $builder->where('subcategory_id', $subcategory_id);
            $builder->orderBy('id', 'DESC');

            $queryTotal = clone $builder; // Clonez le constructeur de requête pour obtenir le nombre total d'articles
            $numArticles = $queryTotal->countAllResults();

            $builder->limit($articlesPage, $show); // Limite les résultats à 18 éléments, en commençant par l'élément 18
            $query = $builder->get();
            $articles = $query->getResultArray();

            $pages = ceil($numArticles / 18); // Calcule le nombre total de pages en fonction du nombre total d'articles
        } else if (isset($_GET['category']) && $_GET['category'] != "favoris") {
            if (!empty($_GET['pages'])) {
                $show = (int) ($_GET['pages'] * 18) - 18;
            } else {
                $show = 0;
            }
            $category_id = (int) $_GET['category'];
            $category_id = (int) $_GET['category'];

            $builder = $this->db->table('articles');
            $builder->select('articles.*, categories.name as category_name, categories.color as category_color');
            $builder->join('categories', 'categories.id = articles.category_id', 'left');
            $builder->where('is_approved', 1);
            $builder->where('category_id', $category_id);
            $builder->orderBy('id', 'DESC');

            $queryTotal = clone $builder; // Clonez le constructeur de requête pour obtenir le nombre total d'articles
            $numArticles = $queryTotal->countAllResults();

            $builder->limit($articlesPage, $show); // Limite les résultats à 18 éléments, en commençant par l'élément 18
            $query = $builder->get();
            $articles = $query->getResultArray();

            $pages = ceil($numArticles / 18); // Calcule le nombre total de pages en fonction du nombre total d'articles




        } else if (isset($_GET['category']) == "favoris") {

            if (!empty($_GET['pages'])) {
                $show = (int) ($_GET['pages'] * 18) - 18;
            } else {
                $show = 0;
            }
            $category_id = (int) $_GET['category'];

            $builder = $this->db->table('articles');
            $builder->select('articles.id as id,articles.rating as rating, articles.title as title , articles.link as link , articles.image as image, articles.content as content, articles.title as title, articles.submission_date as submission_date,categories.name as category_name, categories.color as category_color,subcategories.name as subcategories_name');
            $builder->join('favorites', 'favorites.article_id = articles.id', 'left');
            $builder->join('categories', 'categories.id = articles.category_id', 'left');
            $builder->join('subcategories', 'subcategories.id = articles.subcategory_id', 'left');
            $builder->where('favorites.user_id', $idUser);
            $builder->where('articles.is_approved', 1);
            $builder->orderBy('id', 'DESC');

            $queryTotal = clone $builder; // Clonez le constructeur de requête pour obtenir le nombre total d'articles
            $numArticles = $queryTotal->countAllResults();

            $builder->limit($articlesPage, $show); // Limite les résultats à 18 éléments, en commençant par l'élément 18
            $query = $builder->get();
            $articles = $query->getResultArray();

            $pages = ceil($numArticles / 18); // Calcule le nombre total de pages en fonction du nombre total d'articles


        } else {
            if (empty($articles)) { // Ajout de la vérification si $articles est vide

                if (!empty($_GET['pages'])) {
                    $show = (int) ($_GET['pages'] * 18) - 18;
                } else {
                    $show = 0;
                }
                $builder = $this->db->table('articles');
                $builder->select('articles.*, categories.name as category_name, categories.color as category_color, subcategories.name as subcategories_name');
                $builder->join('categories', 'categories.id = articles.category_id', 'left');
                $builder->join('subcategories', 'subcategories.id = articles.subcategory_id', 'left');
                $builder->where('articles.is_approved', 1);
                $builder->orderBy('articles.id', 'DESC');

                $queryTotal = clone $builder; // Clonez le constructeur de requête pour obtenir le nombre total d'articles
                $numArticles = $queryTotal->countAllResults();

                $builder->limit($articlesPage, $show); // Limite les résultats à 18 éléments, en commençant par l'élément 18
                $query = $builder->get();
                $articles = $query->getResultArray();

                $pages = ceil($numArticles / 18); // Calcule le nombre total de pages en fonction du nombre total d'articles

            }
        }
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);
        return $this->twig->render('showarticles.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'articles' => $articles,
            'likes' => $likes,
            'votes' => $votes,
            'categories' => $categories,
            'souscategories' => $souscategories,
            'session' => $_SESSION,
            'propagande' => $existingData['Propagande'],
            'pages' => $pages,
            'numberpage' => $_GET['pages'] ?? 1

        ]);
    }

    public function showarticleadmin()
    {
        $sessionExistsAndTrue = false;

        $autoriser = $this->session->get('Autoriser');

        // Vérifier si la session existe et est vraie
        if ($autoriser === true && $_SESSION['role'] == 'admin') {
            $sessionExistsAndTrue = true;
        } else {
            header("Location: ./login");
            exit(0);
        }

        $idUser = $_SESSION['iduser'];


        $builder = $this->db->table('favorites');
        $builder->orderBy('id', 'DESC');
        $builder->where('user_id', $idUser); // Remplacez $id par l'ID que vous voulez rechercher
        $query = $builder->get();
        $likes = $query->getResultArray();

        $builder = $this->db->table('votes');
        $builder->orderBy('id', 'DESC');
        $builder->where('user_id', $idUser); // Remplacez $id par l'ID que vous voulez rechercher
        $query = $builder->get();
        $votes = $query->getResultArray();

        $builder = $this->db->table('categories');
        $query = $builder->get();
        $categories = $query->getResultArray();

        $builder = $this->db->table('subcategories');
        $query = $builder->get();
        $souscategories = $query->getResultArray();

        if (isset($_GET['category']) && isset($_GET['souscat'])) {
            $builder = $this->db->table('articles');
            $builder->where('is_approved', 1);
            $builder->where('category_id', $_GET['category']);
            $builder->where('subcategory_id', $_GET['souscat']);
            $builder->orderBy('id', 'DESC');
            $query = $builder->get();
            $articles = $query->getResultArray();

        } else if (isset($_GET['category'])) {
            $builder = $this->db->table('articles');
            $builder->where('is_approved', 1);
            $builder->where('category_id', $_GET['category']);
            $builder->orderBy('id', 'DESC');
            $query = $builder->get();
            $articles = $query->getResultArray();
        } else {
            if (empty($articles)) { // Ajout de la vérification si $articles est vide
                $builder = $this->db->table('articles');
                $builder->where('is_approved', 1);
                $builder->orderBy('id', 'DESC');
                $query = $builder->get();
                $articles = $query->getResultArray();
            }
        }
        $existingContent = file_get_contents("assets/json/propagande/propagande.json");
        // Convertir le contenu existant en tableau associatif ou objet
        $existingData = json_decode($existingContent, true);

        if (isset($_POST['deleteArticle'])) {
            $builder = $this->db->table('articles');
            $builder->where('id', $_POST['idDeleteArticle']);
            $builder->delete();
            header("Location: /showarticleadmin");
            exit(0);
        }
        return $this->twig->render('showarticlesadmin.html.twig', [
            'sessionExistsAndTrue' => $sessionExistsAndTrue,
            'articles' => $articles,
            'likes' => $likes,
            'votes' => $votes,
            'categories' => $categories,
            'souscategories' => $souscategories,
            'session' => $_SESSION,
            'propagande' => $existingData['Propagande']

        ]);
    }


    public function votestar()
    {
        if (isset($_POST['note']) && isset($_POST['idarticle'])) {
            $note = $_POST['note'];
            $idarticle = $_POST['idarticle'];


            $builder = $this->db->table('votes');
            $builder->where('user_id', $_SESSION['iduser']);
            $builder->where('article_id', $idarticle);
            $query = $builder->get();
            $result = $query->getResultArray();

            if ($result) {

                $dernierVote = $result[0]['vote'];
                $data = [
                    'vote' => $note
                ];
                $updated = $this->db->table('votes')->update($data, ['article_id' => $idarticle, 'user_id' => $_SESSION['iduser']]);


                $sql = "UPDATE articles SET rating = ((rating * vote_ranted) - $dernierVote + $note) / vote_ranted WHERE id = ?";
                $updated = $this->db->query($sql, [$idarticle]);

            } else {

                $data = [
                    'article_id' => $idarticle,
                    'vote' => $note,
                    'user_id' => $_SESSION['iduser'],
                ];
                $builder = $this->db->table('votes');
                $insert = $builder->insert($data);
                $sql = "UPDATE articles SET rating = CASE WHEN vote_ranted = 0 THEN $note ELSE (rating * vote_ranted + $note) / (vote_ranted + 1) END, vote_ranted = vote_ranted + 1 WHERE id = ?";
                $updated = $this->db->query($sql, [$idarticle]);


            }




            if ($this->db->error()) {
                $error = $this->db->error();
                $response = "Erreur lors de la mise à jour : " . $error['message'];
            } else {
                $response = "note effectuée avec succès.";
            }
        } else {
            $response = "Erreur lors de la note.";
        }

        // Renvoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }
    public function addfavorite()
    {
        if (isset($_POST['idarticle'])) {
            $idarticle = $_POST['idarticle'];

            $data = [
                'article_id' => $idarticle,
                'user_id' => $_SESSION['iduser'],
            ];
            $builder = $this->db->table('favorites');
            $insert = $builder->insert($data);

            if ($this->db->error()) {
                $error = $this->db->error();
                $response = "Erreur lors de la mise à jour : " . $error['message'];
            } else {
                $response = "note effectuée avec succès.";
            }
        } else {
            $response = "Erreur lors de la note.";
        }

        // Renvoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }
    public function deletefavorite()
    {
        if (isset($_POST['idarticle'])) {
            $idarticle = $_POST['idarticle'];

            $builder = $this->db->table('favorites');
            $builder->where('article_id', $idarticle);
            $builder->where('user_id', $_SESSION['iduser']);
            $builder->delete();

            if ($this->db->error()) {
                $error = $this->db->error();
                $response = "Erreur lors de la mise à jour : " . $error['message'];
            } else {
                $response = "note effectuée avec succès.";
            }
        } else {
            $response = "Erreur lors de la note.";
        }

        // Renvoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }

    function testtt()
    {

        $title = $_POST['title'];
        $url = $_POST['url'];
        $content = $_POST['description'];
        $category_id = $_POST['category'];
        $souscat = $_POST['souscat'];
        $idarticle = $_POST['id'];

        if (isset($_POST['image'])) {
            $compressedImage = $_POST['image'];

            // Générer un nom de fichier unique pour chaque image
            $fileName = uniqid('') . '.jpg';

            // Convertir les données Base64 en binaire
            $imageData = base64_decode(str_replace('data:image/jpeg;base64,', '', $compressedImage));

            // Chemin de destination pour enregistrer l'image
            $destinationPath = 'assets/imgArticle/' . $fileName;

            // supprimer l'anciene image
            $builder = $this->db->table('articles');
            $query = $builder->getWhere(['id' => $idarticle], 1);
            $articles = $query->getResultArray();

            unlink($articles['0']['image']);
            // Enregistrer l'image sur le serveur
            file_put_contents($destinationPath, $imageData);
            $response = "image";

            $data = [
                'title' => $title,
                'content' => $content,
                'category_id' => $category_id,
                'subcategory_id' => $souscat,
                'link' => $url,
                'image' => $destinationPath,
            ];
            $updated = $this->db->table('articles')->update($data, ['id' => $idarticle]);
        } else {
            $data = [
                'title' => $title,
                'content' => $content,
                'category_id' => $category_id,
                'subcategory_id' => $souscat,
                'link' => $url
            ];
            $updated = $this->db->table('articles')->update($data, ['id' => $idarticle]);




            $response = "pas d'image";
        }

        // Renvoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        return;
    }

}