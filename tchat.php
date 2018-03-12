<?php

session_start();
$username = "root";
$password = "user";
$host = "localhost";
$bdname = "tchat";

//=================================================================================================  Connexion SQL

try 
{
    $db = new PDO('mysql:host=localhost;dbname=tchat;charset=utf8', 'root', 'user');
}

catch(Exception $erreur) {
    die('Erreur: ' .$erreur->getMessage());
}

//===================================================================================================== Session Creation 

// <input type="text" name="login" value="" placeholder="Username">
// <input type="password" name="psw" value="" placeholder="Password">
// <input type="submit" name="join" value="Join the Chat">

// $req = $db->prepare('SELECT * FROM members WHERE login = $_post['login']) 

// if(isset($_post['login'] = ))    
// session_start();

// $_SESSION['login'] = 'login';

// $_SESSION['psw'] = 'psw';

//================================================================================================= Recup last messages

// $reponse = $db->query('SELECT login, txt_message FROM messages ORDER BY ID DESC LIMIT 0, 20');
// while ($donnee = $reponse->fetch())
// {
//     echo '<p><strong>' .
//     htmlspecialchars($donnee['login']) . '<strong> :' . htmlspecialchars($donnee['message']) . '</p>';
// }

// $reponse->closeCursor();

// Envoie Message form

$req = $db->prepare('INSERT INTO messages (login, txt_message) VALUE(?, ?)');
$req->execute(array($_POST['login'], $_POST['txt_message']));


//==================================================================================================== Essai foireux 1.0

// if(isset($_POST["en_ligne"]) && ($_POST["en_ligne"]) == "en_ligne")
// {
//     $status=$_POST["en_ligne"];
//     updatemysql($status);
// }

// if(isset($_POST["occupe"]) && ($_POST["occupe"]) == "occupe")
// {
//     $status=$_POST["occupe"];
//     updatemysql($status);
// }

// if(isset($_POST["absent"]) && ($_POST["absent"]) == "absent")
// {
//     $status=$_POST["absent"];
//     updatemysql($status);
// }

// if(isset($_POST["hors_ligne"]) && ($_POST["hors_ligne"]) == "hors_ligne")
// {
//     $status=$_POST["hors_ligne"];
//     updatemysql($status);
// }

// function updatemysql($id)
// {
//     global $db;
//     $reponse = $db->prepare("SELECT * FROM tasks WHERE id = :id LIMIT 0,1"); 
//     $reponse->execute(array(
//         "id" => $id,
//         ));

//     $ligne = $reponse->fetch();
//     $status = $ligne["status"] == "true" ? "false": "true";

//     $sql = $db->prepare("UPDATE tasks SET status = :status WHERE id = :id");
//     $sql->execute(array(
//         "id" => $id,
//         "status" => $status
//         ));
// }
 
//==================================================================================================== Fin Essai foireux 1.0



//=================================================================================================== Verification identifiant


//========================================================================== Récupération de l'utilisateur et de son pass hashé

$req = $db->prepare('SELECT id_user, password FROM users WHERE login = :login');
$req->execute(array(
    'login' => $login));

$resultat = $req->fetch();

//=================================================================== Comparaison du pass envoyé via le formulaire avec la BDD

$PasswordCorrect = password_verify($_POST['psw'], $resultat['psw']);

if (!$resultat)
{
    echo "Veuillez confirmer l'ordre de désintégration d'Alderaan, confirmer ? </br>";
    echo '<a href="alderaan.html"><button>Confirmer</button></a><a href="http://www.google.com/"><button>Fuck, Go back !</button></a>';
}
else
{
    if ($PasswordCorrect) {
        session_start();
        $_SESSION['id_user'] = $resultat['id_user'];
        $_SESSION['login'] = $login;
        echo "Félicitation, vous n'avez pas alzheimer !";
    }
    else {
        echo 'Je pense que vous avez fait une erreur :/';
    }
}

// <input type="button" name="en_ligne" value="En Ligne"></br>
// <input type="button" name="occupe" value="Occupé"></br>
// <input type="button" name="absent" value="Absent"></br>
// <input type="button" name="hors_ligne" value="Hors Ligne">

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link id="pagestyle" href="css/tchat.css" rel="stylesheet">
    <link rel="stylesheet" media="screen" href="https://fontlibrary.org/face/grundschrift" type="text/css"/> 
<body>
    <section class="chat">
