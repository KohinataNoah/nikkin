<?php

$siteTtl = 'パスワードを忘れてしまった方へ';

// 共通関数読み込み
require('parts/function.php');
// 画面表示ログ吐き出し
dStart();

$page_num = 1;

if (!empty($_POST)) {
  d('POST送信を確認しました。');
  $email = $_POST['email'];
  d('メールアドレス：' . print_r($email, true));
  checkBlank($email, 'email');
  if (empty($errMsg)) {
    d('空白チェックOK。');
    checkEmailForm();
  }
  if (empty($errMsg)) {
    try {
      $dbh = connectDB();
      $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      $stmt = queryPost($dbh, $sql, $data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($stmt && array_shift($result)) {
        d('データベースに登録されているメールアドレスです。');
        $_SESSION['msg'] = 'メールを送信しました。';
        $authKey = makeRandKey();
        // メール生成
        $from = 'info@nikkin.com';
        $to = $email;
        $subject = 'パスワード再発行認証 | NIKKIN';
        $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
先程入力されたページにて認証キーをご入力いただくとパスワードが再発行されます。

認証キー：{$authKey}
※認証キーの有効期限は30分となります。

認証キーを再発行されたい場合は、再度パスワード再発行ページより再発行をお願いします。

//////////////////////////////
NIKKINカスタマーセンター
Email info@nikkin.com
//////////////////////////////
EOT;
        sendMail($from, $to, $subject, $comment);
        $_SESSION['auth_key'] = $authKey;
        $_SESSION['auth_email'] = $email;
        $_SESSION['auth_key_limit'] = time() + (60 * 30);
        d('セッション変数の中身：' . print_r($_SESSION, true));
      } else {
        d('メールアドレスが登録されていません。');
        // 登録されているかどうか判明してしまうので、登録していなくても送信したように振る舞う
        $_SESSION['msg'] = 'メールを送信しました。';
      }
    } catch (Exception $e) {
      getErrorLog();
    }
  }
}

if (!empty($_POST['submit']) && empty($errMsg)) {
  $page_num = 2;
}

// 画面終了ログ吐き出し
dStop();

// head,header呼び出し
include('parts/head.php');
include('parts/header.php'); ?>

<div class="l_cont hp_1col">
  <div class="l_cont_inner">
    <main class="l_main">
      <div class="l_main_inner">
        <?php if ($page_num === 2) : ?>
          <section class="b_media">
            <div class="b_media_inner">
              <div class="b_media_text">
                <p><?= $email ?>宛にメールアドレスを送信しました。<br>メールに記載されているURLからパスワード再設定が行えます。</p>
              </div><!-- /.b_media_text -->
            </div><!-- /.b_media_inner -->
          </section><!-- /.b_media -->
        <?php else : ?>
          <form action="" method="post" class="b_formUnit">
            <p>入力されたメールアドレス宛に<br>パスワード再発行用の認証キーをお送りいたします。</p>
            <span class="is_err"><?= getErrMsg('common') ?></span><!-- /.is_err -->
            <label class="b_form <?= isErr('email') ?>">
              <h3 class="e_headingLv4">メールアドレス</h3><!-- /.e_headingLv3 -->
              <span class="b_formMsg">
                <?= getErrMsg('email') ?>
              </span><br>
              <input class="b_formText" type="text" name="email" placeholder="メールアドレス">
            </label><!-- /.b_form -->
            <div class="e_btn_wrapper">
              <input type="submit" value="送信" class="e_btn" name="submit">
            </div><!-- /.e_btn_wrapper -->
          </form><!-- /.b_form -->
        <?php endif; ?>
      </div><!-- /.l main_inner -->
    </main><!-- /.l_main -->
    <?php include('parts/sidebar.php'); ?>
  </div><!-- /.l_cont_inner -->
</div><!-- /.l_cont -->
<?php
include('parts/footer.php');
