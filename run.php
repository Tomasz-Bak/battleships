<?php
require __DIR__ . '/vendor/autoload.php';

use Battleships\Game as Game;

$game = new Game();

$game->generateBoard();

$game->showBoard();
$selection = readline("Where would You like to shoot?: ");

while (!$game->registerShot($selection)) {
	$game->showBoard();
	$selection = readline("Where would You like to shoot?: ");
}