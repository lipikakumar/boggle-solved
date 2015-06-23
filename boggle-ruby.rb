
class Node
  @eow
  @children

  def initialize()
    @eow = false
    @children = nil
  end

  def addChild(char)
    if @children == nil 
      @children = []
    end
    @children[char] = Node.new
  end

  def markEndOfWord()
    @eow = true
  end

  def isEndOfWord()
    @eow
  end

  def getChild(char)
    if @children == nil
      nil
    end
    (@children[char] == nil) ? @children[char] : nil 
  end

end

class Dictionary
  @rootNode;
  def initialize(wordsFileName) 
    @rootNode = Node.new
    aFile = File.new(wordsFileName, "r")
    aFile.each_line {|line| addWord(line)}
    aFile.close
  end
  
  def addWord(word)
      len = word.length;
      if ( len == 0 ) 
        return
      end
      parent = @rootNode; 
      len.times do |i|
        ch = word[i] 
        node = parent.getChild(ch)
        if ( node == nil ) 
          node = parent.addChild(ch)
        end
        parent = node
      end
      @parent.markEndOfWord()
  end

  def searchWord(word)
    len = word.length;
    if (len == 0)
        0
    end
    parent = @rootNode; 
    len.times do |i| 
      ch    = word[i] 
      node = parent.getChild(ch); 
      if ( node == nil )
        0
      end
      parent = node;
    end
      parent.isEndOfWord() ? 1 : 2

  end

end



puts "Dictionary Complete"

dictionary = Dictionary.new("wordlist.txt")
