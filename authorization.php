<?php
include 'path.php';
include SITE_ROOT . '/app/controllers/Users.php';
if (!empty($_SESSION['login']))header('location: ' . BASE_URL);
?>
<!doctype html>
<html lang="ru">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Custom Styling -->
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="assets/css/Style.css">

    <title>Тестовое задание</title>
</head>
<body>

<!-- HEADER START -->
<?php include SITE_ROOT . '/app/include/Header.php';?>
<!-- HEADER END-->

<!-- FORM -->
<div class="container">
    <div class="col-3"></div>
    <div class="col-4 registration-form">
        <form method="post" action="authorization.php">
            <h2>Авторизация</h2>
            <?php if(!empty($auth_info_msg)):?>
                <div class="err">
                    <p><?=$auth_info_msg?></p>
                </div>
            <?php endif;?>

            <div class="col-10">
                <label for="formGroupExampleInput" class="form-label">Номер телефона/Email</label>
                <input name="login" value="<?=$login?>" type="text" class="form-control" id="formGroupExampleInput" placeholder="Например Robocop@mai.com">
                <div id="loginHelp" class="form-text">Введите номер телефона(в формате +ХХХХХХХХХХХ) или почту, которую вы указывали при регистрации</div>
            </div>

            <div class="col-10">
                <label for="exampleInputPassword1" class="form-label">Пароль</label>
                <input name="password" type="password" class="form-control" id="exampleInputPassword1" placeholder="Введите пароль">
                <div id="passwordHelp" class="form-text">Введите пароль от вашего аккаунта</div>
            </div>

            <div id="captcha-container" class="smart-captcha col-10" data-sitekey="<?=SMARTCAPTCHA_CLIENT_KEY;?>">
                <input type="hidden" name="smart-token" value="<токен>">
            </div>

            <div class="submit col-10">
                <button type="submit" class="btn btn-primary" name="button-auth">Авторизоваться</button>
            </div>

            <div class="aut_help row col-10">
                <a class="registration" href="registration.php">Регистрация</a>
            </div>
        </form>
    </div>
    <div class="col-3"></div>
</div>
<!-- FORM END -->
</body>
</html>
