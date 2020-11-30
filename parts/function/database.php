<?php

const DATABASE_LOCAL = false;

// データベース接続準備
function connectDB()
{
  d('データベースに接続します。');
  if (DATABASE_LOCAL) {
    $dsn = "mysql:localhost=8888;dbname=nikkin;charset=utf8";
    $user = 'root';
    $password = 'root';
  } else {
    $db = parse_url($_SERVER['CLEARDB_DATABASE_URL']);
    $db['dbname'] = ltrim($db['path'], '/');
    $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
    $user = $db['user'];
    $password = $db['pass'];
  }
  $options = array(
    // 何かあったらエラーを投げてもらう
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // 取得結果に連想配列形式を設定する
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使用、結果を一度に取得し鯖負荷軽減
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
  d('データベースに接続しました。');
}
// クエリ
function queryPost($dbh, $sql, $data)
{
  $stmt = $dbh->prepare($sql);
  if (!$stmt->execute($data)) {
    global $errMsg;
    d('クエリ失敗しました。');
    d('失敗したクエリ：' . print_r($stmt, true));
    $errMsg['common'] = MSG07;
    return 0;
  }
  d('クエリ成功しました。');
  return $stmt;
}
// ユーザー情報取得陽関数
function getUser($user_id)
{
  d('ユーザー情報を取得します。');
  try {
    $dbh = connectDB();
    $sql = 'SELECT * FROM users WHERE id = :user_id AND delete_flg = 0';
    $data = array(':user_id' => $user_id);
    $stmt = queryPost($dbh, $sql, $data);
    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
      d('ユーザー情報の中身：' . print_r($stmt, true));
    } else {
      return false;
    }
  } catch (Exception $e) {
    getErrorLog();
  }
}
function getRoutine($user_id, $routine_id)
{
  d('日課情報を取得します。');
  d('ユーザーID：' . print_r($user_id, true));
  d('日課ID：' . print_r($routine_id, true));
  try {
    $dbh = connectDB();
    $sql = 'SELECT * FROM routine WHERE user_id = :user_id AND id = :routine_id AND delete_flg = 0';
    $data = array('user_id' => $user_id, ':routine_id' => $routine_id);
    $stmt = queryPost($dbh, $sql, $data);
    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch (Exception $e) {
    getErrorLog();
  }
}
function getDiary($user_id, $diary_id)
{
  d('日記情報を取得します。');
  d('ユーザーID：' . print_r($user_id, true));
  d('日記ID：' . print_r($diary_id, true));
  try {
    $dbh = connectDB();
    $sql = 'SELECT * FROM diary WHERE user_id = :user_id AND id = :diary_id AND delete_flg = 0';
    $data = array('user_id' => $user_id, ':diary_id' => $diary_id);
    $stmt = queryPost($dbh, $sql, $data);
    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch (Exception $e) {
    getErrorLog();
  }
}
// 画像アップロード用関数
function uploadImg($file, $k)
{
  d('画像アップロード処理を開始します。');
  d('FILE情報：' . print_r($file, true));
  if (isset($file['error']) && is_int($file['error'])) {
    try {
      // 以下、バリデーション
      // $file['error']の中身を確認。配列内には[UPLOAD_ERR_OK]などの定数が入っている。
      // [UPLOAD_ERR_OK]などの定数はphpでファイルアップロード時に自動的に定義され、値として0や1などの数値が入っている。
      switch ($file['error']) {
        case UPLOAD_ERR_OK: //OK
          break;
        case UPLOAD_ERR_NO_FILE: //ファイル未選択
          throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE: //php.ini定義の最大サイズを超過した場合
        case UPLOAD_ERR_FORM_SIZE: //フォーム定義の最大サイズを超過した場合
          throw new RuntimeException('ファイルサイズが大きすぎます');
        default: //その他の場合
          throw new RuntimeException('その他のエラーが発生しました。');
      }
      // バリデーションここまで

      // $file['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェック
      // exif_imagetype 関数は[IMAGETYPE_GIF],[IMAGETYPE_JPEG]などの定数を返す
      $type = @exif_imagetype($file['tmp_name']);
      // 第三引数にtrueをつけると厳密な定義となる（強く推奨）
      if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
        throw new RuntimeException('画像形式が未対応です。');
      }
      // ファイルデータからSHA-1ハッシュをとってファイル名を決定し、ファイルを保存する
      // ハッシュ化をしないとファイル名がそのままとなり、同じファイル名の画像がアップロードされる可能性がある
      // DBにパスを保存した場合、どちらの画像のパスなのか判断つかなくなってしまうためハッシュ化して保存する
      // image_type_to_extension関数はファイルの拡張子を取得するもの
      $path = 'uploads/' . sha1_file($file['tmp_name']) . image_type_to_extension($type);
      if (!move_uploaded_file($file['tmp_name'], $path)) {
        throw new RuntimeException('ファイル保存時にエラーが発生しました。');
      }
      chmod($path, 0644);
      d('ファイルは正常に保存されました。');
      d('ファイルパス：' . $path);
      return $path;
    } catch (RuntimeException $e) {
      d($e->getMessage());
      global $errMsg;
      $errMsg[$k] = $e->getMessage();
    }
  }
}
