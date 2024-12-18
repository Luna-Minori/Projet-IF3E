<?php
session_start();
if (!isset($_SESSION['player_username'])) {
    header('Location: Login_user.php');
    exit();
}

$conn = new PDO('mysql:host=localhost;dbname=board_game_tournament', 'root', '');
$sql = "SELECT id FROM players WHERE username = :username";
$rep = $conn->prepare($sql);
$rep->bindParam(':username', $_SESSION['player_username'], PDO::PARAM_STR);
$rep->execute();
$_SESSION['player_id'] = $rep->fetchColumn();

$sql = "SELECT DISTINCT t.* FROM tournaments t LEFT JOIN player_tournaments pt ON t.id = pt.tournament_id LEFT JOIN team_tournaments tt ON t.id = tt.tournament_id LEFT JOIN players p ON p.id = pt.player_id LEFT JOIN player_teams ptm ON ptm.team_id = tt.team_id WHERE pt.player_id = :player_id OR ptm.player_id = :player_id";
$rep = $conn->prepare($sql);
$rep->bindParam(':player_id', $_SESSION['player_id'], PDO::PARAM_INT);
$rep->execute();
$Tournament_basedata = $rep->fetchAll(PDO::FETCH_ASSOC);
//var_dump($Tournament_basedata);

