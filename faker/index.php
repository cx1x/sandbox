<?php
require __DIR__ . '/../vendor/autoload.php';

use Lib\Config;
use Lib\Sql;
use Lib\Helper;


$faker = Faker\Factory::create();

echo "<pre>";
echo $faker->name . "<br />";
echo $faker->address . "<br />";
echo $faker->text . "<br />";
echo "</pre>";




$ini = new Config($_SERVER['DOCUMENT_ROOT'] . '/../wlsconfig.ini');

//$config->get_env();
//$config->get_config();
$db = Sql::get_DB_instance();
$handler = $db->connect_db($ini, 'basketball');
$sql = "SELECT match_id, match_datetime, team1_id, team1.team_name as team1_team_name, team2_id, team2.team_name as team2_team_name, score_final, score, t.tournament_id, tournament_name FROM `match` LEFT JOIN `team` as team1 ON `match`.team1_id = team1.team_id LEFT JOIN `team` as team2 ON `match`.team2_id = team2.team_id LEFT JOIN `tournament` as t ON `match`.tournament_id = t.tournament_id WHERE team1.team_id = '35546' or team2.team_id = '35546' ORDER BY match_datetime DESC";

$row = $db->exec_query($handler, $sql);

Helper::debug($row);