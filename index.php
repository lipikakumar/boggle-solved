<?php

class Node {
  private $eow;  // end of word
  private $children; // Array that is actually a map of a char to one child node

  function __construct() {
    $this->eow = false;
    $this->children = null;
  }

  function addChild($char) {
    if ( $this->children == null ) {
      $this->children = array();
    }
    return ($this->children[$char] = new Node());  
  }

  function markEndOfWord() {
    $this->eow = true;
  }

  function isEndOfWord() {
    return $this->eow;
  }

  function getChild($char) {
    if ( $this->children == null ) {
      return null;
    }

    if(array_key_exists($char,  $this->children)){
        return $this->children[$char];
    }

  }
}

class Dictionary {

  private $rootNode;
    
    function __construct($wordsFileName) {
        $this->rootNode = new Node();
        $words = file($wordsFileName, FILE_IGNORE_NEW_LINES);
        foreach ( $words as $word ) {
          $this->addWord($word);
        }
  }
    
    public function addWord($word) {
        $len = strlen($word);
        if ( $len == 0 ) {
          return;
        }
        $parent = $this->rootNode; 

        for( $i=0; $i < $len; $i++) {

          $ch = $word[$i]; 
          $node = $parent->getChild($ch);
          if ( $node == null ) { 
            $node = $parent->addChild($ch);
          }
          $parent = $node;
        }
        
        // we covered all chars of this word, just mark the end of word in the 
        // leaf node
        $parent->markEndOfWord();
  }

 /*
   * Return 
   *   0 -> Not a Valid word, and not a valid start
   *   1 -> Valid Word
   *   2 -> Valid So far but word not completed yet
   */

    public function searchWord($word) {

        $len = strlen($word);
        if ( $len == 0 ) {
            return 0;
        }
        $parent = $this->rootNode; 
        for( $i=0; $i < $len; $i++) {
        $ch = $word[$i]; 
        $node = $parent->getChild($ch); 
        if ( $node == null ) { 
        return 0;
        }
        $parent = $node;
        }
        if ( $parent->isEndOfWord() ) {
        //echo "Found word: ".$word."\n";
        return 1;
        } else {
        // Valid start of some word(s)        
        return 2;
        }
    }

}

class Boggle {

    private $dictionary;
    private $board;
    private $solution;
    private $pathTaken;

    function __construct($dictionary) {
        $this->dictionary = $dictionary;
        $this->solution = array();

        $this->pathTaken = array();
        for($i=0; $i<4; $i++){
            for($j=0; $j<4; $j++){
                  $this->pathTaken[$i][$j] = false;  
            }
        }
        $this->board = self::getBoard();
      }

    function getBoard(){
        $boggleBoard = array();
        for($x=0; $x<4; $x++){
            for($y=0; $y<4; $y++){
                $boggleBoard[$x][$y] = self::getRandomLetter();
            }  
        }

        return $boggleBoard;
      }

    function getRandomLetter(){
        $alphabetList = 'abcdefghijklmnopqrstuvwxyz';
        $letterIndex  = rand(0, 25);

        return $alphabetList[$letterIndex];
    }

    function getBoardSolution(){

        $start = round(microtime(true) * 1000); 
        foreach($this->board as $y => $row){    
            foreach($row as $x => $letter){
                self::traversePath($x, $y, '', $this->pathTaken);
            }
        }

        $timeTaken = round(microtime(true) * 1000) - $start;
        return $this->solution;
    }

    function traversePath($x, $y, $path){

        $newPath = $path.$this->board[$x][$y];
        $this->pathTaken[$x][$y] = true;

        $searchResult = $this->dictionary->searchWord($newPath);

        if ( $searchResult == 0 ) {
            $this->pathTaken[$x][$y] = false;
            return;
        }

        if ( $searchResult == 1 ) {
            array_push($this->solution, $newPath);
        }    

        for($rowOffset = -1; $rowOffset <= 1; $rowOffset++) {
            $newRow = $x + $rowOffset;
            if ( $newRow < 0 || $newRow >= 4 ) {
              continue; 
            }
            for($colOffset = -1; $colOffset <= 1; $colOffset++) {
              $newCol = $y + $colOffset;
              if ( $newCol < 0 || $newCol >= 4 ) {
                continue;
              }
              if ( $this->pathTaken[$newRow][$newCol] == false ) {
                self::traversePath($newRow, $newCol, $newPath);
              }

            }

          }

        $this->pathTaken[$x][$y] = false;
    }

}
    $dictionary = new Dictionary("wordlist.txt");
    echo "Dictionary Complete\n";
    $boggle     = new Boggle($dictionary);
    $solution   = $boggle->getBoardSolution();
    echo 'SOLUTION: ';
    print_r($solution);
?>
























