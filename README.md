# mygento
### Тестовый проект на вакансию Symfony middle разработчика в компанию Mygento
____

#### Текст задания:
Реализовать средствами Symfony роуты:

1. Голосование пользователя за новость. Условия: пользователь может как проголосовать, так и убрать голос за новость. Один пользователь может только 1 раз проголосовать за новость

2. Отдача всех новостей и их лайков.

#### Запуск проекта:
1. Открыть корневую директорию проекта в консоли запустить проект командой "docker-compose up -d".
2. При необходимости изменить настройки окружения по-умолчанию в файлах .env и .env.test.
3. Открыть корневую директорию проекта в консоли и перейти в контейнер с приложением командой "docker-compose exec db sh", после чего создать базу данных командой "php bin/console service:db:create", загрузить в БД примеси данных с помощью команды "php bin/console doctrine:fixtures:load".
4. Открыть проект в браузере (адрес по-умолчанию: http://127.0.0.1/, настраивается в файле docker-compose.yml) и войти с помощью предустановленных логина и пароля (UserOne / password1 или UserTwo / password2).

#### Руководство по эксплуатации:
1. Для получения доступа к системе, в ней необходимо авторизоваться по маршруту "/login".
2. Для просмотра списка новостей следует авторизоваться в системе, после чего перейти по маршруту "/".
3. Для установки/снятия лайка с новости, следует нажать кнопку "нравится" под соответствующей новостью.
