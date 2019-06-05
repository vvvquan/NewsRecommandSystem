# encoding=utf-8
import jieba
import os
import sys
import pymysql
import pymysql.cursors

def filter(word):
	filter_char = ['，','。','“','”','——','：',' ','、','…','（','）','？','！','|','《','》','#','@']
	for ch in filter_char:
		word = word.replace(ch, '')
	return word

def cut(num):
	with connection.cursor() as cursor:
		sql = 'select id, content from news'
		cursor.execute(sql)

	for row in cursor.fetchall():
		num = row[0]
		article = row[1]
		f = open('sina/news' + str(num) + '_cut.txt', 'w', encoding='utf-8')

		article = filter(article)
		seg_list = jieba.cut(article)
		for word in seg_list:
			word = filter(word).strip()
			if (len(word) > 0):
				f.write(word+'#')
		f.close()

## main ##
connection = pymysql.connect(host = 'localhost',
                             user = 'root',
                             password = 'root',
                             db = 'NewsDB',
                             port = 3306,
                             charset = 'utf8') #注意是utf8而不是utf-8
for num in range(777, 934):
	print(num)
	cut(num)

connection.close()