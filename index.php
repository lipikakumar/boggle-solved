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

    const WIDTH  = 4;
    const HEIGHT = 4;

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

        //$board = array();
        // for($y=0; $y<self::HEIGHT; $y++){
        //     $row = array();
        //     for($x=0; $x<self::WIDTH; $x++){
        //         array_push($row, self::getRandomLetter());
        //     }
        //     array_push($board, $row);  
        // }

         $this->board = array(
                 array('c', 'l', 'r', 'i'),
                 array('p', 'a', 'y', 'a'),
                 array('t', 'p', 't', 'b'),
                 array('i', 'l', 'n', 'i'),
             );

      }

    function getRandomLetter(){
        $alphabetList = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $letterIndex  = rand(0, 25);

        return $alphabetList[$letterIndex];
    }

    function getBoardSolution(){

        $start = round(microtime(true) * 1000); 

         //echo "Start Time: ".$start.'<br>';
        foreach($this->board as $y => $row){    
            foreach($row as $x => $letter){
                self::traversePath($x, $y, '', $this->pathTaken);
            }
        }

      $timeTaken = round(microtime(true) * 1000) - $start;
      //echo "\nTime taken: $timeTaken\n";

        return $this->solution;
    }

    function traversePath($x, $y, $path){
        //  echo '('.$x.','.$y.')';
        // echo "\n";

        // add letter to path
        // echo 'old path: '.$path."\n";

        $newPath = $path.$this->board[$x][$y];
        $this->pathTaken[$x][$y] = true;

         //echo 'new path to search: '.$newPath."\n";
         $searchResult = $this->dictionary->searchWord($newPath);

        // echo 'new path: '.$searchResult;
        if ( $searchResult == 0 ) {
            $this->pathTaken[$x][$y] = false;
            return;
        }

        if ( $searchResult == 1 ) {
            // echo 'word found: '.$newPath."\n";
            array_push($this->solution, $newPath);
        }    

        for($rowOffset = -1; $rowOffset <= 1; $rowOffset++) {
            $newRow = $x + $rowOffset;
            //echo 'new row: '. $newRow."\n";
            if ( $newRow < 0 || $newRow >= 4 ) {
              continue; 
            }
            for($colOffset = -1; $colOffset <= 1; $colOffset++) {
              $newCol = $y + $colOffset;
               //echo 'new col: '. $newCol."\n";
              if ( $newCol < 0 || $newCol >= 4 ) {
                // echo 'continue';
                continue;
              }
              if ( $this->pathTaken[$newRow][$newCol] == false ) {

                // var_dump($this->pathTaken);
              // echo 'call traversal method again';

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
























