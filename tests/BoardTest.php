<?php 
namespace Tests;

use PHPUnit\Framework\TestCase;
use Battleships\Board;

class BoardTest extends TestCase {

	private $board;
	private $empty;

	protected function setUp(): void
    {
        $this->board = new Board();

        $this->empty = array();
    	for ($i=0; $i < 10; $i++) { 
    		array_push($this->empty, array_fill(0, 10, 0));
    	}
    }

    private function findShip( int $id ): array {
    	$position = array();
    	$board = $this->board->getBoard();
    	for ($i=0; $i < $this->board->getSize()[1]; $i++) { 
    		$value = array_search($id, $board[$i],true);
    		if ($value !== false) {
    			$position = array($i,$value);
    			break;
    		}
    	}

    	return $position;
    }

    public function testGeneration() {

    	$this->board->generate(10,10);

    	$this->assertEquals($this->empty,$this->board->getBoard());

    }

    public function testGetSize() {

    	$this->board->generate(10,10);

    	$this->assertEquals(array(10,10),$this->board->getSize());

    }

    public function testGetBoardAndGetCleanBoard() {

    	$this->board->generate(10,10);

    	$this->assertEquals($this->empty,$this->board->getBoard());

    	$this->board->autoAddShip(5);
    	$this->assertNotEquals($this->empty,$this->board->getBoard());
    	$this->assertEquals($this->empty,$this->board->getCleanBoard());

    	$this->board->registerShot(array(0,0));
    	$this->assertNotEquals($this->empty,$this->board->getCleanBoard());

    }

    public function testRegisterShot() {

    	$this->board->generate(10,10);

    	$return = $this->board->registerShot(array(11,0));
    	$this->assertEquals($return,0);

    	$return = $this->board->registerShot(array(0,0));
    	$this->assertEquals($return,1);

    	$return = $this->board->registerShot(array(0,0));
    	$this->assertEquals($return,4);

    	$this->board->generate(10,10);
    	$this->board->autoAddShip(1);
    	$return = $this->board->registerShot($this->findShip(10));
    	$this->assertEquals($return,3);

    	$this->board->generate(10,10);
    	$this->board->autoAddShip(2);
    	$return = $this->board->registerShot($this->findShip(10));
    	$this->assertEquals($return,2);

    }

    public function testWinCondition() {

    	$this->board->generate(10,10);
    	$this->assertTrue($this->board->didIWin());

    	$this->board->autoAddShip(1);
    	$this->assertFalse($this->board->didIWin());

    	$this->board->registerShot($this->findShip(10));
    	$this->assertTrue($this->board->didIWin());

    }

    public function testGenerationXLimitMin() {

    	$this->expectException(\Exception::class);
    	$this->expectExceptionMessage('Invalid board size.');

    	$this->board->generate(9,10);

    }

    public function testGenerationXLimitMax() {

		$this->expectException(\Exception::class);
    	$this->expectExceptionMessage('Invalid board size.');

    	$this->board->generate(24,10);

    }

    public function testGenerationYLimit() {

    	$this->expectException(\Exception::class);
    	$this->expectExceptionMessage('Invalid board size.');

    	$this->board->generate(10,9);

    }

    public function testAutoAddShip() {

    	$this->board->generate(10,10);
    	$this->assertEquals($this->empty,$this->board->getBoard());

    	$this->assertTrue($this->board->autoAddShip(5));
    	$this->assertNotEquals($this->empty,$this->board->getBoard());

    	$this->board->generate(10,10);
    	$this->assertFalse($this->board->autoAddShip(50));
    	$this->assertEquals($this->empty,$this->board->getBoard());

    }

}