# Exchange-rates

## Get started

1. Создайте файл окружения
```
cp .env.example .env
```
2. Запустите проект
```
docker-compose up -d
```
3. Установите зависимости
```
docker-compose exec app composer install
```
4. Зайдите в app контейнер
```
docker-compose exec app bash
```
5. Установите проект
```
php artisan setup:project
```

## Fetch currencies

1. Запустите redis
```
php artisan queue:work redis > /dev/null 2>&1 &
```
2. Запустите команду получения списка валют
```
php artisan fetch:currencies
```
3. Запустите команду получения курсов валют (5 - необязательный параметр, количество дней)
```
php artisan fetch:currency-rate 5
```
По умолчанию команда заполняет курсы валют за 180 дней

Протестировать работу можно на главной странице сайта
