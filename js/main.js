$(function () {
  // フッターの位置調整、即時関数で包む
  (function () {
    const $footer = $(".l_footer");
    const $window = $(window);
    const windowHeight = $window.height();
    const footerPosition = $footer.offset().top;
    const footerHeight = $footer.innerHeight();
    if (footerPosition < windowHeight - footerHeight) {
      $footer.offset({ top: windowHeight - footerHeight, left: 0 });
    }
  })();
  //セッションのメッセージを取得し、ニュルッと出す
  (function () {
    const $topMsg = $(".js_topMsg");
    const msg = $topMsg.text();
    if (msg.replace(/^[\s　]+|[\s　]+$/g, "").length) {
      $topMsg.slideToggle("slow");
      setTimeout(function () {
        $topMsg.slideToggle("slow");
      }, 3000);
    }
  })();

  // フォーム入力
  // 変数指定
  const $formText = $(".b_formText");
  const $check = $(".js_check");
  // 処理
  // フォームのバリデーション
  $formText.on("keyup", function () {
    const $formMsg = $(".b_formMsg");
    const $pass = $('input[name="pass"]');
    const $passOld = $('input[name="passOld"]');
    const $passNew = $('input[name="passNew"]');
    const $passRe = $('input[name="passRe"]');
    const $passNewRe = $('input[name="passNewRe"]');
    const $form = $(this).parent();
    const $b_formMsg = $form.find($formMsg);
    const mailForm = /^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/;
    const formName = $(this).attr("name");
    const formVal = $(this).val();

    function pushErr() {
      $form.removeClass("is_suc").addClass("is_err");
      // $e_btn.prop("disabled", true);
    }
    function pushSuc() {
      $form.removeClass("is_err").addClass("is_suc");
      // $e_btn.prop("disabled", false);
      outputText("");
    }
    function outputText($t) {
      $b_formMsg.text($t);
    }

    // フォームそれぞれに対応していく
    if (formName === "email") {
      //メアド
      if (formVal === "") {
        pushErr();
        outputText("入力してください");
      } else if (!formVal.match(mailForm)) {
        pushErr();
        outputText("Eメールアドレスの形式で入力してください");
      } else if (formVal.length > 256) {
        pushErr();
        outputText("255文字以下で入力してください");
      } else {
        pushSuc();
      }
      //パスワード
    } else if (
      formName === "pass" ||
      formName === "passOld" ||
      formName === "passNew"
    ) {
      if (formVal === "") {
        pushErr();
        outputText("入力してください");
      } else if (!formVal.match(/^([a-zA-Z0-9])*$/)) {
        pushErr();
        outputText("半角英数字で入力してください");
      } else if (formVal.length < 8) {
        pushErr();
        outputText("8文字以上で入力してください");
      } else if ($passOld === $passNew) {
        pushErr();
        outputText("現在のパスワードと新しいパスワードが同じです");
      } else {
        pushSuc();
      }
    } else if (formName === "passRe" || formName === "passNewRe") {
      //パスワード再入力
      if (formVal === "") {
        pushErr();
        outputText("入力してください");
      } else if (!formName.match(/^([a-zA-Z0-9])*$/)) {
        pushErr();
        outputText("半角英数字で入力してください");
      } else if (formVal.length < 8) {
        pushErr();
        outputText("8文字以上で入力してください");
      } else if (
        $pass.val() !== $passRe.val() ||
        $passNew.val() !== $passNewRe.val()
      ) {
        pushErr();
        outputText("パスワード（再入力）があっていません");
      } else {
        pushSuc();
      }
    } else if (formName === "target" || formName === "name") {
      if (formVal === "") {
        pushErr();
        outputText("入力してください");
      } else {
        pushSuc();
      }
    }
  });
  // チェック
  $check.on("click", function () {
    // $(this).toggleClass("js_check__checked");
    const id = $(this).data("routineid");
    $(this).toggleClass("js_check__checked");
    $.ajax({
      type: "POST",
      url: "js/ajaxroutine.php",
      data: { routineId: id },
    })
      .done(function () {
        console.log("ajax success");
      })
      .fail(function () {
        console.log("ajax error");
      });
  });
});
