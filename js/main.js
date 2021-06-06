// カウンターの処理
let textarea = $("textarea[name='memo']");
let counter = $(".conter");

textarea.keyup(function(){
    let string_num = $(".string_num");
    
    alert(string_num.textContent);
    if(textarea.value > 255){

    }
});