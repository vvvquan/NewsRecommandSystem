function shenzhan(e){
    if(e.classList.contains('active')){
        e.classList.remove('active')
    } else {
        e.classList.add('active')
    }
}

function hide(){
    var left = document.getElementById('leftNav')
    var main = document.getElementById('main')
    document.getElementById('hide').style.display = 'none'
    document.getElementById('show').style.display = 'block'
    main.style.width = '99%'
    left.style.display = 'none'
}

function show(){
    var left = document.getElementById('leftNav')
    var main = document.getElementById('main')
    document.getElementById('hide').style.display = 'block'
    document.getElementById('show').style.display = 'none'
    main.style.width = '79%'
    left.style.display = 'block'
}