
class Node
  @eow
  @children

  def initialize
    @eow = false
    @children = nil
  end

  def addChild(char)
    if @children == nil 
      @children = Hash.new
    end
    key = char.to_sym
    node = Node.new
    @children[key] = node
  end

  def markEndOfWord
    @eow = true
  end

  def isEndOfWord
    @eow
  end

  def getChild(char)
    key = char.to_sym
    @children == nil ? nil : @children[key] 
  end
end

class Dictionary
  @rootNode

  def initialize(wordsFileName) 
    @rootNode = Node.new
    aFile = File.new(wordsFileName, "r")
    aFile.each_line do |line| 
      addWord(line)
    end
    aFile.close
  end
  
  def addWord(word)
      len = word.length
      if len == 0 
        return
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
      parent.markEndOfWord
  end

  def searchWord(word)
    len = word.length
    if len == 0
      0
    end
    parent = @rootNode
    len.times do |i| 
      ch = word[i]
      node = parent.getChild(ch)
      # the prefix does not exist 
      # in the dictionary so no need to keep searching
      if node == nil 
        0
      end
      parent = node;
    end
    parent.isEndOfWord ? 1 : 2
  end
  def getRootNode 
      @rootNode
  end
end

dictionary = Dictionary.new("short-wordlist.txt")
dictionary.getRootNode()

# word = "abaft"
# prefix = "ab"
# garbage = "oijiud"

#puts dictionary.searchWord(word)

# puts "Prefix: #{prefix}"
# puts dictionary.searchWord(prefix)

# puts "Garbage: #{garbage}"
# puts dictionary.searchWord(garbage)

# puts "Dictionary Complete"


