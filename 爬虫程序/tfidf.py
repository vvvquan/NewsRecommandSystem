import math

def not_include(keyword, word):
    num = len(keyword)
    for i in range(num):
        if word == keyword[i]:
            return False
    return True

def contain(word_list, word):
    for t in word_list:
        if t == word:
            return True
    return False

def sort(tf_idf, num, index):
    a = tf_idf
    b = {}
    for i in range(num):
        b[i] = i
    # sort desc
    for i in range(num-1):
        for j in range(i+1, num):
            if a[i] < a[j]:
                a[i], a[j] = a[j], a[i]
                b[i], b[j] = b[j], b[i]
    test = open('test/tf_idf-'+ str(index) +'.txt', 'w', encoding = 'utf-8')
    for i in range(top):
        test.write(str(a[i])+' ')
    test.close()
    return b
    

def calc_chuxian(word, arr, i, start, end):
    cnt = 0
    for k in range(start, end):
        if i == k:
            continue
        for temp in arr[k]:
            if(word == temp):
                cnt = cnt + 1
                break
    return cnt


# arr下标从1开始， tf_idf下标从0开始
if __name__ == '__main__':
    arr = {}
    start = 777 ############################################## continue
    end = 934 #n+1   ##############################################
    n =  end - start #文档总数

    for i in range(start, end):
        f = open('sina/news' + str(i) + '_cut.txt', 'r', encoding = 'utf-8')
        data = f.read()
        arr[i] = data.split('#')
        f.close()
    
    #读取停用词
    f = open('stop_words.txt', 'r')
    data = f.read()
    stop_words_list = data.split('\n')
    f.close()

    for i in range(start, end):  #第i篇文档
        print('处理第'+str(i)+'个文档中...')
        num = len(arr[i])  #第i个文档的分词个数
        tf_idf = {}
        j = 0  #第i个文档的第j个分词
        for word in arr[i]:
            if contain(stop_words_list, word) or len(word) < 2 or word.isdigit(): #过滤停用词、纯数字、单个字
                tf_idf[j] = 0
                j = j + 1
                continue 
            cnt = 0 #统计出word在第i个文档的出现次数cnt
            for temp in arr[i]:
                if word == temp:
                    cnt = cnt + 1
            tf = cnt / num
            cnt2 = calc_chuxian(word, arr, i, start, end) #计算第i个以外的包含word在内的文档数
            idf = math.log(n / (cnt2 + 1))
            tf_idf[j] = tf * idf
            j = j + 1  #第i个文档的下一个词语
        
        top = 10  # 第i个文档的关键词个数
        #第i个文档的num个分词，找到tf-idf值最大的top个word，返回这些词的下标索引
        index = sort(tf_idf, num, i)
        filename = 'sina/keyword/keyword' + str(i) +'.txt'
        f = open(filename, 'w', encoding = 'utf-8')
        keyword = {}
        x = 0
        for j in range(num):
            if x == top:
                break
            t = arr[i][index[j]]
            if not_include(keyword, t):
                keyword[x] = t
                f.write(keyword[x] + '\n')
                x = x + 1
        f.close()