<?php
namespace Battleships;

use Battleships\Board as Board;

class Game {

	private $board;
	private $columns;
	private $responses = array(
		"That shot is outside the board.",
		"That's a miss!",
		"That's a hit!",
		"You sunk my ship!",
		"You already shot there...",
		"victory" => "Thanks for playing.",
		"format" => "The coordinates must be provided in the following format: A1"
	);

	public function __construct(){
		$this->board = new Board();
	}

	public function generateBoard(): void {
		$ships = array(5,4,4);
		$this->board->generate(10,10);
		$this->columns = array_slice(range("A", "Z"),0,10);

		foreach($ships as $v) {
			$this->board->autoAddShip($v);
		}
	}

/* Probable usage *
	public loadBoard() {}
	public saveBoard() {}
/**/

	public function showBoard(): void {
		//$board = $this->board->getBoard(); # why not cheat?
		$board = $this->board->getCleanBoard();

		echo "   ".implode(" ", $this->columns).PHP_EOL;
		for ($i=0; $i < $this->board->getSize()[1]; $i++) { 
			echo sprintf("%'.2d %s \n",($i+1),implode(" ",$board[$i]));
		}
	}

	public function registerShot(string $input ): bool {
		if (!preg_match("/^[A-Z]{1}[0-9]{1,2}$/", $input)) {
			$this->respond('format');
			return false;
		}

		$coordinates = array(
			substr($input, 1)-1,
			array_search(substr($input, 0, 1), $this->columns)
		);

		$this->respond($this->board->registerShot($coordinates));

		if (!$this->board->didIWin()) return false;

		$this->respond('victory');
		return true;
	}

	private function respond(mixed $type): void { # poor mans language file implementation
		echo $this->responses[$type].PHP_EOL;
	}
}