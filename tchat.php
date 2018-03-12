<?php

session_start();
$username = "root";
$password = "user";
$host = "localhost";
$dbname = "tchat";

//=================================================================================================  Connexion SQL

try 
{
    $db = new PDO('mysql:host=localhost;dbname=tchat;charset=utf8', 'root', 'user');
}
catch(Exception $erreur) {
    die('Erreur: ' .$erreur->getMessage());
}


//=================================================================== Comparaison du pass envoyé via le formulaire avec la DB

if(isset($_POST['Log_in'])) 
{
    if(!empty($_POST['login']) AND !empty($_POST['password'])) 
    { 
    $_POST['login'] = filter_var($_POST['login'],FILTER_SANITIZE_STRING);
    $_POST['password'] = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
    $login = htmlspecialchars($_POST['login']);
    $password = password_hash($_POST['password']);
    
    $req_user = $db->prepare('SELECT * FROM users WHERE login = ? AND password = ?');
    $req_user->execute(array($login, $password));
    
  
    $user_info = $req_user->fetchAll(PDO::FETCH_ASSOC);
        if(count($user_info) == 1) 
        {
            $_SESSION['id_user'] = $user_info[0]['id_user'];
            $_SESSION['login'] = $user_info[0]['login'];
            $_SESSION['password'] = $user_info[0]['password'];
        }
        else 
        {
        $Error="vous n'êtes pas encore inscrit";
            echo($Error);
            echo($password);
        }
    }  
}
else
{
    echo('tvpasbien');
}

if(isset($_POST['logout']))
{
    $_POST['login'] = filter_var($_POST['login'],FILTER_SANITIZE_STRING);
    $_POST['password'] = filter_var($_POST['password'],FILTER_SANITIZE_STRING);
    session_unset();
    session_destroy();
}

global $db;
if(isset($_POST['send']) && isset($_POST['new_mess']) && !empty($_SESSION['login'])){
  
  $_POST['new_mess'] = filter_var($_POST['new_mess'],FILTER_SANITIZE_STRING);
  
  $req = $db->prepare('INSERT INTO messages (login, txt_message) VALUE (?, ?)');
  $login2 = $_SESSION['id_user'];
  $req->execute(array( $login2, $_POST['txt_message']));

  }
$req_user = $db->prepare('SELECT * FROM messages 
                    LEFT JOIN users
                    ON messages.id_user = user.id
                    ORDER BY date DESC');

$req_user->execute();

$user_info = $req_user->fetchAll(PDO::FETCH_ASSOC);

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
                        echo "<input type='hidden' name='login[$key]' value='".$login["id_user"]."' />";
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
                    $result = $db->query('SELECT * FROM users WHERE status="Hors Ligne"');
                    $resultArr = $result->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($resultArr as $key => $login)
                    {
                        print $login["login"];
                        echo "<input type='hidden' name='login[$key]' value='".$login["id_user"]."' />";
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
            <?php if(!empty($_SESSION['login'])): ?>
            <button type"button" name="logout">Log-Out</button>
            <?php endif; ?>
        </div>
        <div class="messages">
            <div class="mess_title">
                <h2>Message d'Accueil</h2>
                <p> Lorem Ipsum </p>
            </div>
            <div class="mess_list">
                <?php foreach($user_info as $value):  ?>
                    <div>
                        <label class="pseudo"> <?php echo $value['login']; ?></label>
                        <label class="heure"> <?php echo $value['date']; ?></label>
                        <label class="lbl_message"> <?php echo $value['txt_message']; ?></label>
                    </div>
                <?php endforeach;?>
            </div>
            <div class="mess_add">
                <?php if(!empty($_SESSION['login'])): ?>
                    <form method="POST" action="tchat.php">
                        <input type="text" name="new_mess" value="" placeholder="Entrez votre message">
                        <input type="submit" name="send" value="Send">
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section class="log_in">
        <?php if(empty($_SESSION['pseudo'])): ?>
            <form action="tchat.php" method="POST">
                <div>
                    <input type="text" name="login" value="" placeholder="Username"> </br>
                    <input type="password" name="password" value="" placeholder="Password"> </br>
                    <input type="submit" name="Log_in" value="JointheChat"> </br>
                </div>
            </form>
        <?php endif; ?>
                <div>
                    <a href="sign.php" class="link_sign">Not already registered?</a>
                    <img class="alice" src="images/alice.gif">
                    <img class="aliza" src="images/aliza.gif">
                </div>
    </section>
</body>
</html>
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
?>