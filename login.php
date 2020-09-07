<?php

  require('function.php');

  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('「「「「ログインページ「「「「「「「「「');
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

  debugLogStart();

  require('auth.php');

  if (!empty($_POST)) {
    debug('ポスト送信があります。');

    $email = $_POST['email'];
    $pass = $_POST['pass'];

    validRequired($email,'email');
    validRequired($pass,'pass');

    if (empty($err_msg)) {
      validEmail($email,'email');
      validMaxLen($email,'email');

      validMaxLen($pass,'pass');
      validMinLen($pass,'pass');
      validHalf($pass,'pass');

      if (empty($err_msg)) {
        debug('バリデーションOKです！');

        try{
          $dbh = dbConnect();
          $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
          $data = array(':email'=> $email);

          $stmt = queryPost($dbh,$sql,$data);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);

          debug('クエリ結果の中身：'.print_r($result,true));

          if(!empty($result) && password_verify($pass,array_shift($result))){
            debug('パスワードがマッチしました。');

            $sesLimt = 60*60;
            $_SESSION['login_date'] = time();

            // if($pass_save){
            //   debug('ログイン保持にチェックがあります。');
            //   $_SESSION['login_limit'] = $sesLimt*24*30;
            // }else{
            //   debug('ログイン保持にチェックがありません。');
            //   $_SESSION['login_limit'] = $sesLimt;
            // }
            $_SESSION['login_limit'] = $sesLimt;
            $_SESSION['user_id'] = $result['id'];

            debug('セッション変数の中身：'.print_r($_SESSION,true));
            debug('プロフィールページへ遷移します。');
            header("Location: index.php");
          } else {
            debug('パスワードがアンマッチです。');
            $err_msg['common'] = MSG09;
          }
        } catch (Exception $e){
          error_log('エラー発生：'.$e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
  debug('画面表示終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>

  <?php
    $siteTitle = 'ログイン';
    require('head.php');
   ?>
  <body>
    <main>
      <section class="site-width position">
        <form class="form" method="post">
          <h1>ようこそ！memoへ</h1>
          <h2>ログイン</h2>
          <label>
            <p>email</p>
            <input type="text" name="email" placeholder="email" class="input" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="err-msg">
            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
          </div>
          <label>
            <p>password</p>
            <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
          <div class="err-msg">
            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
          </div>
          <input type="submit" name="btn" value="ログインする" class="btn">
        </form>
        <div class="link">
          <a href="signup.php">新規会員登録はこちら</a>
          <a href="premind.php">パスワードを忘れた方はこちらへ</a>
        </div>
      </section>
    </main>
  </body>
</html>
