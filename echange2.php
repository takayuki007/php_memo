<?php

require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「');
debug('「「「「「「「「「email変更確認コード入力「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

require('auth.php');

if (empty($_SESSION['auth_key'])) {
  header("Location: echange.php");
}

$dbFormData = getUser($_SESSION['user_id']);

if (!empty($_POST)) {
  debug('POST送信がありました。');
  debug('POST情報があります：' .print_r($_POST,true));

  $auth_key = $_POST['token'];

  validRequired($auth_key,'token');

  if (empty($err_msg)) {
    debug('未入力オッケーです。');

    validLength($auth_key,'token');
    validHalf($auth_key,'token');

    if (empty($err_msg)) {
      debug('バリデーションオッケーです');

      if ($auth_key !== $_SESSION['auth_key']) {
        $err_msg['common'] = MSG13;
      }

      if (time() > $_SESSION['auth_key_limit']) {
        $err_msg['common'] = MSG14;
      }

      if (empty($err_msg)) {
        debug('認証オッケー');

        try{
          $dbh = dbConnect();
          $sql = 'UPDATE users SET email = :email WHERE id = :id';
          $data = array(':email' => $_SESSION['new_email'],':id' = $dbFormData);

          $stmt = queryPost($dbh,$sql,$data);

          if ($stmt) {
            debug('クエリに成功');
            header("Location: index.php");
          } else {
            debug('クエリに失敗');
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
  <?php
  require('header.php');
   ?>
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