foreach ($Tournament_basedata as $T) {
    echo $T['participant'];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Manager</title>
    <link rel="stylesheet" href="Team_hub.css">
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
    <div class="content">
        <?php
        if (isset($_POST['tournament_id'])) {
            $conn = new PDO('mysql:host=localhost;dbname=board_game_tournament', 'root', '');
            $sql = "SELECT participant FROM tournaments WHERE id =:tournament_id";
            $rep = $conn->prepare($sql);
            $rep->bindParam(':tournament_id', $_POST['tournament_id'], PDO::PARAM_INT);
            $rep->execute();
            $Bool = $rep->fetch(PDO::FETCH_ASSOC);
            if ($Bool['participant'] == 1) {
                $sql = "SELECT COUNT(*) id FROM player_tournaments WHERE player_id = :player_id AND tournament_id = :tournament_id";
                $rep = $conn->prepare($sql);
                $rep->bindParam(':player_id', $_SESSION['player_id'], PDO::PARAM_INT);
                $rep->bindParam(':tournament_id', $_POST['tournament_id'], PDO::PARAM_INT);
                $rep->execute();
                $Bool = $rep->fetchColumn();
            } else {
                $sql = "SELECT COUNT(*) id FROM team_tournaments WHERE team_id = :team_id AND tournament_id = :tournament_id";
                $rep = $conn->prepare($sql);
                $rep->bindParam(':team_id', $_SESSION['player_id'], PDO::PARAM_INT);
                $rep->bindParam(':tournament_id', $_POST['tournament_id'], PDO::PARAM_INT);
                $rep->execute();
                $Bool = $rep->fetchColumn();
            }

            if ($Bool == 0) {
                $sql = "INSERT INTO tournament_request(player_id, tournament_id) VALUES (:player_id, :tournament_id)";
                $rep = $conn->prepare($sql);
                $rep->bindParam(':player_id', $_SESSION['player_id'], PDO::PARAM_INT);
                $rep->bindParam(':tournament_id', $_POST['tournament_id'], PDO::PARAM_INT);
                $rep->execute();
                $Basedata = $rep->fetchAll();
                echo "<div class='popup'><p>Your request has been sent </p></div>";
            } else {
                echo "<div class='popup'><p>Your are already in the tournament</p></div>";
            }
        }
        ?>
        <section class="Tournament_participate">
            <div class="information">
                <div class="Menu_info">
                    <div class="sub_Title">Tournament you participate</div>
                </div>
                <?php if (!empty($Tournament_basedata)): ?>
                    <div class="Tab">
                        <table>
                            <?php
                            $valid_columns_tournament = ['tournament_id', 'Name', 'creator_id', 'Match_system'];
                            $order_tournament_join = "ASC";
                            if (isset($_POST['order_tournament_join'])) {
                                if ($_POST['order_tournament_join'] === 'desc') {
                                    $order_tournament_join = 'DESC';
                                } else {
                                    $order_tournament_join = 'ASC';
                                }
                            }

                            if (isset($_POST['sort_tournament_join'])) {
                                if (in_array($_POST['sort_tournament_join'], $valid_columns_tournament)) {
                                    $sort_tournament_join = $_POST['sort_tournament_join'];
                                } else {
                                    $sort_tournament_join = "t.id";
                                }
                            } else {
                                $sort_tournament_join = "t.id";
                            }

                            $sql = "SELECT t.Name, t.id, t.creator_id, t.Creation_Date, t.game_id, t.Match_system, t.Register_time, t.participant FROM tournaments t LEFT JOIN player_tournaments pt ON pt.tournament_id = t.id INNER JOIN players p ON p.id = pt.player_id LEFT JOIN team_tournaments tt ON tt.tournament_id = t.id WHERE (pt.player_id = :player_id OR tt.team_id IN (SELECT team_id FROM player_teams WHERE player_id = :player_id))  AND (t.history = 0 OR t.history = 1) ORDER BY $sort_tournament_join $order_tournament_join";
                            $rep = $conn->prepare($sql);
                            $rep->bindParam(':player_id', $_SESSION['player_id'], PDO::PARAM_INT);
                            $rep->execute();
                            $Basedata = $rep->fetchAll();
                            ?>

                            <tr>
                                <th><a href="?sort_tournament_join=tournament_id&order=<?= $order_tournament_join === 'ASC' ? 'desc' : 'asc' ?>">ID</a></th>
                                <th><a href="?sort_tournament_join=Name&order=<?= $order_tournament_join === 'ASC' ? 'desc' : 'asc' ?>">Tournament Name</a></th>
                                <th><a href="?sort_tournament_join=participant&order=<?= $order_tournament_join === 'ASC' ? 'desc' : 'asc' ?>">Creator username</a></th>
                                <th><a href="?sort_tournament_join=Match_system&order=<?= $order_tournament_join === 'ASC' ? 'desc' : 'asc' ?>">Type of participant</a></th>
                                <th><a href="?sort_tournament_join=title&order=<?= $order_tournament_join === 'ASC' ? 'desc' : 'asc' ?>">Game</a></th>
                                <th><a href="?sort_tournament_join=Match_system&order=<?= $order_tournament_join === 'ASC' ? 'desc' : 'asc' ?>">Match system</a></th>
                                <th><a href="?sort_tournament_join=Register_time&order=<?= $order_tournament_join === 'ASC' ? 'desc' : 'asc' ?>">Register time</a></th>
                                <th>Profile</th>
                            </tr>

                            <?php foreach ($Basedata as $T): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($T['id']) ?></td>
                                    <td><?php echo htmlspecialchars($T['Name']) ?></td>
                                    <?php
                                    $sql = "SELECT username FROM players WHERE id =:player_id";
                                    $rep = $conn->prepare($sql);
                                    $rep->bindParam(':player_id', $T['creator_id'], PDO::PARAM_INT);
                                    $rep->execute();
                                    $username = $rep->fetchColumn();
                                    ?>
                                    <td><?php echo htmlspecialchars($username) ?></td>
                                    <td><?php
                                        if ($T['participant'] == 1) {
                                            echo "Singleplayer";
                                        }
                                        if ($T['participant'] == 2) {
                                            echo "Team";
                                        }
                                        ?></td>
                                    <td>
                                        <?php
                                        $sql = "SELECT g.title FROM games g INNER JOIN tournaments t ON t.game_id = g.id WHERE t.id = :tournament_id";
                                        $rep = $conn->prepare($sql);
                                        $rep->bindParam(':tournament_id', $T['id'], PDO::PARAM_INT);
                                        $rep->execute();
                                        $game = $rep->fetch();
                                        echo htmlspecialchars($game['title']);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($T['Match_system'] == 1): ?>
                                            <p> Elimnation rounds </p>
                                        <?php endif;
                                        if ($T['Match_system'] == 2): ?>
                                            <p> Swiss system </p>
                                        <?php endif;
                                        if ($T['Match_system'] == 3): ?>
                                            <p> League format </p>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $creation_date = new DateTime($T['Creation_Date']);
                                        $Register_time = $T['Register_time'];

                                        $End_date = $creation_date;
                                        $Between = new DateInterval('PT' . $Register_time . 'S');
                                        $End_date->add($Between);

                                        $Now = new DateTime();
                                        $Between = $Now->diff($End_date);

                                        if ($Now < $End_date) {
                                            $remaining_time = $Between->format('%a days %h Hours %i minutes');
                                        } else {
                                            $remaining_time = "Register close";
                                            $sql = "UPDATE tournaments SET History = 1 WHERE id = :tournament_id";
                                            $rep = $conn->prepare($sql);
                                            $rep->bindParam(':tournament_id', $T['id'], PDO::PARAM_INT);
                                            $rep->execute();
                                        }
                                        echo "<p>" . htmlspecialchars($remaining_time) . "</p>";
                                        ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="Tournament_management.php">
                                            <input type="submit" value="Tournament info" />
                                            <input type="hidden" name="tournament_id" value="<?= $T['id'] ?>" />
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="">Your are not registered in a tournament</div>
                <?php endif; ?>
            </div>
        </section>
        <section>
            <div class="Tournament_choices">
                <div class="Tab">
                    <table>
                        <?php
                        $valid_columns = ['tournament_id', 'tournament_Name', 'creator_id', 'title', 'Match_system', 'Register_time'];
                        $order = "ASC";
                        if (isset($_POST['order'])) {
                            if ($_POST['order'] === 'desc') {
                                $order = 'DESC';
                            } else {
                                $order = 'ASC';
                            }
                        }

                        if (isset($_POST['sort'])) {
                            if (in_array($_POST['sort'], $valid_columns)) {
                                $sort = $_POST['sort'];
                            } else {
                                $sort = "'team_id";
                            }
                        } else {
                            $sort = "team_id";
                        }

                        $sql = "SELECT DISTINCT t.*, p_creator.username AS creator_username FROM tournaments t INNER JOIN players p_creator ON t.creator_id = p_creator.id WHERE t.history = 0";
                        $rep = $conn->prepare($sql);
                        $rep->execute();
                        $Basedata = $rep->fetchAll();
                        ?>
                        <tr>
                            <th><a href="?sort=tournament_id&order=<?= $order === 'ASC' ? 'desc' : 'asc' ?>">ID </a></th>
                            <th><a href="?sort=tournament_Name&order=<?= $order === 'ASC' ? 'desc' : 'asc' ?>">Name </a></th>
                            <th><a href="?sort=creator_id&order=<?= $order === 'ASC' ? 'desc' : 'asc' ?>">Creator ID </a></th>
                            <th><a href="?sort=Match_system&order=<?= $order === 'ASC' ? 'desc' : 'asc' ?>">Creator username </a></th>
                            <th>Game</th>
                            <th><a href="?sort=Register_time&order=<?= $order === 'ASC' ? 'desc' : 'asc' ?>">Register Time </a></th>
                            <th>Profile</th>
                        </tr>
                        <?php foreach ($Basedata as $T): ?>
                            <tr>
                                <?php
                                echo "<td> " . htmlspecialchars($T['id']) . "</td>";
                                echo "<td> " . htmlspecialchars($T['Name']) . "</td>";
                                echo "<td> " . htmlspecialchars($T['creator_id']) . "</td>";
                                echo "<td> " . htmlspecialchars($T['creator_username']) . "</td>";

                                $sql = "SELECT title FROM games WHERE id = :id";
                                $rep = $conn->prepare($sql);
                                $rep->bindParam(':id', $T['game_id'], PDO::PARAM_INT);
                                $rep->execute();
                                $game = $rep->fetch();
                                echo "<td>" . htmlspecialchars($game['title']) . "</td>";

                                ?>
                                <td>
                                    <?php
                                    $creation_date = new DateTime($T['Creation_Date']);
                                    $Register_time = $T['Register_time'];

                                    $End_date = $creation_date;
                                    $Between = new DateInterval('PT' . $Register_time . 'S');
                                    $End_date->add($Between);

                                    $Now = new DateTime();
                                    $Between = $Now->diff($End_date);

                                    if ($Now < $End_date) {
                                        $remaining_time = $Between->format('%a days %h Hours %i minutes');
                                    } else {
                                        $remaining_time = "Register close";
                                        $sql = "UPDATE tournaments SET History = 1 WHERE id = :tournament_id";
                                        $rep = $conn->prepare($sql);
                                        $rep->bindParam(':tournament_id', $T['id'], PDO::PARAM_INT);
                                        $rep->execute();
                                    }
                                    echo "<p>" . htmlspecialchars($remaining_time) . "</p>";
                                    ?>
                                </td>
                                <?php
                                if ($T['participant'] == 2) {
                                    echo "<td><form method='POST' action='Team_tournament_request.php'>
                                <input type='submit' value='Ask to Join' />
                                <input type='hidden' name='tournament_id' value='" . $T['id'] . "' /> 
                             </form></td>";
                                } else {
                                    echo "<td><form method='POST' action='Tournament_hub.php'>
                                <input type='submit' value='Ask to Join' />
                                <input type='hidden' name='tournament_id' value='" . $T['id'] . "' /> 
                             </form></td>";
                                }
                                ?>
                            </tr> <?php endforeach; ?>
                        </tr>
                    </table>
                </div>
            </div>
        </section>
    </div>
</body>

</html>