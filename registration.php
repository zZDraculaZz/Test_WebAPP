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
        <form method="post" action="registration.php">
            <h2>Регистрация</h2>
            <?php if(!empty($reg_info_msg)):?>
            <div class="err">
                <p><?=$reg_info_msg?></p>
            </div>
            <?php endif;?>

            <div class="col-10">
                <label for="formGroupExampleInput" class="form-label">Логин</label>
                <input name="login" value="<?=$login?>" type="text" class="form-control" id="formGroupExampleInput" placeholder="Введите ваше имя">
            </div>

            <div class="col-10">
                <label for="exampleInputEmail1" class="form-label">Email адрес</label>
                <input name="email" type="email" value="<?=$email?>" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="example@gav.com">
                <div id="emailHelp" class="form-text">Укажите вашу действительную почту</div>
            </div>

            <div class="col-10">
                <label for="exampleInputPhone" class="form-label">Ваш номер телефона</label>
                <input name="phone" type="phone" value="<?=$phone?>" class="form-control" id="exampleInputPhone" aria-describedby="phoneHelp" placeholder="Введите ваш номер телефона">
                <div id="phoneHelp" class="form-text">Укажите ваш личный номер телефона в формате +XXXXXXXXXXX</div>
            </div>

            <div class="col-10">
                <label for="exampleInputPassword1" class="form-label">Пароль</label>
                <input name="pass-first" type="password" class="form-control" id="exampleInputPassword1" placeholder="Введите пароль">
                <div id="passwordHelp" class="form-text">Постарайтесь придумать сложный пароль</div>
            </div>

            <div class="col-10">
                <label for="exampleInputPassword2" class="form-label">Подтверждение пароля</label>
                <input name="pass-second" type="password" class="form-control" id="exampleInputPassword2" placeholder="Повторите пароль">
            </div>

            <div class="submit col-10">
                <button type="submit" class="btn btn-primary" name="button-reg">Зарегистрироваться</button>
            </div>

            <div class="aut_help row col-10">
                    <a class="authorization" href="authorization.php">Войти</a>
            </div>
        </form>
    </div>
    <div class="col-3"></div>
</div>
<!-- FORM END -->
</body>
</html>
