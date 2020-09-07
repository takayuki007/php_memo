<?php

  require('function.php');
  debug('「「「「「「「「「「「「「「「「「「「「「「「');
  debug('「「「「パスワード変更確認コード入力画面「「「');
  debug('「「「「「「「「「「「「「「「「「「「「「「「');
  debugLogStart();

  if (empty($_SESSION['auth_key'])) {
    header("Location: premind.php");
  }

  if (!empty($_POST)) {
    debug('POST送信があります。');
    debug('POST情報：' .print_r($_POST,true));

    $auth_key = $_POST['token'];

    validRequired($auth_key,'token');

    if (empty($err_msg)) {
      debug('未入力チェックOKです。');

      validLength($auth_key,'token');
      validHalf($auth_key,'token');

      if (empty($err_msg)) {
        debug('バリデーションOKです。');

        if ($auth_key !== $_SESSION['auth_key']) {
          $err_msg['common'] = MSG13;
        }

        if (time() > $_SESSION['auth_key_limit']) {
          $err_msg['common'] = MSG14;
        }

        if (empty($err_msg)) {
          debug('認証OKです');

          $pass = makeRandkey();

          try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
            $data = array(
              ':pass' => password_hash($pass,POSSWORD_DEFAULT),
               ':email' => $_SESSION['auth_email'] );

               $stmt = queryPost($dbh,$sql,$data);

               if ($stmt) {
                 debug('クエリ成功');

                 $from = 'test@test.com';
                 $to = $_SESSION['auth_email'];
                 $subject = 'パスワード再発行完了';
                 $comment = <<<EOT
     本メールアドレス宛にパスワードの再発行を致しました。
     下記のURLにて再発行パスワードをご入力頂き、ログインください。

     ログインページ：http://localhost/memo/login.php
     再発行パスワード：{$pass}
     ※ログイン後、パスワードのご変更をお願い致します

     カスタマーセンター
     EOT;

             sendMail($from,$to,$subject,$comment);

             session_unset();
             debug('セッション変数の中身：' .print_r($_SESSION,true));

             header("Location: login.php");
           } else {
             debug('クエリ失敗');
             $err_msg['common'] = MSG08;
           }
         } catch (Exception $e) {
           error_log('エラー発生：' .$e->getMessage());
           $err_msg['common'] = MSG08;
         }
        }
      }
    }
  }
 ?>


<?php
$siteTitle = '確認コードを入力';
require('head.php');

 ?>

<body>
    <main>
      <section class="site-width position">
        <form class="form" method="post">
          <h1>確認コードを入力</h1>
          <p>確認コード</p>
          <input type="text" name="token" placeholder="確認コード" class="input">
          <input type="submit" name="btn" value="変更する" class="btn">
        </form>
      </section>
    </main>
  </body>
</html>
