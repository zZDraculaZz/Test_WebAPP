<?php
include SITE_ROOT . '/settings.php';

// confirm user auth
function userAuth($my_db, $email)
{
    $user = $my_db->takeArrayData('users', 'email', $email);
    $_SESSION['id'] = $user['id'];
    $_SESSION['login'] = $user['login'];
    header('location: ' . BASE_URL);
}

// yandex captcha - check user captcha
function checkCaptcha($token) {
    $ch = curl_init();
    $args = http_build_query([
        'secret' => SMARTCAPTCHA_SERVER_KEY,
        'token' => $token,
        'ip' => $_SERVER['REMOTE_ADDR'],
    ]);
    curl_setopt($ch, CURLOPT_URL, "https://smartcaptcha.yandexcloud.net/validate?$args");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

    $server_output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
        return true;
    }
    $resp = json_decode($server_output);
    return $resp->status === 'ok';
}

// registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['button-reg'])){
    $login = ucfirst(mb_strtolower(trim($_POST['login']), 'utf-8'));
    $email = mb_strtolower(trim($_POST['email']), 'utf-8');
    if((int)substr(trim($_POST['phone']),1)){
        $phone = (int) substr(trim($_POST['phone']),1);
    }else{$phone='+';}

    $pass_first = trim($_POST['pass-first']);
    $pass_second = trim($_POST['pass-second']);

    if ($login === '' or $email === '' or $pass_first === '' or $pass_second === '' or $phone == '') {
        $reg_info_msg = 'Не все поля заполнены!';
        if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
    }elseif (mb_strlen ($login, 'UTF-8') <= 2) {
        $reg_info_msg = 'Логин должен быть более 2-х символов!';
        if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
    }elseif (!(mb_strlen ($phone, 'UTF-8') === 11 or mb_strlen ($phone,'UTF-8') === 12) or !is_int($phone)){
        $reg_info_msg = 'Номер указан в неверном формате!';
        if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
    }elseif (mb_strlen ($pass_first, 'UTF-8') <= 5){
        $reg_info_msg = 'Пароль должен быть более 5-и символов!';
        if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
    }elseif ($pass_first != $pass_second) {
        $reg_info_msg = 'Пароли в обеих полях должны совпадать!';
        if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
    }else{
        if ($my_db->takeArrayData('users', 'login', $login)) {
            $reg_info_msg = 'Такой логин уже зарегистрирован!';
            if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
        }elseif ($my_db->takeArrayData('users', 'email', $email)){
            $reg_info_msg = 'Такой email уже зарегистрирован!';
            if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
        }elseif ($my_db->takeArrayData('users', 'phone', $phone)){
            $reg_info_msg = 'Такой мобильный телефон уже зарегистрирован!';
            if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
        }else{
            $password = password_hash($pass_first, PASSWORD_DEFAULT);
            $my_db->createUser($email, $login, $password, $phone);
            userAuth($my_db, $email);
        }
    }
}else{
    $phone = '+';
    $login = '';
    $email = '';
}

// authorization
if($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['button-auth'])) {
    $is_phone = False;
    $login = trim($_POST['login']);
    $token = $_POST["smart-token"];
    if (is_int(substr($login, 1)) and !empty($login)) {
        $is_phone = True;
        $login = (int)substr($login, 1);
    }else{$login = mb_strtolower($login);}
    $password = trim($_POST['password']);

    if (!checkCaptcha($token)) {
        $auth_info_msg = 'Проверка на робота не выполнена!';
    } elseif ($login === '' or $password === '') {
        $auth_info_msg = 'Не все поля заполнены!';
        if (!(substr($login, 0) === '+') and $is_phone) $login = (string)('+' . $login);
    } elseif ((!(mb_strlen ($login, 'UTF-8') === 11 or mb_strlen ($login,'UTF-8') === 12) or !is_int($login)) and $is_phone){
        $auth_info_msg = 'Номер указан в неверном формате!';
        if(!(substr($login, 0) === '+'))$login = (string)('+' . $login);
    } elseif ($my_db->takeArrayData('users', 'phone', $login)){
        if(password_verify($password, $my_db->takeArrayData('users', 'phone', $login)['password'])) {
            userAuth($my_db, $my_db->takeArrayData('users', 'phone', $login)['email']);
        }else{
            $auth_info_msg = 'Неверный пароль!';
            if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
        }
    } elseif ($my_db->takeArrayData('users', 'email', $login)){
        if(password_verify($password, $my_db->takeArrayData('users', 'email', $login)['password'])) {
            userAuth($my_db, $login);
        }else{
            $auth_info_msg = 'Неверный пароль!';
        }
    } else{
        $auth_info_msg = 'Такого пользователя не существует!';
    }
}

// user press "edit profile"
if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['button-change'])){
    $data_change = True;
    $user = $my_db->takeArrayData('users', 'id', $_SESSION['id']);
    $login = $user['login'];
    $email = $user['email'];
    $phone = '+' . $user['phone'];
}

// edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['button-confirm-change'])){
    $data_change = True;
    $id = $_SESSION["id"];
    $login = ucfirst(mb_strtolower(trim($_POST['login']), 'utf-8'));
    $email = mb_strtolower(trim($_POST['email']), 'utf-8');
    if((int)substr(trim($_POST['phone']),1)){
        $phone = (int)substr(trim($_POST['phone']),1);
    }else{$phone="+";}
    $pass_first = trim($_POST["pass-first"]);
    $pass_second = trim($_POST["pass-second"]);

    if ($login === '' or $email === '' or $phone == '') {
        $change_info_msg = 'Не все поля заполнены!';
        if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
    }elseif (mb_strlen ($login, 'UTF-8') <= 2) {
        $change_info_msg = 'Логин должен быть более 2-х символов!';
        if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
    }elseif (!(mb_strlen ($phone, 'UTF-8') === 11 or mb_strlen ($phone,'UTF-8') === 12) or !is_int($phone)){
        $change_info_msg = 'Номер указан в неверном формате!';
        if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
    }elseif (mb_strlen ($pass_first, 'UTF-8') <= 5 and !empty($pass_first)){
        $change_info_msg = 'Пароль должен быть более 5-и символов!';
        if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
    }elseif ($pass_first != $pass_second) {
        $change_info_msg = 'Пароли в обеих полях должны совпадать!';
        if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
    }else{
        if ($my_db->takeArrayData('users', 'login', $login) and $my_db->takeArrayData('users', 'login', $login)['id']!=$id) {
            $change_info_msg = 'Такой логин уже зарегистрирован!';
            if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
        }elseif ($my_db->takeArrayData('users', 'email', $email) and $my_db->takeArrayData('users', 'email', $email)['id']!=$id){
            $change_info_msg = 'Такой email уже зарегистрирован!';
            if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
        }elseif ($my_db->takeArrayData('users', 'phone', $phone) and $my_db->takeArrayData('users', 'phone', $phone)['id']!=$id){
            $change_info_msg = 'Такой мобильный телефон уже зарегистрирован!';
            if(!(substr($phone, 0) === '+'))$phone = (string)('+' . $phone);
        }else{
            $my_db->updateData("users","login",$login,"id",$id);
            $my_db->updateData("users","email",$email,"id",$id);
            $my_db->updateData("users","phone",$phone,"id",$id);
            if (strlen($pass_first)>0)
            {
                $password = password_hash($pass_first, PASSWORD_DEFAULT);
                $my_db->updateData("users","password",$password,"id",$id);
            }
            $_SESSION['login'] = $login;
            unset($data_change);
        }
    }
}