/***********************************************
* 入力フォームの収支タブ切り替え処理
***********************************************/
document.addEventListener('DOMContentLoaded', function(){
    // タブに対してクリックイベントを適用
    const tabs = document.getElementsByClassName('tab');
    for(let i = 0; i < tabs.length; i++) {
      tabs[i].addEventListener('click', tabSwitch, false);
    }
    
    // タブをクリックすると実行する関数
    function tabSwitch(){
      // タブのclassの値を変更
      document.getElementsByClassName('is-active')[0].classList.remove('is-active');
      this.classList.add('is-active');
      // コンテンツのclassの値を変更
      document.getElementsByClassName('is-show')[0].classList.remove('is-show');
      const arrayTabs = Array.prototype.slice.call(tabs);
      const index = arrayTabs.indexOf(this);
      document.getElementsByClassName('panel')[index].classList.add('is-show');
    };
}, false);


/***********************************************
* 入力する金額にコンマを入れる処理
***********************************************/

  function addComma(inputNum){
    let inputValue = inputNum.value;
    let num = inputValue.replace(/[^0-9]/g, "");
    num = num.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
    if(num.match(/[^0-9]/g)){
      inputNum.value= num;
      return true;
    }
  };


/***********************************************
* 非同期でレコード削除
***********************************************/
window.addEventListener('DOMContentLoaded',function() {

  $('.delete-btn').on('click', function() {
    let delete_confirm = confirm('マジで消しちゃっていいですか？');

    if(delete_confirm === true) {
      //ボタンの親の親要素（tr）のid値を取得
      let record_id = $(this).parent().parent().attr("id");
      record_id = 'id='+ record_id;
      
      //削除対象のレコード行を取得
      let element = $(this).parent().parent();
      element = element[0];
      console.log(element);

      // 非同期処理
      $.ajax({
        
        type: 'POST',
        url: 'http://localhost/kakeibo/ajax_registory.php',
        data: record_id,

      })

      .done(function() {
        // 通信が成功したらレコード削除
        element.remove();
      })

      .fail(function() {
        //★仮置き。ヘッダー直下にエラー内容を表示する予定
        alert('エラーが発生しました。');
      });
    }
  });

}, false);

/***********************************************
* 非同期でレコード取得
***********************************************/
window.addEventListener('DOMContentLoaded',function() {

  $('.edit-btn').on('click', function() {

      //ボタンの親の親要素（tr）のid値を取得
      let record_id = $(this).parent().parent().attr("id");
      record_id = 'id='+ record_id;
      console.log(record_id);
      //削除対象のレコード行を取得
      let element = $(this).parent().parent();
      element = element[0];
      console.log(element);

      //１．ここで上記のelementから値(value)を取得する。
      
      let scroll_position = $(window).scrollTop();
      console.log(scroll_position);
      $('body').addClass('fixed').css({ 'top': -scroll_position });
      //１で取得した値をモーダルウィンドウのフォームに挿入する。
      
      $('.post_process').fadeIn();
      $('.modal').fadeIn();
      
  });
  // モーダルを閉じる
  $('#close').on('click',function(){
      $('.post_process').fadeOut();
      $('.modal').fadeOut();
  });


}, false);




