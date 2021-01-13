<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recette Facile !</title>
  <meta name="description" content="Trouvez votre recette préférée en un clic !">
</head>
<body><pre><?php

  // séparer ses identifiants et les protéger, une bonne habitude à prendre
  include "recettefacile.dbconf.php";

  try {

    // instancie un objet $connexion à partir de la classe PDO
    $connexion = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);

    // Requête de sélection 01
    $requete = "SELECT * FROM `recettes`";
    $prepare = $connexion->prepare($requete);
    $prepare->execute();
    $resultat = $prepare->fetchAll();
    print_r([$requete, $resultat]); // debug & vérification

    // Requête d'insertion
    $requete = "INSERT INTO `recettes` (`recette_titre`, `recette_contenu`, `recette_datetime`)
                VALUES (:recette_titre, :recette_contenu, :recette_datetime);";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":recette_titre" => "Carbonade",
      ":recette_contenu" => "##Ingrédients\n-1 kg de boeuf maigre à braiser (paleron, gîte, hampe, etc ...)\n-1 cuillère à soupe de cassonade\n-1 bouquet garni\n-1 l de bière brune (Pelforth brune pour un goût délicat, ou Leffe Brune pour un goût plus sucré)\n-400 g d'oignon\n-250 g de lard fumé entier\n-5 tranches de pain d'épices\n-30 g de beurre\n-Sel de Guérande\n-3 cuillères à soupe de moutarde\n\n
      ##Préparation\n-Couper la viande en cubes de 2 à 3 centimètres de côté. Découper grossièrement les oignons et couper le lard en gros lardons.\n-Faire fondre le beurre et faire suer les oignons dedans 10 minutes pour les ramollir (feu au mini à couvert).\n-Ajouter le lard en augmentant légèrement le feu, remuer régulièrement en essayant de garder couvert le plus possible.\n-Une fois le lard bien rose, retirer le tout (sauf le jus) et le réserver dans un plat.\n-Mettre le feu au maxi et mettre la viande dans la cocotte. Remuer régulièrement (ne pas couvrir), la viande doit se colorer de tous les côtés, elle va finir par rendre pas mal de jus.
      Retirer la cocotte du feu, mettre la viande dans un plat en conservant le jus dans la cocotte.\n-Diluer la cassonade dans le jus de viande et mettre sur le feu à fond pour le réduire de moitié.\n-Une fois réduit, mettre le feu au mini et remettre le mélange lard-oignons en le mêlant au 'sirop', ajouter la viande et re-mélanger, ajouter le bouquet garni et recouvrir de bière entre (80 cl et 1 litre), saler très légèrement.\n-Recouvrir délicatement toute la surface avec le pain d'épices 'moutardé'.
      Laisser mijoter à couvert 3 heures sans remuer, tant que le pain d'épices n'est pas fondu (retirer le bouquet après 1 heure ou 2 maxi).\n-Si après trois heures, le jus est encore trop liquide, laisser encore mijoter en laissant le couvercle en partie ouvert, la sauce doit être légèrement collante en surface mais bien liquide en dessous et ne doit surtout pas brûler au fond.\n\n
      Petit + : Prévoir 1 cocotte en inox ou en fonte émaillée avec couvercle. L'idéal est de preparer la veille ou 2 jours avant (c'est encore meilleur) et donc de faire mijoter en 2 fois :
      \n- la premiere fois laisser mijoter 1h30 à 2 heures laisser refroidir et réserver au frais (à ce moment, la préparation doit être encore très liquide)
      \n-le lendemain, retirer la pellicule en surface de gras rejeté par le lard, et rechauffer encore a feu mini pendant 1h30 à 2 heures en ouvrant ou non le couvercle en fonction de l'épaisseur de la sauce.",
      ":recette_datetime" => date('Y-m-d H:i:s'),
    ));
    $resultat = $prepare->rowCount(); // rowCount() nécessite PDO::MYSQL_ATTR_FOUND_ROWS => true
    $lastInsertedrecetteId = $connexion->lastInsertId(); // on récupère l'id automatiquement créé par SQL
    print_r([$requete, $resultat, $lastInsertedrecetteId]); // debug & vérification

    // Requête de modification
    $requete = "UPDATE `recettes`
                SET `recette_titre` = :recette_titre
                WHERE `recette_id` = :recette_id;";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
      ":recette_id"   => $lastInsertedrecetteId,
      ":recette_titre" => "🍺 Carbonade",
    ));
    $resultat = $prepare->rowCount();
    print_r([$requete, $resultat]); // debug & vérification

    // Requête de suppression
    $requete = "DELETE FROM `recettes`
                WHERE ((`recette_id` = :recette_id));";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array($lastInsertedrecetteId)); // on lui passe l'id tout juste créé
    $resultat = $prepare->rowCount();
    print_r([$requete, $resultat, $lastInsertedrecetteId]); // debug & vérification

    //Requête insertion levain
    $requete = "INSERT INTO `hashtags`(`hashtag_id`, `hashtag_nom`) 
                VALUES (:hashtag_id, :hashtag_nom);";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
         ":hashtag_id" => NULL,
         ":hashtag_nom" => "levain",
     ));

    //Requête qui lie hashtag levain à recette pain au levain
    $requete = "SELECT * FROM `assoc_hashtags_recettes`";
    $prepare = $connexion->prepare($requete);
    $prepare->execute();
    $resultat = $prepare->fetchAll();
    print_r([$requete, $resultat]); // debug & vérification
    $requete = "INSERT INTO `assoc_hashtags_recettes` (`assoc_hr_hashtag_id`, assoc_hr_recette_id)
    VALUES (:assoc_hr_hashtag_id, :assoc_hr_recette_id);";
    $prepare = $connexion->prepare($requete);
    $prepare->execute(array(
    ":assoc_hr_hashtag_id" => 4,
    ":assoc_hr_recette_id" => 1
    ));
    $resultat = $prepare->rowCount(); // rowCount() pour check combien de row ont été ajouté
    $lastInsertedAssocId = $connexion->lastInsertId(); // on récupère l'id automatiquement créé par SQL
    print_r([$requete, $resultat, $lastInsertedAssocId]); // debug & vérification

    //Pour aller + loin : Créer une requête de sélection pour requêter des données dont le hashtag est "nourriture" et afficher le titre de chaque recette concernée
    $requete = "SELECT `recette_titre`
                FROM recettes
                INNER JOIN assoc_hashtags_recettes ON recette_id = assoc_hr_recette_id
                WHERE assoc_hr_hashtag_id = 1;";
    $prepare = $connexion->prepare($requete);
    $prepare->execute();
    $resultat = $prepare->fetchAll();
    print_r([$requete, $resultat]); // debug & vérification
    
  } catch (PDOException $e) {

    // en cas d'erreur, on récup et on affiche, grâce à notre try/catch
    exit("❌🙀💀 OOPS :\n" . $e->getMessage());

  }

?></pre></body>
</html>