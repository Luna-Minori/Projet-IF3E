<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Manager</title>
    <link rel="stylesheet" href="Main.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li class="logo_container">
                    <img class="logo" src="Image/logo.png">
                        </li>
                            <li class="deroulant_Main"><a href="#"> Players &ensp;</a>
                                <ul class="deroulant_Second">
                                   <li><a href="Login_user.php"> My Profile </a></li>
                                   <li><a href="Create_user.php"> Browse Players </a></li>
                                </ul>
                           </li>
                        <li class="deroulant_Main"><a href="#"> Teams &ensp;</a>
                            <ul class="deroulant_Second">
                                <li><a href="Team_hub.php"> My Teams </a></li>
                                <li><a> Join Teams </a></li>
                           </ul>
                        </li>
                        <li class="deroulant_Main"><a href="#"> Games &ensp;</a>
                                <ul class="deroulant_Second">
                                    <li><a href="Profile_user.php"> Add game </a></li>
                                    <li><a> Browse games </a></li>
                                </ul>
                        <li class="deroulant_Main"><a href="#"> Tournaments &ensp;</a>
                                <ul class="deroulant_Second">
                                    <li><a> My tournaments </a></li>
                                    <li><a> Join tournament </a></li>
                                    <li><a> Browse tournaments </a></li>    
                                </ul>
                            </li>
                         </li>
                    </ul>
                </nav>
                <div class = "content">
                    <section id="welcome">
                        <h2>WELCOME TO GAME ARENA</h2>
                        <p>Manage your tournaments, teams, and players all in one place!</p>
                    </section>
                    <section id="tournaments-info">
                        <h2>Upcoming Tournaments</h2> 
                            <table id="upcoming-tournaments">
                                <tr>
                                    <th> Name </th>
                                    <th> Game </th>
                                    <th> Date </th>
                                    <th> Location </th>
                                </tr>
                                <tr>
                                    <td> Summer Championship 2024 </td>
                                    <td> Chess </td>
                                    <td> September 20, 2024 </td>
                                    <td> Virtual Arena </td>
                                </tr>
                                <tr>
                                    <td> Winter Championship 2024 </td>
                                    <td> Uno </td>
                                    <td> December 20, 2024 </td>
                                    <td> Virtual Arena </td>
                                </tr>
                                <tr>
                                    <td> Spring Championship 2025 </td>
                                    <td> Poker </td>
                                    <td> March 20, 2025 </td>
                                    <td> Virtual Arena </td>
                                </tr>
                    </section>
                </div>
            </ul>
        </nav>
    </header>
</body>
</html>