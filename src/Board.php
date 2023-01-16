<?php
namespace Battleships;

class Board {

	private $board = array();
	private $ships = array();
	private $size = array();

	public function generate( int $sizeX, int $sizeY ): void {
		if ($sizeX < 10 || $sizeX > 23) throw new \Exception('Invalid board size.');
		if ($sizeY < 10) throw new \Exception('Invalid board size.');

		$this->size = [$sizeX,$sizeY];

		$this->board = array();
		for ($i=0;$i<$sizeY;$i++) {
			array_push($this->board, array_pad(array(), $sizeX, 0));
		}

		$this->ships = array();
	}

	public function registerShot(array $position): int {
		if (!isset($this->board[$position[0]][$position[1]])) return 0;
		$field_value = $this->board[$position[0]][$position[1]];

		switch (true) {
			case $field_value === 0:
				$this->board[$position[0]][$position[1]] = 1;
				return 1;
			case $field_value >= 10:
				$this->ships[$field_value] -= 1;
				$this->board[$position[0]][$position[1]] = 2;

				if ($this->ships[$field_value] === 0) {
					unset($this->ships[$field_value]);
					return 3;
				}

				return 2;
			default:
				return 4;
		}		
	}

	public function autoAddShip(int $size ): bool {

		$board_size_limit = $this->size[0]*$this->size[1]; # just to make sure we don't loop forever

		while ($board_size_limit > 0) {
			$directions = array(0,1,2,3);
			$position = array(rand(0,$this->size[0]-1),rand(0,$this->size[1]-1));

			if (($this->board[$position[0]][$position[1]] != 0)) {
				$board_size_limit--;
				continue;
			}

			while (count($directions) > 0) {

				$direction_pointer = array_rand($directions);

				if (!$this->checkShipPlacement($size,$position,$direction_pointer)) {
					unset($directions[$direction_pointer]);
					continue;
				}

				$this->placeShip($size,$position,$direction_pointer);
				return true;
			}
			$board_size_limit--;
		}

		return false;
	}

	public function getSize(): array {
		return $this->size;
	}

	public function getBoard(): array {
		return $this->board;
	}

	public function getCleanBoard(): array {
		$board = $this->board;
		for ($i=0; $i < $this->size[1]; $i++) { 
			array_walk($board[$i], function (&$v) { if ($v >= 10) $v = 0; } );
		}

		return $board;
	}

	public function didIWin(): bool {
		if (count($this->ships) === 0) return true;
		return false;
	}

	private function placeShip(int $size, array $position, int $direction): void {
		$id = 10+count($this->ships);
		switch ($direction) {
			case 0: # Y asc
				for ($i=0;$i<$size;$i++) {
					$this->board[$position[0]][$position[1]+$i] = $id;
				}
				break;
			case 1: # Y desc
				for ($i=0;$i<$size;$i++) {
					$this->board[$position[0]][$position[1]-$i] = $id;
				}
				break;
			case 2: # X asc
				for ($i=0;$i<$size;$i++) {
					$this->board[$position[0]+$i][$position[1]] = $id;
				}
				break;
			case 3: # X desc
				for ($i=0;$i<$size;$i++) {
					$this->board[$position[0]-$i][$position[1]] = $id;
				}
				break;
		}
		$this->ships[$id] = $size;
	}

	private function checkShipPlacement(int $size, array $position, int $direction = 0 ): bool {

		if (!($this->board[$position[0]][$position[1]] === 0)) return false;

		switch ($direction) {
			case 0: # Y asc
				if (($position[1]+$size) >= $this->size[1]) return false;
				for ($i=0;$i<$size;$i++) {
					if (!($this->board[$position[0]][$position[1]+$i] === 0)) return false;
				}
				break;
			case 1: # Y desc
				if (($position[1]-$size) <= $this->size[1]) return false;
				for ($i=0;$i<$size;$i++) {
					if (!($this->board[$position[0]][$position[1]-$i] === 0)) return false;
				}
				break;
			case 2: # X asc
				if (($position[0]+$size) >= $this->size[0]) return false;
				for ($i=0;$i<$size;$i++) {
					if (!($this->board[$position[0]+$i][$position[1]] === 0)) return false;
				}
				break;
			case 3: # X desc
				if (($position[0]-$size) <= $this->size[0]) return false;
				for ($i=0;$i<$size;$i++) {
					if (!($this->board[$position[0]-$i][$position[1]] === 0)) return false;
				}
				break;
			default:
				throw new Exception('Invalid direction provided');
				break;
		}

		return true;
	}

}