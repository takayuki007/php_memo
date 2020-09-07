<?php

 ini_set('log_errors','on');
 ini_set('error_log','php.log');

 $debug_flg = true;

 function debug($str){
   global $debug_flg;
   if (!empty($debug_flg)) {
     error_log('デバッグ：'.$str);
   }
 }

 session_save_path("/var/tmp/");
 ini_set('session.gc_maxlifetime',60*60*24*30);
 ini_set('session.cookie_lifetime',60*60*24*30);
 session_start();
 session_regenerate_id();

 function debugLogStart(){
   debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<画面処理開始');
   debug('セッションID：'.session_id());
   debug('セッション変数の中身：'.print_r($_SESSION,true));
   debug('現在日時タイムスタンプ：'.time());
   if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_date'])) {
     debug('ログイン期限日時タイムスタンプ：' .($_SESSION['login_date'] + $_SESSION['login_limit']));
   }
 }

 define('MSG01','入力必須項目です');
 define('MSG02','Email形式ではありません。');
 define('MSG03','こちらのEmailは既に登録されています。');
 define('MSG04','半角英数字でご入力ください。');
 define('MSG05','6文字以上でご入力ください。');
 define('MSG06','255文字以内でご入力ください。');
 define('MSG07','パスワード（再入力）が合っていません。');
 define('MSG08','エラーが発生しました。しばらく経ってからやり直してください。');
 define('MSG09','メールアドレスまたはパスワードが違います。');
 define('MSG10','古いパスワードが違います。');
 define('MSG11','古いパスワードと同じです。');
 define('MSG12','文字で入力してください。');
 define('MSG13','正しくありません。');
 define('MSG14','有効期限オーバーです。');
 define('SUC01','パスワードを変更しました。');

 $err_msg = array();

 function validRequired($str,$key){
   if(empty($str)){
     global $err_msg;
     $err_msg[$key] = MSG01;
   }
 }

 function validEmail($str,$key){
   if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
     global $err_msg;
     $err_msg[$key] = MSG02;
   }
 }

 function validEmailDup($email){
   global $err_msg;
   try{
     $dbh = dbConnect();
     $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
     $data = array(':email' => $email);
     $stmt = queryPost($dbh,$sql,$data);
     $result = $stmt -> fetch(PDO::FETCH_ASSOC);

     if(!empty(array_shift($result))){
       $err_msg['email'] = MSG03;
     }
   } catch (Exception $e) {
     error_log('エラー発生：' .$e->getMessage());
     $err_msg['common'] = MSG08;
   }
 }

 function validHalf($str,$key){
   if(!preg_match("/[a-zA-Z0-9]+$/",$str)){
     global $err_msg;
     $err_msg[$key] = MSG04;
   }
 }

 function validMinLen($str,$key,$min = 6){
   if(mb_strlen($str) < $min){
     global $err_msg;
     $err_msg[$key] = MSG05;
   }
 }

 function validMaxLen($str,$key,$max = 256){
   if (mb_strlen($str) > $max) {
     global $err_msg;
     $err_msg[$key] = MSG06;
   }
 }

 function validPass($str,$key){
   validHalf($str,$key);
   validMinLen($str,$key);
   validMaxLen($str,$key);
 }

 function validMatch($str1,$str2,$key){
   if ($str1 !== $str2) {
     global $err_msg;
     $err_msg[$key] = MSG07;
   }
 }

 function validLength($str,$key,$length = 8){
   if (mb_strlen($str) !== $length) {
     global $err_msg;
     $err_msg[$key] = $length.MSG12;
   }
 }

 function dbConnect(){
  //DBへの接続準備
  $dsn = 'mysql:dbname=memo;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  // PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}

function queryPost($dbh, $sql, $data){
  //クエリー作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。');
    $err_msg['common'] = MSG08;
    return 0;
  }
  debug('クエリ成功。');
  return $stmt;
}

