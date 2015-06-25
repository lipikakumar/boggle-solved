<?php

class Node
{
    private $isEndOfWord;
    private $children;
    
    public function __construct()
    {
        $this->isEndOfWord      = false;
        $this->children = null;
    }
    
    public function addChild($char)
    {
        if ($this->children == null) {
            $this->children = array();
        }
        return ($this->children[$char] = new Node());
    }
    
    public function markEndOfWord()
    {
        $this->isEndOfWord = true;
    }
    
    public function isEndOfWord()
    {
        return $this->isEndOfWord;
    }
    
    public function getChild($char)
    {
        if ($this->children == null) {
            return null;
        }
        
        if (array_key_exists($char, $this->children)) {
            return $this->children[$char];
        }
    }
}

class Dictionary
{
    
    private $rootNode;
    
    public function __construct($wordsFileName)
    {
        $this->rootNode = new Node();
        $words          = file($wordsFileName, FILE_IGNORE_NEW_LINES);
        foreach ($words as $word) {
            $this->addWord($word);
        }
    }
    
    public function addWord($word)
    {
        $len = strlen($word);
        if ($len == 0) {
            return;
        }
        $parent = $this->rootNode;
        
        for ($i = 0; $i < $len; $i++) {
            
            $ch   = $word[$i];
            $node = $parent->getChild($ch);
            if ($node == null) {
                $node = $parent->addChild($ch);
            }
            $parent = $node;
        }
        
        $parent->markEndOfWord();
    }
    
    public function searchWord($word)
    {
        
        $len = strlen($word);
        if ($len == 0) {
            return 0;
        }
        
        $parent = $this->rootNode;
        
        for ($i = 0; $i < $len; $i++) {
            $ch   = $word[$i];
            $node = $parent->getChild($ch);
            
            if ($node == null) {
                return 0;
            }
            $parent = $node;
        }
        
        if ($parent->isEndOfWord()) {
            return 1;
        } else {
            return 2;
        }
        
    }
    
}

class Boggle
{
    
    private $dictionary;
    private $board;
    private $solution;
    private $pathTaken;
    private $solutionTime;

    const HEIGHT = 4;
    const WIDTH = 4;   

    const WORD_FOUND = 1;
    const WORD_NOT_FOUND = 0;   

    public function __construct($dictionary)
    {        
        $this->dictionary = $dictionary;
        $this->solution   = array();
        $this->solutionTime = 0;
        
        $this->pathTaken = array();
        for ($i = 0; $i < self::WIDTH; $i++) {
            for ($j = 0; $j < self::HEIGHT; $j++) {
                $this->pathTaken[$i][$j] = false;
            }
        }
        $this->board = self::createRandomBoard();
    }
    
    private function createRandomBoard()
    {
        $boggleBoard = array();
        for ($x = 0; $x < self::WIDTH; $x++) {
            for ($y = 0; $y < self::HEIGHT; $y++) {
                $boggleBoard[$x][$y] = self::getRandomLetter();
            }
        }
        return $boggleBoard;
    }
    
    private function getRandomLetter()
    {
        $alphabetList = 'abcdefghijklmnopqrstuvwxyz';
        $letterIndex  = rand(0, 25);
        
        return $alphabetList[$letterIndex];
    }
    
    public function solveBoggle()
    {
        $startTime = microtime(true);
        foreach ($this->board as $y => $row) {
            foreach ($row as $x => $letter) {
                self::traversePath($x, $y, '');
            }
        }
        $this->solutionTime = microtime(true) - $startTime;
    }
    
    private function traversePath($x, $y, $path)
    {
        $newPath                 = $path . $this->board[$x][$y];
        $this->pathTaken[$x][$y] = true;
        
        $searchResult = $this->dictionary->searchWord($newPath);
        
        if ($searchResult == self::WORD_NOT_FOUND) {
            $this->pathTaken[$x][$y] = false;
            return;
        }
        
        if ($searchResult == self::WORD_FOUND) {
            $this->solution[$newPath] = self::WORD_FOUND;
        }
        
        for ($rowOffset = -1; $rowOffset <= 1; $rowOffset++) {
            $newRow = $x + $rowOffset;
            if ($newRow < 0 || $newRow >= self::WIDTH) {
                continue;
            }
            for ($colOffset = -1; $colOffset <= 1; $colOffset++) {
                $newCol = $y + $colOffset;
                if ($newCol < 0 || $newCol >= self::HEIGHT) {
                    continue;
                }
                if ($this->pathTaken[$newRow][$newCol] == false) {
                    self::traversePath($newRow, $newCol, $newPath);
                }
                
            }
            
        }
        
        $this->pathTaken[$x][$y] = false;
    }
    
    public function printBoard()
    {
        $board = "--Board--------\n";
        foreach ($this->board as $row => $values) {
            foreach ($values as $key => $letter) {
                $board .= $letter . " ";
            }
            $board .= "\n";
        }
        $board .= "---------------\n";
        echo $board;
    }

    public function printSolution()
    {
        $solution = "--Solution------\n";
        $validWords = array_keys($this->solution);
        foreach($validWords as $key => $value){
            $solution .= $value ."\n";
        }
        $solution .= "---------------\n";
        $solution .= "Solution time: ".$this->solutionTime." sec\n";
        $solution .= "---------------\n";
        echo $solution;
    }   
}
///////////////////////////////////////////////////

$wordListFilePath = "wordlist.txt";

$dictionary = new Dictionary($wordListFilePath);
$boggle     = new Boggle($dictionary);

$boggle->printBoard();
$boggle->solveBoggle();
$boggle->printSolution();

?>