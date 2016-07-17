<?php

require_once('config.php');

$db = sqlClass::get_DB_instance();
$handler = $db->connect_db('basketball');

$sql = "SELECT match_id, match_datetime, team1_id, team1.team_name as team1_team_name, team2_id, team2.team_name as team2_team_name, score_final, score, t.tournament_id, tournament_name FROM `match` LEFT JOIN `team` as team1 ON `match`.team1_id = team1.team_id LEFT JOIN `team` as team2 ON `match`.team2_id = team2.team_id LEFT JOIN `tournament` as t ON `match`.tournament_id = t.tournament_id WHERE team1.team_id = '35546' or team2.team_id = '35546' ORDER BY match_datetime DESC";

$row = $db->exec_query($handler, $sql);

debugr_show($row);