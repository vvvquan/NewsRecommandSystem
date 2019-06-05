        var $ = function(selector){
            return document.querySelector(selector);
        },
        box = $('.drag'),//容器
        bg = $('.bg'),//绿色背景
        text = $('.text'),//文字
        btn = $('.btn'),//拖动按钮
        done = false;//是否通过验证

        btn.onmousedown = function(e){
            var e = e || window.event;
            var posX = e.clientX - this.offsetLeft;

            btn.onmousemove = function(e){
                var e = e || event;

                var x = e.clientX - posX;

                this.style.left = x + 'px';
                bg.style.width = this.offsetLeft + 'px';

                // 通过验证
                if(x >= box.offsetWidth - btn.offsetWidth){
                    done = true;
                    btn.onmousedown = null;
                    btn.onmousemove = null;
                    text.innerHTML = '通过验证';
                }
            };

            document.onmouseup = function(){
                btn.onmousemove = null;
                btn.onmouseup = null;

                if(done)return;
                btn.style.left = 0;
                bg.style.width = 0;
            }
        };

        text.onmousedown = function(){
            return false;
        }