function scroll_l(width){
    num = 1  //一次滚动num张
    total = 9  //一共total张图
    xianshi = 4  //显示窗口显示4张
    marginRight = 10 //每张的右边缘距离
    end = (total-xianshi+num+1) * (width + marginRight) + marginRight * xianshi;
    width = num * width
    var x = document.getElementById('scroll').style.left
    x = x.replace('px','')
    x = Number(x) - width
    x = (x < -end ? -end : x)
    document.getElementById('scroll').style.left = x + 'px'
  }
  function scroll_r(width){
    num = 1  //一次滚动num张
    width = num * width  
    var x = document.getElementById('scroll').style.left
    x = x.replace('px','')
    x = Number(x) + width
    x = (x > 0 ? 0 : x)  //0是左边起点距离
    document.getElementById('scroll').style.left = x + 'px'
  }