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
* レコード削除
***********************************************/
window.addEventListener('DOMContentLoaded',function() {

$('.delete-btn').on('click', function() {
  let delete_confirm = confirm('マジで消しちゃっていいですか？');

  if(delete_confirm === true) {
    //ボタンの親の親要素（tr）のid値を取得
    let record_id = $(this).parent().parent().attr("id");
    // record_id = 'id='+ record_id;
    
    //削除対象のレコード行を取得
    let element = $(this).parent().parent();
    element = element[0];
    console.log(element);

    // 非同期処理
    $.ajax({
      
      type: 'POST',
      url: '../ajaxRegistory.php',
      datatype: "json",
      data: {'id': record_id, 'method': 'delete'}

    })
    
    .done(function(data) {
      let total_record = data;
      console.log(total_record);

      // 通信が成功したらレコード削除
      console.log('通信成功');
      element.remove();
      
      // 画面のレコード数表示（●●件）を更新
      $("#total_record").html(total_record);
      //1ページに表示する最大レコード数
      const limit = 10;
      //MAXのページ数を取得
      const max_page = Math.ceil((total_record+1) / limit);
      //現在ページ数を取得
      const carrent_page = $("#carrent_page").html();
      //現在ページが最終ページになるのか計算
      const exist_next = max_page - carrent_page;

      //レコード削除に伴いページ数を減らすか否か確認
      if(total_record % limit === 0) {
        $(`#page-num${max_page}`).remove();
        if(exist_next === 1 ) {
          //現在ページが最終ページになったら「次へ」を非活性にする
          $("#next-page").attr('class', 'page-item disabled');
        }
      }else {
        console.log(total_record % limit === 0);
      }
    })

    .fail(function() {
      //★仮置き。ヘッダー直下にエラー内容を表示する予定
      // window.location.href('http://localhost/kakeibo/view/error.php');  
      alert('エラーが発生しました。');
    });
  }
});

}, false);

/***********************************************
* 取得したレコードをモーダルWindowで表示
***********************************************/
window.addEventListener('DOMContentLoaded',function() {

  $('.edit-btn').on('click', function() {
  
      //ボタンの親の親要素（tr）のid値を取得
      let id = $(this).parent().parent().attr("id");
      let record_id = id;
      console.log(record_id);
      //編集対象のレコード要素（tr…/tr）を取得
      let element = $(this).parent().parent();
      // element = element[0].innerText;
      console.log(element);
  
      // 非同期処理
      $.ajax({
        // リクエスト方法
        type: 'POST',
        // 送信先ファイル名
        url: '../ajaxRegistory.php',
        // 受け取りデータの種類
        datatype: "json",
        // 送信データ
        data: {'id': record_id, 'method': 'select'}
  
      })
  
      // 通信が成功した時
      .done( function(data) {
        
        console.log('通信成功');
        console.log(data);
        console.log(data.payment);
        
        //DBより取得した値編集フォームにを入れる
        $("#record_id").val(data.id);
        $("#type_id").val(data.type_id);
        $("#edit_payment_at").val(data.payment_at);
        $("#edit_title").val(data.title);
        $("#edit_payment").val(data.payment);
        $("#edit_memo").val(data.memo);
        
        let type_id = (data.type_id);
        let category_id = (data.category_id);
  
        //編集ボタン押下したときのページurlを取得
        let uri = location.href;
  
        //記帳画面（registory.php）の編集ボタンを押下したときの処理
        if (uri.match(/registory.php/)){
  
          if (type_id == 1) {
            //カテゴリ表示を複製
            let clone_outgoes = $('#outgoes').clone(true);
            // 複製した要素の属性を編集
            clone_outgoes[0].id = "modal_outgoes";
            //複製したカテゴリをhtmlに追加する
            $('#modal_categories').html(clone_outgoes[0]);
            //選択済みのカテゴリ名が初期値で設定される。
            $("#modal_outgoes").val(category_id);
    
          } else {
    
            let clone_incomes = $('#incomes').clone(true);
    
            clone_incomes[0].id = "modal_incomes";
    
            $('#modal_categories').html(clone_incomes[0]);
    
            $("#modal_incomes").val(category_id);
          }
        }
  
      })
  
      // 通信が失敗した時
      .fail( function(data) {
        console.log('通信失敗');
        console.log(data);
      });
  
      //モーダルウィンドウの表示
      $('.edit_form').fadeIn();
      $('.modal').fadeIn();
      
      });
  
      // モーダルを閉じる
      $('#close').on('click',function() {
        $('.edit_form').fadeOut();
        $('.modal').fadeOut();
      });
  
      //モーダルを閉じる
      $('.modal').on('click', function() {
        $('.edit_form').fadeOut();
        $('.modal').fadeOut();
      });
  
  
  
  }, false);
