const user_new = new Vue({
  el: '#app',
  data: {
    group: 'new_group',
    url: '',
  },
  methods: {
    uploadFile() {
      const file = this.$refs.preview.files[0];
      this.url = URL.createObjectURL(file)
    }
  },
});

// 確認用モーダル
$(function () {
  $('#exampleModal').on('show.bs.modal', function () {
    var user_name = $('#user_name').val()
    var mail = $('#mail').val()
    var password = $('#password').val()
    var group_name = $('#group_name').val()
    var modal = $(this)
    modal.find('#modalName').text(user_name)
    modal.find('#modalMail').text(mail)
    modal.find('#modalPassword').text(password)
    modal.find('#modalGroup').text(group_name)
  })
})

$(document).keypress(function (e) {
  // エンターキーだったら無効にする
  if (e.key === 'Enter') {
    return false;
  }
});

// 確認ボタンの非活性
$(document).ready(function () {
  const $submitBtn = $('#js-submit')
  $submitBtn.prop('disabled', true);
  $('#form input').on('change', function () {
    if (
      $('#form input[type="text"]').val() !== "" &&
      $('#form input[type="mail"]').val() !== "" &&
      $('#form input[type="password"]').val() !== "" &&
      $('#form input[type="file"]').val() !== ""
    ) {
      $submitBtn.prop('disabled', false);
    }
  });
});