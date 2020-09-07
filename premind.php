<?php

  require('function.php');
  debug('「「「「「「「「「「「「「「「「「「「「「「「');
  debug('「「「「「「パスワード再発行ページ「「「「「「');
  debug('「「「「「「「「「「「「「「「「「「「「「「「');

  debugLogStart();

  if (!empty($_POST)) {
    debug('POST送信あります！');
    debug('POST情報：' .print_r($_POST,true));

    $email = $_POST['email'];

    validRequired($email,'email');

    if (empty($err_msg)) {
      debug('未入力チェックOKです。');

      validMaxLen($email,'email');
      validEmail($email,'email');

      if (empty($err_msg)) {
        debug('バリデーションOKです');

        try{
          $dbh = dbConnect();
          $sql = 'SELECT count(*) FROM users WHERE email = :email';
          $data = array(':email' => $email);

          $stmt = queryPost($dbh,$sql,$data);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);

          if ($stmt && array_shift($result)) {
            debug('クエリ成功。DB登録あり');

            $auth_key = makeRandkey();

            $from = 'test@test.com';
            $to = $email;
            $subject = 'パスワード変更通知';
            $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：http://localhost/memo/premind2.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります

認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
http://localhost/memo/premind.php

カスタマーセンター
EOT;

            sendMail($from,$to,$subject,$comment);

            $_SESSION['auth_key'] = $auth_key;
            $_SESSION['auth_email'] = $email;
            $_SESSION['auth_key_limit'] = time() + (60*30);

            debug('セッション変数の中身：' .print_r($_SESSION,true));

            header("Location: premind2.php");
          } else {
            debug('クエリに失敗したか、emailがDBに登録されていません。');
            $err_msg['common'] = MSG08;
          }
        } catch (Exception $e) {
          error_log('エラー発生：' .$e->getMessage());
          $err_msg['common'] = MSG08;
        }
      }
    }

  }
 ?>

<?php
$siteTitle = 'パスワード再発行';
require('head.php');

 ?>

<body>
  <main>
    <section class="site-width position">
      <form class="form" method="post">
        <h1>パスワード再発行</h1>
        <p>email</p>
        <input type="text" name="email" placeholder="email" class="input">
        <input type="submit" name="btn" value="確認コードを送信する" class="btn">
      </form>
    </section>
  </main>
</body>
</html>
