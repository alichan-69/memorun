$(function(){
    // 文字カウンターの処理
    let $textarea = $("textarea");

    $textarea.each(function(){
        let $counter = $(this).siblings(".counter");
        let $counter_num = $counter.children(".counter_num");

        // ページ描画時に文字数を描画
        $counter_num.text($(this).text().length);

        // 打鍵時にカウンター処理
        $(this).keyup(function(){
            $counter_num.text($(this).val().length);

            if($(this).val().length > 255){
                $counter.addClass("error_message");
            }else{
                $counter.removeClass("error_message");
            }
        });
    });

    // メモを表示するエリア周辺のスタイルを表示するメモの数ごとに変える処理
    let memo_size = $(".memos_container > li").length;
    let pagenation = $(".pagenation");
    if(memo_size === 0){
        pagenation.css("display","none");
    }

    // メモの更新・削除時にでてくるモーダルの処理
    let $modal = $(".modal");

    if($modal.text().length){
        $modal.slideToggle('slow');
        setTimeout(function(){ $modal.slideToggle('slow'); }, 5000);
    }
});