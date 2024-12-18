<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    echo $_POST['email'];

    $conn = new PDO('mysql:host=localhost;dbname=board_game_tournament', 'root', '');
    $sql = "SELECT * FROM players";
    $rep = $conn->prepare($sql);
    $rep->execute();

    $bool = false;
    while ($Basedata = $rep->fetch(PDO::FETCH_ASSOC)) {
        echo $Basedata['username'];
        echo $username;
        if ($Basedata['username'] == $username) {
            echo "this username is already use";
            $bool = true;
            exit();
        }
        if ($email == $Basedata['email']) {
            echo "this email is already use";
            $bool = true;
            exit();
        }
    }
    if ($bool == false) {

        $sql = "INSERT INTO players(username, email, hashed_password, bio) VALUES (:username,:email,:hashed_password,:bio)";
        $rep = $conn->prepare($sql);
        $rep->bindParam(':username', $username, PDO::PARAM_STR);
        $rep->bindParam(':email', $email, PDO::PARAM_STR);
        $rep->bindParam(':hashed_password', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);
        $rep->bindParam(':bio', $bio, PDO::PARAM_STR);
        $rep->execute();

        $_SESSION['username'] = $username;
        $_SESSION['hashed_password'] = $password;
        $_SESSION['email'] = $email;
        $_SESSION['bio'] = $bio;
        header('Location: Profile_user.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Manager</title>
    <link rel="stylesheet" href="Create.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li class="logo_container">
                    <a href="Main.php"><img class="logo" src="Image/logo.png"></a>
                </li>
                <li class="deroulant_Main"><a href="#"> Players &ensp;</a>
                    <ul class="deroulant_Second">
                        <li><a href="Login_user.php"> Log in </a></li>
                        <li><a href="Profile_user.php"> My Profile </a></li>
                        <li><a href="Create_user.php"> Browse Players </a></li>
                        <li><a href="Log_out.php"> Log Out </a></li>
                    </ul>
                </li>
                <li class="deroulant_Main"><a href="#"> Teams &ensp;</a>
                    <ul class="deroulant_Second">
                        <li><a href="Team_hub.php"> My Teams </a></li>
                        <li><a href="Team_hub.php"> Join Teams </a></li>
                        <li><a href="Create_team.php"> Create Team </a></li>
                    </ul>
                </li>

                <li class="deroulant_Main"><a href="#"> Tournaments &ensp;</a>
                    <ul class="deroulant_Second">
                        <li><a href="Tournament_hub.php"> My tournaments </a></li>
                        <li><a href="Tournament_hub.php"> Join tournament </a></li>
                        <li><a href="Create_tournament.php"> Browse tournaments </a></li>
                    </ul>
                </li>
                <li class="deroulant_Main"><a href="#"> Games &ensp;</a>
                    <ul class="deroulant_Second">
                        <li><a href="Profile_user.php"> Add Games </a></li>
                        <li><a href="Profile_game.php"> Games Stats </a></li>
                    </ul>
                </li>
                </li>
            </ul>
        </nav>
    </header>
    <section>
        <main>
            <div class="Create">
                <form method="post" action="Create_user.php">
                    <div class="bo">
                        <h2 class="Title_form">Account Creation</h2>
                        <div class="text_form">
                            <br>
                            <div class="arena_text">
                                <input class="left-space" type="text" id="username" name="username" size="12" required>
                                <label>Username</label>
                                <span>Username</span>
                            </div>
                            <div class="arena_text">
                                <input class="left-space" type="text" name="password" size="12" required>
                                <label>Password</label>
                                <span>Password</span>
                            </div>
                            <div class="arena_text">
                                <input class="left-space" type="email" name="email" size="12" required>
                                <label>email</label>
                                <span>email</span>
                            </div>
                            <input class="button" type="submit" name="condition" value="Creation" value="1" required>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </main>
    </section>
</body>

</html>