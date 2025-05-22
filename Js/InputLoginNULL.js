var a = document.getElementById('mk').value;
var b = document.getElementById('tk').value;
var c = document.getElementById('thongbao');
function inputNULL(a,b){
    if(a == null || b == null){
        c.innerHTML = "Vui lòng nhập đầy đủ thông tin";
        c.style.display = 'block';
    }
}