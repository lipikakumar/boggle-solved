
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

    @children[char] = Node.new
    return @children[char]
  end

  def markEndOfWord
    @eow = true
  end

  def isEndOfWord
    @eow
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
  @rootNode;
  def initialize(wordsFileName) 
    @rootNode = Node.new
    aFile = File.new(wordsFileName, "r")
    aFile.each_line {|line|
    word = line.to_s.strip
    addWord(word)
   }
    aFile.close
  end
  
  def addWord(word)
    #puts word
      len = word.length
    #puts "#{len}"
      if len == 0 
        return nil
      end
      parent = @rootNode
      len.times do |i|
        ch = word[i] 
       #puts "#{i} : #{ch}"
        node = parent.getChild(ch)
        if node == nil 
          node = parent.addChild(ch)
        end
        parent = node
      end
      #puts "marks end of word: #{parent.markEndOfWord}"

      return parent.markEndOfWord
  end

  def searchWord(word)
    len = word.length;
    if len == 0
      return 0
    end
    parent = @rootNode
    len.times do |i| 
      ch = word[i]
      node = parent.getChild(ch)
      if node == nil 
        return 0
      end
      parent = node;
    end
    if parent.isEndOfWord
      return 1
    else
      return 2
    end
  end

end

dictionary = Dictionary.new("short-wordlist.txt")
word = "abaft"
prefix = "ab"
garbage = "oijiud"

puts "#{word}"
puts dictionary.searchWord(word)


puts "#{prefix}"
puts dictionary.searchWord(prefix)

puts "#{garbage}"
puts dictionary.searchWord(garbage)

puts "Dictionary Complete"


