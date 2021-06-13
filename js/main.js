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

    // メモを表示するエリア周辺のスタイルを表示するメモの数ごとに変える処理
    let textarea_container = $("form .textarea_container"); 
    let memos_container = $(".memos_container");
    let memo_size = $(".memos_container > li").length;
    let memos = $(".memos");
    let pagenation = $(".pagenation");

    if(memo_size === 0){
        textarea_container.css("margin-bottom","30px");
        memos_container.css("height","0");
        memos.css("display","none");
        pagenation.css("display","none");
    }else if(3 >= memo_size >= 1){
        memos_container.css("height","300px");
    }else if(6 >= memo_size >= 4){
        memos_container.css("height","630px");
    }

    // メモの更新・削除時にでてくるモーダルの処理
    let modal = $(".modal");

    if(modal.text().length){
        modal.slideToggle('slow');
        setTimeout(function(){ modal.slideToggle('slow'); }, 5000);
    }
});