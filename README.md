<img width="694" height="627" alt="image" src="https://github.com/user-attachments/assets/784d3bdf-3fd3-49a2-8020-851f141abf39" /># password-manager
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

![<img width="694" height="627" alt="image" src="https://github.com/user-attachments/assets/b3b6d0e6-8568-4f19-abe1-9830a6795dc8" />
](screenshots/login.png)
![Дашборд](screenshots/dashboard.png)
