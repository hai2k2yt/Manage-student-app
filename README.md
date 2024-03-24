# Manage student app
PHP 8.1<br>
Mysql 8.0<br>
Composer, npm
## Clone project
https://github.com/hai2k2yt/Manage-student-app.git

## Config env

Duplicate file .env từ file example và cấu hình lại những trường sau
- DB_CONNECTION=
- DB_HOST=
- DB_PORT=3306
- DB_DATABASE=
- DB_USERNAME=
- DB_PASSWORD=

## Run command

- composer install - Build vendor file
- npm install - Build node_modules
- php artisan key:generate - tạo app key trong .env
- php artisan jwt:secret - tạo mã jwt trong .env
- php artisan migrate - tạo bảng database
- php artisan db:seed - tạo dữ liệu mẫu trong db

## Chạy chương trình
- php artisan serve
