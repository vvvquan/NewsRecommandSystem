#coding: utf-8

import sys
import pymysql
import pymysql.cursors

def mysql(newsId, word):
	with connection.cursor() as cursor:
		sql = 'insert into keyword(newsId, word) values(\''+newsId+'\',\''+word+'\')'
		cursor.execute(sql)
		connection.commit()

 
def getKeyword(count):
	# 读文件
	f = open('sina/keyword/keyword'+ str(count) +'.txt', 'r', encoding='utf-8')
	tmp = f.read()
	f.close()

	# 写入数据库
	keyword = tmp.replace('\n', '#')
	mysql(str(count), keyword)
 
def solve():
	for i in range(777, 934):
		print(i)
		getKeyword(i)

connection = pymysql.connect(host = 'localhost',
                             user = 'root',
                             password = 'root',
                             db = 'NewsDB',
                             port = 3306,
                             charset = 'utf8') #注意是utf8而不是utf

solve()							 

connection.close()