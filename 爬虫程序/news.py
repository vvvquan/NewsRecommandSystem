#coding: utf-8
 
import codecs
import requests
from urllib import request, parse
from bs4 import BeautifulSoup
import re
import time
from urllib.error import HTTPError, URLError
import sys
import pymysql
import pymysql.cursors
 
###新闻类定义
class News(object):
	def __init__(self):
		self.url = None      #该新闻对应的url
		self.topic = None    #新闻标题
		self.date = None     #新闻发布日期
		self.content = None  #新闻的正文内容
		self.source = None   #新闻来源

def mysql(topic, date, source, url, content):
	with connection.cursor() as cursor:
		sql = 'insert into news(topic,date,source,url,content) values('+'\''+topic+'\',\''+date+'\',\''+source+'\',\''+url+'\',\''+content+'\')'
		cout = cursor.execute(sql)
		connection.commit()

 
def getHTML(url):
	#获取页面所有元素
	html = request.urlopen(url).read().decode('utf-8', 'ignore')
	#解析
	soup = BeautifulSoup(html, 'html.parser')
	#f.write(soup.prettify())
	return soup

#时政
def getNews1(soup): 
	#获取文章信息
	if not(soup.find('div', {'id':'article'})): return	
	news = News()  #建立新闻对象	
	topic = soup.find('h1', {'class':'main-title'})
	if not(topic): return
	news.topic = topic.get_text()		#新闻标题 
	main_content = soup.find('div', {'id': 'article'})   #新闻正文内容
	content = ''	
	for p in main_content.select('p'):
		content = content + p.get_text() 
	news.content = content 
	#news.url = url       #新闻页面对应的url
	news.url = 'http://news.sina.com.cn'  #现在url不需要了，随便给个网址
	if not(soup.find('div',{'class' : 'date-source'})): return
	date_source = soup.find('div',{'class' : 'date-source'})
	date = date_source.find('span',{'class' : 'date'})
	if not(date): return
	news.date = date.get_text()        #新闻发布日期

	source = date_source.find('a',{'class' : 'source'})
	if not(source): return
	news.source = source.get_text()     #新闻来源

	# 写入文件
	# string = news.topic+'\n'+news.date+'\n'+news.source+'\n'+news.content+'\n\n'+news.url
	# if len(news.content) == 0: return
	# f = open('sina/news'+ str(count) +'.txt', 'w', encoding='utf-8')
	# f.write(string)
	# f.close()

	# 写入数据库
	length = len(news.content)  #len()也是一个汉字算1个
	if length == 0: return  #没有爬到内容
	if length * 3 > 64 * 1000:
		return  #超过64K的内容不插入，一个utf8汉字占据3个字节

	mysql(news.topic, news.date, news.source, news.url, news.content)

#娱乐  体育
def getNews23(soup): 
	#获取文章信息
	if not(soup.find('div', {'id':'article'})): return	
	news = News()  #建立新闻对象	
	topic = soup.find('h1', {'class':'main-title'})
	if not(topic): return
	news.topic = topic.get_text()		#新闻标题 
	main_content = soup.find('div', {'class': 'article'})   #新闻正文内容
	content = ''	
	for p in main_content.select('p'):
		content = content + p.get_text() 
	news.content = content 
	#news.url = url       #新闻页面对应的url
	news.url = 'http://news.sina.com.cn'  #现在url不需要了，随便给个网址
	if not(soup.find('div',{'class' : 'date-source'})): return
	date_source = soup.find('div',{'class' : 'date-source'})
	date = date_source.find('span',{'class' : 'date'})
	if not(date): return
	news.date = date.get_text()        #新闻发布日期

	source = date_source.find('a',{'class' : 'source'})
	if not(source): return
	news.source = source.get_text()     #新闻来源
	# 写入数据库
	length = len(news.content)  #len()也是一个汉字算1个
	if length == 0: return  #没有爬到内容
	if length * 3 > 64 * 1000:
		return  #超过64K的内容不插入，一个utf8汉字占据3个字节

	mysql(news.topic, news.date, news.source, news.url, news.content)

#科技
def getNews4(soup): 
	#获取文章信息
	if not(soup.find('div', {'id':'article'})): return	
	news = News()  #建立新闻对象	
	topic = soup.find('h1', {'class':'main-title'})
	if not(topic): return
	news.topic = topic.get_text()		#新闻标题 
	main_content = soup.find('div', {'class': 'article'})   #新闻正文内容
	content = ''	
	for p in main_content.select('p'):
		content = content + p.get_text() 
	news.content = content 
	#news.url = url       #新闻页面对应的url
	news.url = 'http://news.sina.com.cn'  #现在url不需要了，随便给个网址
	if not(soup.find('div',{'class' : 'date-source'})): return
	date_source = soup.find('div',{'class' : 'date-source'})
	date = date_source.find('span',{'class' : 'date'})
	if not(date): return
	news.date = date.get_text()        #新闻发布日期

	source = date_source.find('span',{'class' : 'source'})
	if not(source): return
	news.source = source.get_text()     #新闻来源

	# 写入文件
	# string = news.topic+'\n'+news.date+'\n'+news.source+'\n'+news.content+'\n\n'+news.url
	# if len(news.content) == 0: return
	# f = open('sina/news'+ str(count) +'.txt', 'w', encoding='utf-8')
	# f.write(string)
	# f.close()

	# 写入数据库
	length = len(news.content)  #len()也是一个汉字算1个
	if length == 0: return  #没有爬到内容
	if length * 3 > 64 * 1000:
		return  #超过64K的内容不插入，一个utf8汉字占据3个字节

	mysql(news.topic, news.date, news.source, news.url, news.content)

