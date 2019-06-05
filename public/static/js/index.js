//这是index模块的index.html使用的js

var x = document.getElementById('recent')
var y = document.getElementById('hot')
var xclass = x.classList
var yclass = y.classList
var recentContent = document.getElementById('tab-content-recent')
var hotContent = document.getElementById('tab-content-hot')

x.addEventListener('mouseover', function(){
    yclass.remove('active')
    xclass.add('active')
    recentContent.style.display = 'block'
    hotContent.style.display = 'none'
})

y.addEventListener('mouseover', function(){
    xclass.remove('active')
    yclass.add('active')
    recentContent.style.display = 'none'
    hotContent.style.display = 'block'
})