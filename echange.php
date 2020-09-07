  <?php

  require('function.php');
  debug('「「「「「「「「「「「「「「「「「「「「「「');
  debug('「「「「「「「「「email変更「「「「「「「「「');
  debug('「「「「「「「「「「「「「「「「「「「「「「');

  debugLogStart();

  require('auth.php');

  $dbFormData = getUser($_SESSION['user_id']);

  debug('取得したユーザー情報：' .print_r($dbFormData,true));

  if (!empty($_POST)) {
    debug('POST送信があります。');
    debug('POST送信情報：' .print_r($_POST,true));

    $new_email = $_POST['new_email'];

    validRequired($new_email,'new_email');

    if (empty($err_msg)) {
      debug('未入力チェックOKです');

      validMaxLen($new_email,'new_email');
      validEmail($new_email,'new_email');
      validEmailDup($email);

      if (empty($err_msg)) {
        debug('バリデーションOKです。');

        $username = $dbFormData['name'];
        $auth_key = makeRandkey();

        $from = 'test@test.com';
        $to = $new_email;
        $subject = 'email変更確認コード送信';
        $comment = <<<EOT

        {$username}様

        email本人確認のため、確認コードをお送りいたします。
        確認コード：{$auth_key}
        確認コード入力ページ
        http://localhost/memo/echange2.php

        ※認証コードは30分有効です。

        カスタマーセンター
        EOT;

        sendMail($from,$to,$subject,$comment);

        $_SESSION['auth_key'] = $auth_key;
        $_SESSION['new_email'] = $new_email;
        $_SESSION['auth_key_limit'] = time() + (60*30);

        debug('セッション変数の中身：' .print_r($_SESSION,true));

        header("Location: echange2.php");
      } else {
        debug('メール送信失敗');
        $err_msg['common'] = MSG08;
      }
    }
  }
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<画面表示終了')
   ?>

  <?php
  $siteTitle = 'email変更';
  require('head.php');

   ?>

  <body>
    <?php
    require('header.php');
     ?>
    <main>
      <section class="site-width position">
        <form class="form" method="post">
          <h1>email変更</h1>
          <p>新しいemail</p>
          <input type="text" name="new_email" placeholder="新しいemail" class="input">
          <input type="submit" name="btn" value="確認コードを送信する" class="btn">
        </form>
      </section>
    </main>
  </body>
</html>
