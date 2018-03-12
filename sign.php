<!-- ================================================= PHP ================================================================ -->

<?php

// =============================================== Connexion SQL =============================================================

try 
{
    $db = new PDO('mysql:host=localhost;dbname=tchat;charset=utf8', 'root', 'user');
}
catch(Exception $erreur) {
    die('Erreur: ' .$erreur->getMessage());
}

//Hashing Password

// $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Add New User

// if(isset($_POST["password"]) && ($_POST["password2"])) // verification que les champs mdp sont remplit
// {
//     $password=$_POST["password"];
//     add_user($password);
// }

// if (isset($_POST['login'])) {
//     echo filter_var($_POST['login'], FILTER_SANITIZE_STRING);
//     echo "<br/><br/>";
// }

// if (isset($_POST['password'])) {
//     echo filter_var($_POST['password'], FILTER_SANITIZE_STRING);
//     echo "<br/><br/>";
// }

// if (isset($_POST['email'])) {
//     echo filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
//     echo "<br/><br/>";
// }


// ================================================ Verification de doublons ==================================================

if(isset($_POST['submit']) && !empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['email']))
{
    $login = htmlspecialchars($_POST['login']);
//==================================================== Hashing Password =======================================================
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $mail = htmlspecialchars($_POST['email']);
    global $bdd;

    $Verif_mail = $db->prepare('SELECT * FROM users WHERE email = ?');
            $Verif_mail->execute(array($email));
            $Already_use_email = $Verif_mail->rowcount();

    $Verif_login = $db->prepare('SELECT * FROM users WHERE login = ?');
            $Verif_login->execute(array($login));
            $already_use_login = $Verif_login->rowcount();

    if($already_use_login == 0)
    {
        if($Already_use_email == 0)
        {
            $add_user= $db->prepare("INSERT INTO users (login, password, email) VALUES ('".$login."', '".$password_hash."','".$email."')");
            $add_user->execute(array(
                "login" => $login, 
                "password" => $password_hash, 
                "email" => $email));
                header('location: index.php');
        }       
        else 
        {
            $erreur = "Votre email existe déjà!";
        }
    }
    else 
    {
        $erreur = "Ce login est déjà utlisé!";
    }      
}
else
{
$erreur = "Tous les champs ne sont pas remplit!";
}                 

?>

<!-- ==================================================== HTML ======================================================== -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link id="pagestyle" href="css/sign.css" rel="stylesheet">
    <link rel="stylesheet" media="screen" href="https://fontlibrary.org/face/grundschrift" type="text/css"/> 
<body>
    <img src="images/corner2.png">
        <div class="log_in">
            <h1>Sign-in</h1>
            <form method="post" action="sign.php">
                Login
                <input type="text" name="login" placeholder="Username"> </br>
                Password
                <input type="password" name="password" placeholder="Password"> </br>
                Email
                <input type="text" name="email" placeholder="Email"> </br>
                <input type="submit" name="submit" value="S'inscrire">
            </form>
            <div class="conditions">
            Rules and Conditions
        </div>
    </div>
</body>