function getUser($u_id){
  debug('ユーザー情報を取得します。');
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM users WHERE id = :u_id';
    $data = array(':u_id' => $u_id);

    $stmt = queryPost($dbh,$sql,$data);

    if ($stmt) {
      debug('クエリ成功しました。');
    } else {
      debug('クエリ失敗しました。');
    }
  } catch (Exception $e) {
    error_log('エラー発生：' .$e->getMessage());
    $err_msg['common'] = MSG08;
  }

  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getFormData($str){
  global $dbFormData;

  if (!empty($dbFormData)) {
    if (!empty($err_msg[$str])) {
      if (isset($_POST[$str])) {
        return $_POST[$str];
      } else {
        return $dbFormData[$str];
      }
    } else {
      if (isset($_POST[$str]) && $_POST[$str] !== $dbFormData[$str]) {
        return $_POST[$str];
      } else {
        return $dbFormData[$str];
      }
    }
  } else {
    if (isset($_POST[$str])) {
      return $_POST[$str];
    }
  }
}

function sendMail($from,$to,$subject,$comment){
  if (!empty($to) && !empty($subject) && !empty($comment)) {
    mb_language("Japanese");
    mb_internal_encodeing("UTF-8");

    $result = mb_send_mail($to,$subject,$comment, "From:" .$from);
    if ($result) {
      debug('メール送信しました。');
    } else {
      debug('【エラー発生】メールの送信に失敗しました。');
    }
  }
}

function getSessionFlash($key){
  if (!empty($_SESSION[$key])) {
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}

function makeRandkey($length = 8){
  static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $str = '';
  for ($i=0; $i < $length; ++$i) {
    $str .= $chars[mt_rand(0,61)];
  }
  return $str;
}

function getMemo($u_id,$m_id){
  debug('メモ情報を取得します。');
  debug('ユーザーID：'.$u_id);
  debug('メモID：'.$m_id);

  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM memo WHERE user_id = :u_id AND id = :m_id AND delete_flg = 0';
    $data = array(
      ':u_id' => $u_id,
      ':m_id' => $m_id
    );

    $stmt = queryPost($dbh,$sql,$data);

    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function uploadImg($file,$key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));

  if (isset($file['error']) && is_int($file['error'])) {
    try{
      switch ($file['error']) {
        case UPLOAD_ERR_OK:
          break;
        case UPLOAD_ERR_NO_FILE:
          throw new RuntimeException('ファイルが選択されていません。');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
          throw new RuntimeException('ファイルサイズが大きすぎます。');
        default:
          throw new RuntimeException('その他のエラーが発生しました。');
      }

      $type = @exif_imagetype($file['tmp_name']);
      if (!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)) {
        throw new RuntimeException('画像形式が未対応です。');
      }

      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);

      if (!move_uploaded_file($file['tmp_name'],$path)) {
        throw new RuntimeException('ファイル保存時にエラーが発生しました。');
      }

      chmod($pass,0644);

      debug('ファイルは正常にアップロードされました。');
      debug('ファイルパス：'.$path);

      return $path;
    } catch(RuntimeException $e) {
      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
}

function getMemoList(){
  debug('メモ情報を取得します。');

  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM memo';
    $data = array();

    $stmt = queryPost($dbh,$sql,$data);

    if ($stmt) {
      return $stmt->fetchAll();
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' .$e->getMessage());
  }
}

function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}

function getMemoOne($m_id){
  debug('メモ情報を取得します。');
  debug('メモID：'.$m_id);

  try{
    $dbh = dbConnect();
    $sql = 'SELECT m.id,m.user_id,m.contents,m.create_date,m.update_date FROM memo AS m LEFT JOIN users AS u ON m.user_id = u.id WHERE m.id = :m_id AND m.delete_flg = 0 AND u.delete_flg = 0';
    $data = array(':m_id' => $m_id);

    $stmt = queryPost($dbh,$sql,$data);

    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

function appendGetParam($arr_del_key){
  if (!empty($_GET)) {
    $str = '?';
    foreach ($_GET as $key => $val) {
      if (!in_array($key.$arr_del_key,true)) {
        $str .= $key.'='.$val.'&';
      }
    }
    $str = mb_substr($str,0,-1,"UTF-8");
    echo $str;
  }
}

 ?>
