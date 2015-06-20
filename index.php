<?php

     const BOARD_WIDTH  = 4;
     const BOARD_HEIGHT = 4;

    function getBoggleBoard(){

         $board = array(
                 array('C', 'L', 'R', 'I'),
                 array('P', 'A', 'Y', 'A'),
                 array('T', 'P', 'T', 'B'),
                 array('I', 'L', 'N', 'I'),
             );
        
        return $board;
    }

    function getRandomLetter(){
        $alphabetList = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $letterIndex  = rand(0, 25);

        return $alphabetList[$letterIndex];
    }

    function getWordList(){
        //http://www-personal.umich.edu/~jlawler/wordlist
        return array('cat' => 1, 'bin'=> 1, 'cater'=> 1);
    }

    function isWordValid($word, $wordList){
        return array_key_exists($word, $wordList);
    }

    function getBoardSolution($board){
        $listOfValidWords = getWordList();

        //step through each board piece, starting with each board piece
        foreach($board as $y => $row){
            $selectedList = array();
            foreach($row as $x => $letter){


            //     N 
            // W       E
            //     S

            // traverse all paths from current letter
            // make move

             // $board = array(
             //         array('C', 'L', 'R', 'I'),
             //         array('P', 'A', 'Y', 'A'),
             //         array('T', 'P', 'T', 'B'),
             //         array('I', 'L', 'N', 'I'),
             //     );
        
            }
        }

        //return $listOfValidWords;
    }

    $board    = getBoggleBoard();
    $solution = getBoardSolution($board);

    print_r($solution);

?>
