///////////////////////////////////////////
// 入力フォームの収支タブ切り替え処理
///////////////////////////////////////////
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
  
  ///////////////////////////////////////////
  // 入力する金額にコンマを入れる処理
  ///////////////////////////////////////////
  function addComma(inputNum){
    let inputValue = inputNum.value;
    let num = inputValue.replace(/[^0-9]/g, "");
    num = num.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
    if(num.match(/[^0-9]/g)){
      inputNum.value= num;
      return true;
    }
   };
  
  
  
  ///////////////////////////////////////////
  // 入力する金額にコンマを入れる処理
  ///////////////////////////////////////////
  // var num = document.querySelectorAll( "[data-type='amount']" );
  // /* イベント操作 */
  // for(var i=0; i < num.length; i++){ 
  //   //対象のinputに対して関数を代入
  //   //何か入力されたらイベント処理（numInput関数）が発生
  //   num[i].oninput = numInput 
  // }
  
  // /* 入力時に実行する処理 */
  // function numInput(event){
  //   var target = event.target;
  //   var data = target.value[ target.value.length-1 ];
  //   console.log(data);
  //   if( data.match( /[0-9]/ ) ){
  //     target.value = target.value
  //     .replace( /,/g, '' )
  //     .replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,' );
  //   // target.value = target.value.slice( 0, target.value.length-1 );
  //   }
  
  // }
  
  
  
  
  // window.addEventListener('DOMContentLoaded',function() {
  
  // 	// buttonがクリックされたときに実行
  // 	$("delete-btn").click(function() {
  
  // 		// buttonの記事IDを取得する
  // 		let id = $(this).attr("id");
  //         console.log(id);
  
  // 		// POST用のデータ準備：id=をつけないと、$_POST['id']で取得できない
  // 		let record = 'id='+ id;
  
  // 		// span内の投票数を書き換える
  // 		let thisButton = $(this).prev('tr');
  
  // 		$.ajax({
  
  // 			 type: "POST",
  // 			 url: "change_record.php",
  // 			 data: record,
  
  // 			 success: function(data) {
  // 			 	// 処理が成功したら、thisButton内部を書き換える
  // 				thisButton.html(data);
  // 			}
  // 		});
  
  // 		return false;
  // 	});
  
  //   $(".delete-btn").click(function(){
  //     var btnid = $(this).data("id");
  //     deleteData(btnid);
  //   });
  //   function deleteData(btnid){
  //     $.ajax({
  //         type: 'POST',
  //         dataType:'json',
  //         url:'change_record.php',
  //         data:{
  //             btnid:btnid,
  //         },
  //         success:function(data) {
  //             console.log("成功");
  //         },
  //         error:function(XMLHttpRequest, textStatus, errorThrown) {
  //             alert(errorThrown);
  //         }
  //     });
  // };
  
  
  
  
  
  
  
  
  
  