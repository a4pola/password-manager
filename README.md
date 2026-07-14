"Менеджер паролей на PHP + MySQL. Поддерживает регистрацию, шифрование, CRUD. Учебный проект для демонстрации навыков."
# 🔐 Менеджер паролей

Простое веб-приложение для хранения паролей. Написано на PHP + MySQL.

## Возможности
- Регистрация и вход (пароли хешируются через `password_hash`).
- Добавление, просмотр, удаление паролей.
- Пароли скрыты звёздочками с возможностью показать.
- Защита от SQL-инъекций (PDO + подготовленные запросы).
- Защита от XSS (`htmlspecialchars`).

## Как запустить локально
1. Установи XAMPP и запусти Apache + MySQL.
2. Создай базу данных `password_manager_db`.
3. Выполни SQL-запрос из файла `database.sql` (или создай таблицы вручную).
4. Помести папку `password-manager` в `htdocs`.
5. Открой браузер по адресу: `http://localhost/password-manager/`.

## Технологии
- PHP 7.4+
- MySQL (PDO)
- HTML5 / CSS3
- Git
## Скриншоты
<h3>Главная страница</h3>
<img width="694" height="627" alt="image" src="https://github.com/user-attachments/assets/a4e3cf70-6376-4138-b94b-d293af02f09c" />
<h3>Дашборд</h3>
<img width="932" height="546" alt="image" src="https://github.com/user-attachments/assets/ffd6a35d-3789-4896-bc32-85e97442710d" />


