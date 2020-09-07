<?php

  require('function.php');
  debug('「「「「「「「「「「「「「「「「「「「「「「');
  debug('「「「「「「「パスワードの変更「「「「「「「');
  debug('「「「「「「「「「「「「「「「「「「「「「「');

  debugLogStart();

  require('auth.php');

  $userData = getUser($_SESSION['user_id']);
  debug('ユーザー情報：'.print_r($userData,true));

  if (!empty($_POST)) {
    debug('POST送信されています。');
    debug('POST情報：' .print_r($_POST,true));

    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];
    $pass_new_re = $_POST['pass_new_re'];

    validRequired($pass_old,'$pass_old');
    validRequired($pass_new,'pass_new');
    validRequired($pass_new_re,'pass_new_re');

    if (empty($err_msg)) {
      debug('未入力チェックOKです。');

      validPass($pass_old,'pass_old');
      validPass($pass_new,'pass_new');

      if (!password_verify($pass_old,$userData['password'])) {
        $err_msg['pass_old'] = MSG11;
      }

      if ($pass_old === $pass_new) {
        $err_msg['pass_new'] = MSG12;
      }

      validMatch($pass_new,$pass_new_re,'pass_new_re');

      if (!empty($err_msg)) {
        debug('バリデーションOKです');

        try{
          $dbh = dbConnect();
          $sql = 'UPDATE users SET password = :pass WHERE id = :id AND delete_flg = 0';
          $data = array(':pass' => password_hash($pass_new,PASSWORD_DEFAULT), ':id' => $_SESSION['user_id'] );

          $stmt = queryPost($dbh,$sql,$data);

          if ($stmt) {
            debug('クエリ成功です。');
            $_SESSION['msg_success'] = SUC01;

            $username = ($userData['name'])? $userData['name']:'名無し';
            $from = 'test@test.com';
            $to = $userData['email'];
            $subject = 'パスワード変更通知';
            $comment = <<<EOT
            {$username}さん
            パスワードが変更されました。

            カスタマーセンター
            EOT;

            sendMail($from,$to,$subject,$comment);
            header("Location: index.php;");
          } else {
            debug('クエリ失敗しました。');
            $err_msg['common'] = MSG08;
          }
        } catch (Exception $e) {
          error_log('エラー発生：' .$e->getMessage());
          $err_msg['common'] = MSG08;
        }
      }
    }
  }
  debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<画面表示終了');
 ?>

<?php
$siteTitle = 'パスワードの変更';
require('head.php');

 ?>

<body>
  <?php
  require('header.php');
   ?>
    <main>
      <section class="site-width position">
        <form class="form" method="post">
          <h1>パスワードの変更</h1>
          <p>現在のパスワード</p>
          <input type="text" name="pass_old" placeholder="現在のパスワード" class="input">
          <p>新しいパスワード</p>
          <input type="text" name="pass_new" placeholder="新しいパスワード" class="input">
          <p>新しいパスワードをもう一度</p>
          <input type="text" name="pass_new_re" placeholder="新しいパスワードをもう一度" class="input">
          <input type="submit" name="btn" value="変更する" class="btn">
        </form>
      </section>
    </main>
  </body>
</html>
