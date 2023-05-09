# Тестовое задание для Freedom Finance

## Задача
Разработать backend приложение для получения курсов, кроскурсов ЦБ.

**Требования:**
- На входе: дата, код валюты, код базовой валюты (по-умолчанию RUR)
- Получать курсы с [http://cbr.ru](http://cbr.ru)
- На выходе: значение курса и разница с предыдущим торговым днем
- Кешировать данные с [http://cbr.ru](http://cbr.ru)
- Продемонстрировать навыки работы с брокерами сообщений и
реализовать сбор данных с cbr за 180 предыдущих дней с помощью 
воркера через консольную команду

## Установка и запуск проекта

Скопировать файл `.env.example` в `.env` в корне проекта.

Изменить при необходимости данные в секциях:
- `DB_DATABASE=` - имя базы данных
- `DB_PASSWORD=` - пароль пользователя

Указать желаемые(или оставить по умолчанию) настройки в секциях:
- `APP_PORT=80` - порт на котором будет работать приложение. Требуется для nginx
- `SWAGGER_PORT=8000` - порт на котором будет работать swagger
- `DB_FORWARD_PORT=5432` - порт для базы данных, для доступа из вне контейнера
- `TIMEZONE='Europe/Moscow'` - указать текущий часовой пояс

Для установки проекта требуется выполнить команду `make install` в случае Linux/MacOS.
После завершения работы команды в консоли можно запустить приложение командой
`docker compose up` или `docker compose up -d`

В случае использования Windows, проделать команды ниже поочередно:
```
docker compose build
docker compose run --rm php composer install
docker compose run --rm php php artisan key:generate
docker compose run --rm php artisan storage:link
docker compose up -d
docker compose exec php php artisan migrate
docker compose exec php php artisan db:seed
```

Для запуска очереди выполнить команду в отдельной консоли
```docker compose exec php php artisan queue:work```
Команду можно выполнить в стольки консолях, сколько требуется экземпляров приложения работающих параллельно.

## Swagger
Сваггер доступен по ссылке `http://localhost:8000` по умолчанию, либо по указанному в `.env` порту

## Консольные команды

### Сбор данных с сайта ЦБ
`docker compose exec php php artisan app:grab-currencies` выполнить сбор данных за 180 дней.

`docker compose exec php php artisan app:grab-currencies --days={количество дней}` если требуется другое количество дней.
