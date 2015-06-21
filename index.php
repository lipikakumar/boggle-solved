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
        $this->rootNode = new Node;
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

    echo $word.'<br>';

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

    echo $parent->isEndOfWord() ;

    }

    if ( $parent->isEndOfWord() ) {
      return 1;
    } else {
      return 2;
    }
  }
}



////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////

class Boggle {

    const WIDTH  = 4;
    const HEIGHT = 4;

    private $dictionary;
    private $board;
    private $solution;


    function __construct($dictionary) {
        $this->dictionary = $dictionary;
        $this->solution = array();

        $board = array();

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

         echo "Start Time: ".$start.'<br>';

        $solution  = array();
        $pathTaken = array(

            array('false', 'false', 'false', 'false'), 
            array('false', 'false', 'false', 'false'),
            array('false', 'false', 'false', 'false'),
            array('false', 'false', 'false', 'false'));


        foreach($this->board as $y => $row){    
            foreach($row as $x => $letter){
                self::traversePath($x, $y, '', $pathTaken);
            }
        }

      $timeTaken = round(microtime(true) * 1000) - $start;
      echo "\nTime taken: $timeTaken\n";

        return $this->$solution;
    }


    function traversePath($x, $y, $path, $pathTaken){


        // add letter to path
        $newPath = $path.$this->board[$y][$x];
        $pathTaken[$y][$x] = true;

         // search for path
         $searchResult = $this->dictionary->searchWord($newPath);

         echo '<br><br>';
        var_dump($searchResult);
         exit( $searchResult);
          
            if ( $searchResult == 0 ) {
                return;
            }

            if ( $searchResult == 1 ) {
                array_push($this->solution, $newPath);
            }



        for($rowOffset = -1; $rowOffset <= 1; $rowOffset++) {

            $newRow = $x + $rowOffset;
            if ( $newRow < 0 && $newRow < 4 ) {
              continue; 
            }

            for($colOffset = -1; $colOffset <= 1; $colOffset++) {

              $newCol = $y + $colOffset;

              if ( $newCol < 0 && $newCol < 4 ) {
                continue;
              }
                

              if ( $pathTaken[$newRow][$newCol] == false ) {
                echo $newPath;
                

                self::traversePath($newRow, $newCol, $newPath);
              }


            }

          }

          $selected[$row][$col] = false;
    }

}


   $dictionary = new Dictionary("wordlist.txt");
   echo 'Dictionary Complete<br>';
     $boggle     = new Boggle($dictionary);

    $solution   = $boggle->getBoardSolution();

    echo 'SOLUTION: ';
    print_r($solution);



?>
























