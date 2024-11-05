<?php
    session_start();
    
    if (!isset($_SESSION['username'])) {
        header('Location: Login_user.php');
        exit();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $title = $_POST['title'];
        $password = $_POST['password'];
        $game = $_POST['game'];

        $conn = new PDO('mysql:host=localhost;dbname=board_game_tournament', 'root', '');
        $sql = "SELECT hashed_password FROM players WHERE username = :username";
        $rep = $conn->prepare($sql);
        $rep->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
        $rep->execute();
        $Hpassword = $rep->fetchColumn();

        if ($Hpassword && password_verify($password, $Hpassword)) {

            $conn = new PDO('mysql:host=localhost;dbname=board_game_tournament', 'root', '');
            $sql = "SELECT COUNT(*) FROM teams WHERE title = :title";
            $rep = $conn->prepare($sql);
            $rep->bindParam(':title', $title, PDO::PARAM_STR);
            $rep->execute();
            $number = $rep->fetchColumn();
            echo $number;
            if($number == 0){
                $sql = "INSERT INTO teams (title, creator_id, game_id) VALUES (:title, (SELECT id FROM players WHERE username = :username), :game)";
                $rep = $conn->prepare($sql);
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $rep->bindParam(':title', $title, PDO::PARAM_STR);
                $rep->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
                $rep->bindParam(':game', $game, PDO::PARAM_STR);
                $rep->execute();

                header('Location: Team_hub.php');
                exit();
            }
            else {
                echo "This name is already used";
            }
        }
        else {
        echo "Invalid password";
        }
    }
?>
    
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Manager</title>
    <link rel="stylesheet" href="Create_team.css">
    <script>
        function Create_user() {
            // Redirige vers le script PHP
            window.location.href = 'Create_user.php'; // Changez 'traitement.php' pour votre script
        }
    </script>
</head>
<body>
<header>
        <nav>
            <div class="Title_nav">
                <h1>Tournament Manager</h1>
            </div>
            <ul>
                <li class="deroulant_Main"><a href="#"> Creation &ensp;</a>
                    <ul class="deroulant_Second">
                        <li><a href="Create_user.php"> Account creation </a></li>
                        <li><a href="Create_user.php"> Team creation </a></li>
                        <li><a href="Create_user.php"> Tournament creation </a></li>
                    </ul>
                </li>
                
                <li class="deroulant_Main"><a href="#"> Creation of &ensp;</a>
                        <ul class="deroulant_Second">
                            <li><a> Account creation </a></li>
                            <li><a> Team creation </a></li>
                            <li><a> Tournament creation </a></li>
                        </ul>
                 </li>
            </ul>
        </nav>
    </header>
    <?php
    session_start();
    /*
    try {

        $conn = new PDO('mysql:host=localhost;dbname=board_game_tournament', 'root', '');
        $sql = "SELECT * FROM board_game_tournament";
        $stmt = $conn->prepare($sql);
        $result = $stmt->fetchAll();
    } 
    catch (PDOException $e) { 
        echo 'Erreur : ' . $e->getMessage();
    }


    $sql = "SELECT username FROM players";
    $rep = $conn->prepare($sql);
    $rep->execute();
    $result = $rep->fetchAll();
    print_r($result);
    echo $result["username"];
    */
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = $_POST['username'];
        echo "Boujour " . htmlspecialchars($username);

    }
    ?>
    <br>

    <form method="post" action="Creation_Team.php">
            <p> Name of the team </p>
            <input type="text" name="nom" size="12" required>
            <br>
            <p> game </p>
            <select required>
            <option value="Osu">Osu</option>
            <option value="Mc">Mc</option>
            <option value="Apex">Apex</option>
            <option value="Célantix">Célantix</option>
            </select>

            <br>
            <p> Team member </p>
            <input type="text" name="Team_member" size="12" required>
            <br>
            <p>Description</p>
            <input type="Text" name="Description" size="12">
            <br>
            <p> Accepter vous les conditions générale utilisation de Tournament Manager</p>
            <input type="radio" name="eail" size="12">
            <br>
            <input type="submit" value="OK">
            <input type="reset" value="Reset">

        <a href=Main.php> Retour Main</a>
    </body>

</html>