<!-- ================================================================================================= Add condition status -->
        <div class="list_members">
        <?php
        echo(date("H:i:s")); 
        ?>
            <div class="logo_chat">
            <img class="logo" src="images/corner2.png">
            </div>
            <div class="status">
                <h4>Status</h4>
                <input type="button" name="en_ligne" value="En Ligne"></br>
                <input type="button" name="occupe" value="Occupé"></br>
                <input type="button" name="absent" value="Absent"></br>
                <input type="button" name="hors_ligne" value="Hors Ligne">
            </div>
            <div class="users_status">
                <h4>Membres Connectés</h4>
                <span class="online">
                <?php
                    $result = $db->query('SELECT * FROM users WHERE status="En Ligne"');
                    $resultArr = $result->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($resultArr as $key => $login)
                    {
                        print $login["login"];
                        echo "<input type='hidden' name='login[$key]' value='".$login["id"]."' />";
                        ?>
                        <html>
                        <img src="images/online.png"/>
                        </br>
                        </html>
                        <?php
                    }
                        ?>
                </span>
                <h4>Membres Déconnectés</h4>
                <span class="offline">
                <?php
//==================================================================================================== Display Offline Members
                    $result = $db->query('SELECT * FROM users WHERE status="Hors Ligne"');
                    $resultArr = $result->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($resultArr as $key => $login)
                    {
                        print $login["login"];
                        echo "<input type='hidden' name='login[$key]' value='".$login["id"]."' />";
                        ?>
                        <html>
                        <img src="images/offline.png"/>
                        </br>
                        </html>
                        <?php
                    }
                        ?>
                </span>
            </div>
        </div>
        <div class="log_out">
            <?php
                if (isset($_SESSION['id']) AND isset($_SESSION['pseudo']))
                {
                    echo 'Bonjour ' . $_SESSION['pseudo'];
                }
                ?>
            <button type"button" name="logout">Log-Out</button>
        </div>
        <div class="messages">
            <div class="mess_title">
                <h2>Message d'Accueil</h2>
                <p> Lorem Ipsum </p>
            </div>
            <div class="mess_list">
                <?php
//==================================================================================================== Display last 20 messages
                    $result = $db->query('SELECT * FROM messages');
                    $resultArr = $result->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($resultArr as $key => $txt_message)
                    {
                        print $txt_message["date_message"] . $login["login"] . $txt_message["txt_message"];
                        echo "<br/>";
                    }
                ?>
            </div>
            <div class="mess_add">
                <input type="text" name="new_mess" value="" placeholder="Entrez votre message">
                <input type="button" name="send" value="Send">
            </div>
        </div>
    </section>
    <section class="log_in">
        <form action="tchat.php" method="post">
            <div>
                <input type="text" name="login" value="" placeholder="Username"> </br>
                <input type="password" name="psw" value="" placeholder="Password"> </br>
                <input type="submit" name="join" value="Join the Chat"> </br>
            </div>
            <div>
                <a href="sign.php" class="link_sign">Not already registered?</a>
                <img class="alice" src="images/alice.gif">
                <img class="aliza" src="images/aliza.gif">
            </div>
        </form>
    </section>
</body>

<?php
/*
================================== Version - 0.3.8 =============================
========Starting - 05/03/18 ============================Ending - 12/03/18 ======
====================================== Author : ================================
    ***** *     **      *                                                           
  ******  **    ****   **                                                           
 **   *  * **    ****  **                                                           
*    *  *  **    * *   **                                                           
    *  *    **   *     **                                           ***  ****         
   ** **    **   *     **  ***      ****    ***  ****       ****     **** ****    
   ** **     **  *     ** * ***    * ***  *  **** **** *   * ***  *   **   ****     
   ** **     **  *     ***   ***  *   ****    **   ****   *   ****    **              
   ** **      ** *     **     ** **    **     **    **   **    **     **            
   ** **      ** *     **     ** **    **     **    **   **    **     **            
   *  **       ***     **     ** **    **     **    **   **    **     **            
      *        ***     **     ** **    **     **    **   **    **     **              
  ****          **     **     ** **    **     **    **   **    **     ***           
 *  *****              **     **  ***** **    ***   ***   ***** **     ***          
*     **                **    **   ***   **    ***   ***   ***   **                 
*  *                          *                                                    
 **                          *                                                      
                            *                                                       
                           *                                                        
*/