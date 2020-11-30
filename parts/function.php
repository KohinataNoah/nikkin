<?php

// show indexes from users = usersテーブルのインデックスがついているやつを持ってくる
// alter table users drop index (keyname) = keynameのインデックスを消せる！

ini_set('error_log', 'php.log');
ini_set('log_errors', 'on');

// 定数定義

const MSG01 = '入力してください';
const MSG02 = '半角英数字で入力してください';
const MSG03 = 'Eメールアドレスの形式で入力してください';
const MSG04 = '文字以上で入力してください';
const MSG05 = '文字以下で入力してください';
const MSG06 = 'そのEメールアドレスは既に使用されています';
const MSG07 = 'エラーが発生しました。時間が経ってからやり直してください';
const MSG08 = 'パスワード再入力が違います';
const MSG09 = 'Eメールアドレスまたはパスワードが違います';
const MSG10 = '半角数字で入力してください';
const MSG11 = '新しいパスワードと古いパスワードが同じです';

// 変数定義
// エラーメッセージ格納用変数
$errMsg = array();


// 関数定義

// セッション
// セッションの保管場所を変更する
session_save_path('/var/tmp/'); // ここに置くと30日は削除されなくなる
// ガーベージコレクションが削除するセッションの有効期限を設定
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
// クッキーの有効期限を延長する
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
session_start();
// セッションIDを都度置き換える（セッションハイジャック対策）
session_regenerate_id();

// デバッグ用関数
require('function/debug.php');

// データベース系関数
require('function/database.php');

// 表示用関数
function h($s)
{
  $h = htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
  return $h;
}
// エラーメッセージ表示用関数
function getErrMsg($k)
{
  global $errMsg;
  if (!empty($errMsg[$k])) return h($errMsg[$k]);
}
// エラーメッセージ格納時、is_errを返す
function isErr($k)
{
  global $errMsg;
  if (!empty($errMsg[$k])) return 'is_err';
}
// 空かどうかの判定をし、空でなければ返してくれる関数
function isBlank($k)
{
  if (!empty($k)) return $k;
}
// フォーム入力保持用
function getFormData($s, $flg = false)
{
  // GET送信も入れることになったためフラグ判別
  if ($flg) {
    $method = $_GET;
  } else {
    $method = $_POST;
  }
  global $dbFormData;
  // ユーザーデータがある場合
  if (!empty($dbFormData)) {
    // フォームのエラーが有る場合
    if (!empty($errMsg[$s])) {
      // POSTにデータが有る場合
      if (isset($method[$s])) {
        return h($method[$s]);
      } else {
        // ない場合（ありえないとは思うが一応想定）DBの情報を表示
        return h($dbFormData[$s]);
      }
    } else {
      // POSTにでーたがあり、DBの情報と違う場合
      if (isset($method[$s]) && $method[$s] !== $dbFormData[$s]) {
        return h($method[$s]);
      } else {
        return h($dbFormData[$s]);
      }
    }
    // ユーザーデータがない場合
  } else {
    // POSTにデータが有る場合
    if (isset($method[$s])) {
      return h($method[$s]);
    }
  }
}

// セッションの中身を一度だけ取得する
function getSessionFlash($k)
{
  if (!empty($_SESSION[$k])) {
    $data = $_SESSION[$k];
    $_SESSION[$k] = '';
    return $data;
  }
}

// サニタイズ・バリデーションチェック関数
require('function/valid.php');

// その他
// メール送信
function sendMail($from, $to, $subject, $comment)
{
  if (!empty($to) || !empty($subject) || !empty($commnet)) {
    // 文字化けしないように設定
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");
    // メール送信
    $result = mb_send_mail($to, $subject, $comment, "From: " . $from);
    if ($result) {
      d('メールを送信しました。');
    } else {
      error_log('エラー発生：メールの送信に失敗しました。');
    }
  }
}
// 認証キー生成
function makeRandKey($length = 10)
{
  static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXWZ1234567890';
  $str = '';
  for ($i = 0; $i < $length; ++$i) {
    $str .= $chars[mt_rand(0, 61)];
  }
  return $str;
}
// ゲストログインの場合処理を抜けてマイページへ飛ばす
function exitGuest()
{
  global $guestFlg;
  if ($guestFlg) {
    d('ゲストログインです。');
    d('マイページへ遷移します。');
    $_SESSION['msg'] = '続けるには会員登録を行ってください。';
    header("Location:mypage.php");
    exit;
  }
}
