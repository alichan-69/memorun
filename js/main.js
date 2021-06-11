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

    // メモの更新・削除時にでてくるモーダルの処理
    let modal = $(".modal");

    if(modal.text().length){
        modal.slideToggle('slow');
        setTimeout(function(){ modal.slideToggle('slow'); }, 5000);
    }
});