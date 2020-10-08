<?php


// デバッグフラグ用変数
$debug_flg = true;

// デバッグ用関数
function d($s)
{
  global $debug_flg;
  if ($debug_flg) {
    error_log($s);
  }
}
function dStart()
{
  global $siteTtl;
  d('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面処理開始');
  d('[ページ名]：' . $siteTtl);
  d('セッションID：' . session_id());
  d('セッション変数の中身：' . print_r($_SESSION, true));
  d('現在日時タイムスタンプ：' . time());
}
function dStop()
{
  d('画面処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
  d('');
}
function getErrorLog()
{
  global $e, $errMsg;
  error_log('エラー発生：' . $e->getMessage());
  $errMsg['common'] = MSG07;
}
