<?php
  require('function.php');

  if (!empty($_POST)) {
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $re_pass = $_POST['re_pass'];

    validRequired($email,'email');
    validRequired($pass,'pass');
    validRequired($re_pass,'re_pass');

    if (empty($err_msg)) {

      validEmail($email,'email');
      validEmailDup($email);
      validMaxLen($email,'email');

      validHalf($pass,'pass');
      validMaxLen($pass,'pass');
      validMinLen($pass,'pass');

      validMaxLen($re_pass,'re_pass');
      validMinLen($re_pass,'re_pass');

      if (empty($err_msg)) {

        validMatch($pass,$re_pass,'re_pass');

        if (empty($err_msg)) {

          try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO users (email,password,create_date,login_time) VALUES (:email,:pass,:create_date,:login_time)';
            $data = array(
              ':email' => $email,
              ':pass' => password_hash($pass,PASSWORD_DEFAULT),
              ':create_date' => date('Y-m-d H:i:s'),
              ':login_time' => date('Y-m-d H:i:s')
            );
            $stmt = queryPost($dbh,$sql,$data);

            if ($stmt) {
              $sesLimt = 60*60;

              $_SESSION['login_date'] = time();
              $_SESSION['login_limit'] = $sesLimt;

              $_SESSION['user_id'] = $dbh->lastInsertId();

              debug('セッション変数の中身：'.$_SESSION,true);
              header("Location:prof.php");
            } else {
              error_log('クエリに失敗しました。');
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
  $siteTitle = '会員登録';
  require('head.php');
  ?>
  <body>
    <main>
      <section class="site-width position">
        <form class="form" method="post">
          <h1>ようこそ！memoへ</h1>
          <h2>新規会員登録</h2>
          <div class="err-msg">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common'] ?>
          </div>
          <label class="label">
            <p>email</p>
            <input type="text" name="email" placeholder="email" class="input" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="err-msg">
            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
          </div>
          <label class="label">
            <p>password</p>
            <p>6文字以上でご入力ください</p>
            <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
          <div class="err-msg">
            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
          </div>
          <label class="label">
            <p>re-password</p>
            <input type="password" name="re_pass" placeholder="パスワード再入力" value="<?php if(!empty($_POST['re_pass'])) echo $_POST['re_pass']; ?>">
          </label>
          <div class="err-msg">
            <?php if(!empty($err_msg['re_pass'])) echo $err_msg['re_pass']; ?>
          </div>
          <input type="submit" name="btn" value="登録する" class="btn">
        </form>
        <div class="link">
          <a href="login.php">ログインはこちら</a>
        </div>
      </section>
    </main>
  </body>
</html>
