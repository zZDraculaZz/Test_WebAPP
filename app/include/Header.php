<header>
    <div class="container">
        <div class="row col-10">
            <div class="col-3">
                <h1>
                    <a href="<?= BASE_URL;?>">Тестовое задание</a>
                </h1>
            </div>
            <div  class = "col-7">
                <nav class = "header-buttons">
                    <ul>
                        <li>
                            <a href="<?= BASE_URL;?>">Главная</a>
                        </li>
                        <li>
                            <?php if(isset($_SESSION['id'])): ?>
                                <a href="<?= BASE_URL . 'office.php';?>"><?= $_SESSION['login'];?></a>
                                <ul>
                                    <li>
                                        <a href="<?= BASE_URL . 'office.php';?>">Кабинет</a>
                                    </li>
                                    <li>
                                        <a href="<?= BASE_URL . "logout.php";?>">Выход</a>
                                    </li>
                                </ul>
                            <?php else: ?>
                                <a href="<?= BASE_URL . "authorization.php";?>">Вход</a>
                                <ul>
                                    <li>
                                        <a href="<?= BASE_URL . "registration.php";?>"> Регистрация</a>
                                    </li>
                                </ul>
                            <?php endif; ?>
                        </li>

                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>