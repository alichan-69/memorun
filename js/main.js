$(function(){
    // カウンターの処理
    let textarea = $("textarea[name='memo']");
    let counter = $(".counter");
    let counter_num = $(".counter_num");
    
    textarea.keyup(function(){
        counter_num.text(textarea.val().length);
        if(textarea.val().length > 255){
            counter.addClass("error_message");
        }else{
            counter.removeClass("error_message");
        }
    });
});