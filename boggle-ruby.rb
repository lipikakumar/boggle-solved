
class Node
  @isEndOfWord
  @children

  public 
  def initialize
    @isEndOfWord = false
    @children = nil
  end

  def addChild(char)
    if @children == nil
      @children = Hash.new
    end

    @children[char] = Node.new
    return @children[char]
  end

  def markEndOfWord
    @isEndOfWord = true
  end

  def isEndOfWord
    @isEndOfWord\
  end

  def getChild(char)
    if @children == nil
      return nil
    end
    if @children[char] == nil
      return nil
    else
      return @children[char]
    end
  end
end

class Dictionary
  @rootNode
  WORD_NOT_FOUND = 0
  WORD_FOUND     = 1
  PREFIX_FOUND   = 2

  public
  def initialize(wordsFileName) 
    @rootNode = Node.new
    aFile = File.new(wordsFileName, "r")
    aFile.each_line {|line|
    word = line.to_s.strip
    addWord(word)
   }
    aFile.close
  end
  
  def searchWord(word)
    len = word.length;
    if len == 0
      return WORD_NOT_FOUND
    end
    parent = @rootNode
    len.times do |i| 
      ch = word[i]
      node = parent.getChild(ch)
      if node == nil 
        return WORD_NOT_FOUND
      end
      parent = node;
    end
    if parent.isEndOfWord
      return WORD_FOUND
    else
      return PREFIX_FOUND
    end
  end

  private
    def addWord(word)
      len = word.length
      if len == 0 
        return nil
      end
      parent = @rootNode
      len.times do |i|
        ch = word[i] 
        node = parent.getChild(ch)
        if node == nil 
          node = parent.addChild(ch)
        end
        parent = node
      end
      return parent.markEndOfWord
  end

end

class Boggle

    @dictionary
    @board
    @solution
    @pathTaken

    WIDTH  = 4
    HEIGHT = 4
    
    public
    def initialize(dictionary)
        @dictionary = dictionary
        @solution   = Array.new
        @pathTaken  = Hash.new{|h, k| h[k] = {}}
        WIDTH.times do |i|
          HEIGHT.times do |j|
            @pathTaken[i][j] = false
          end
        end
        @board = getRandomBoard()
    end

    def printBoard
      puts "--Board--------"
      WIDTH.times do |i|
        HEIGHT.times do |j|
           print "#{@board[i][j]} "
        end
        puts ""
      end
      puts "---------------"
    end

    def getBoardSolution

      WIDTH.times do |x|
        HEIGHT.times do |y|
           traversePath(x, y, '')
        end
      end
      return printSolution
    end

    def printSolution
      puts "--Solution------"
      @solution.each do |word|
        puts word
      end
      puts "----------------"
      return @solution.uniq!

    end

    def traversePath(x, y, path)
        newPath = path + @board[x][y]
        @pathTaken[x][y] = true

        searchResult = @dictionary.searchWord(newPath)
        if searchResult == 0 
            @pathTaken[x][y] = false
            return
        end

        if searchResult == 1
           @solution << newPath
        end  

        (-1..1).each do |rowOffset|
            newRow = x + rowOffset;
            if newRow < 0 || newRow >= 4
              next 
            end
          (-1..1).each do |colOffset|
              newCol = y + colOffset;
              if newCol < 0 || newCol >= 4
                next
              end
              if @pathTaken[newRow][newCol] == false
                traversePath(newRow, newCol, newPath);
              end
          end
        end
        @pathTaken[x][y] = false
    end

    private
    def getRandomBoard
      boggleBoard = Hash.new{|h, k| h[k] = {}}
      WIDTH.times do |i|
        HEIGHT.times do |j|
           boggleBoard[i][j] = getRandomLetter()
        end
      end
      return boggleBoard
    end

    def getRandomLetter
        alphabetList = 'abcdefghijklmnopqrstuvwxyz';
        letterIndex  = rand(0..25);
        return alphabetList[letterIndex]
    end

end

##########################################################

wordListFilePath = "wordlist.txt"
dictionary = Dictionary.new(wordListFilePath)
boggle     = Boggle.new(dictionary)
boggle.printBoard
boggle.getBoardSolution