#文化
def getNews5(soup): 
	#获取文章信息
	if not(soup.find('div', {'id':'article'})): return	
	news = News()  #建立新闻对象	
	topic = soup.find('h1', {'class':'main-title'})
	if not(topic): return
	news.topic = topic.get_text()		#新闻标题 
	main_content = soup.find('div', {'id': 'article'})   #新闻正文内容
	content = ''	
	for p in main_content.select('p'):
		content = content + p.get_text() 
	news.content = content 
	#news.url = url       #新闻页面对应的url
	news.url = 'http://news.sina.com.cn'  #现在url不需要了，随便给个网址
	if not(soup.find('div',{'class' : 'date-source'})): return
	date_source = soup.find('div',{'class' : 'date-source'})
	date = date_source.find('span',{'class' : 'date'})
	if not(date): return
	news.date = date.get_text()        #新闻发布日期

	source = date_source.find('span',{'class' : 'source'})
	if not(source): return
	news.source = source.get_text()     #新闻来源

	# 写入数据库
	length = len(news.content)  #len()也是一个汉字算1个
	if length == 0: return  #没有爬到内容
	if length * 3 > 64 * 1000:
		return  #超过64K的内容不插入，一个utf8汉字占据3个字节

	mysql(news.topic, news.date, news.source, news.url, news.content)

#获取链接中的信息
def dfs2(url, pattern):
	soup = getHTML(url)
	if pattern == pattern1:
		getNews1(soup)
	if pattern == pattern2:
		getNews23(soup)
	if pattern == pattern3:
		getNews23(soup)
	if pattern == pattern4:
		getNews4(soup)
	if pattern == pattern5:
		getNews5(soup) 
#获取页面中的链接
def dfs(url, pattern):
	#该url访问过，则直接返回
	if url in visited:  return 
	#把该url添加进visited()
	visited.add(url)
	# print(visited)
	try:
		soup = getHTML(url) 
		# if re.match(pattern, url):
		# 	getNews(soup)
		####提取该页面其中所有的url####
		links = soup.findAll('a', href=re.compile(pattern))
		for link in links:
			print(link['href'])
			if link['href'] not in visited: 
				if re.match(pattern, link['href']): 
					dfs2(link['href'], pattern)

	except URLError as e:
		print(e)
		return
	except HTTPError as e:
		print(e)
		return
 
visited = set()  ##存储访问过的url

connection = pymysql.connect(host = 'localhost',
                             user = 'root',
                             password = 'root',
                             db = 'NewsDB',
                             port = 3306,
                             charset = 'utf8') #注意是utf8而不是utf-8

#解析新闻信息的url规则
#时政
pattern1 = 'https:\/\/news\.sina\.com\.cn\/[a-z]\/[0-9\-]{10}\/[a-zA-Z0-9\-]*\.shtml'
#娱乐
pattern2 = 'https:\/\/ent\.sina\.com\.cn\/[a-z]\/[a-z]\/[0-9\-]{10}\/[a-zA-Z0-9\-]*\.shtml'
#体育
pattern3 = 'https:\/\/sports\.sina\.com\.cn\/[a-z]*\/[a-z]*\/[0-9\-]{10}\/[a-zA-Z0-9\-]*\.shtml'
#科技
pattern4 = 'https:\/\/tech\.sina\.com\.cn\/it\/[0-9\-]{10}\/[a-zA-Z0-9\-]*\.shtml'
#文化
pattern5 = 'https:\/\/cul\.news\.sina\.com\.cn\/topline\/[0-9\-]{10}\/[a-zA-Z0-9\-]*\.shtml'

#时政
# print('----时政新闻-----')
# dfs('https://news.sina.com.cn', pattern1)   #这类网页有关键词和相关新闻，可以用来跟自己的推荐计算结果进行比较

#娱乐
# print('----娱乐新闻-----')
# dfs('https://ent.sina.com.cn', pattern2)

#体育
#print('----体育新闻-----')
#dfs('https://sports.sina.com.cn', pattern3)  #正则表达式匹配失败，体育新闻 还没有爬

#科技
# print('----科技新闻-----')
# dfs('https://tech.sina.com.cn', pattern4)

#文化
# print('----文化新闻-----')
# dfs('https://cul.news.sina.com.cn', pattern5)   #这类网页有关键词和相关新闻，可以用来跟自己的推荐计算结果进行比较

connection.close()