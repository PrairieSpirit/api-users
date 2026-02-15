
# API: Users [Symfony]

REST API з реалізацією одиного ендпоінта, що описує сутність користувача.

## Завдання
Реалізувати один ендпоінт REST api, що описує сутність користувача.

## Умова 
Реалізувати ендпоінт по урлу:

> /v1/api/users
>
>

**Методи**

Ендпоінт повинен приймати в себе 4 типи запитів:
**GET**, **POST**, **PUT**, **DELETE**.


## Обовʼязкові атрибути в запитах
| HTTP метод | Обовʼязкові поля               |
| ---------- | ------------------------------ |
| POST       | `login`, `phone`, `pass`       |
| PUT        | `id`, `login`, `phone`, `pass` |
| GET        | `id`                           |
| DELETE     | `id`                           |

---

## Обовʼязкові атрибути у відповідях

| HTTP метод | Поля у відповіді                                        |
| ---------- | ------------------------------------------------------- |
| POST       | `id`, `login`, `phone`, `pass`                          |
| PUT        | `id`                                                    |
| GET        | `login`, `phone`, `pass`                                |
| DELETE     | не повертає поля (факт видалення, наприклад `"DELETE"`) |


**Сутність користувача**

Сутність користувача складається з 4 атрибутів **id, login, phone, pass**. На всіх атрибутах повинні бути правила валідації: довжина - не більше 8 символів. Також закрита можливість дублікату за атрибутами **login, pass** валідацією: повісити на них унікальний складовий індекс.



**Авторизація**

Застосунок закритий зовні, спосіб авторизації Bearer. Два типи ролей:
"root" - має право на GET, POST, PUT, DELETE без обмежень;
"user" - має право на GET, POST, PUT, але тільки в рамках свого користувача, видаляти не може.


**Обробка помилок**

Усі помилки, передбачені або раптові, повинні бути повернені у вигляді json без трейсу.

**Вимоги**

Фреймворк - **Symfony**, база даних - **Mysql**.

Використання будь-яких компонентів symfony, але FOS* компонентом symfony вже не рахуємо. Покриття тестами буде плюсом, але не є обов'язковим.

## Результат

Виконане тестове завдання разом із дампом бд розмістити на github та надіслати посилання на репозиторій.

---

## Встановлення та запуск

1. Клонувати репозиторій або використати поточну директорію

2. Створити файл `.env` (якщо його немає):

```bash
cp .env.distr .env
```

3. Запустити Docker-контейнери:

```bash
docker-compose up -d --build
```

4. Встановити залежності (виконується автоматично під час збірки, але можна запустити вручну):

```bash
docker-compose exec php composer install
```

5. Очистка кеш Symfony (за потреби):

```bash
docker-compose exec php php bin/console cache:clear
```

6. Відкрити в браузері:

```
http://localhost:8080
```

7.  Робота з БД

**Створення бази даних (якщо її ще немає)**

```bash
docker-compose exec php php bin/console doctrine:database:create --if-not-exists
````

**Видалення бази даних**

```bash
docker-compose exec php php bin/console doctrine:database:create
````

**Створення схеми бази даних**

```bash
docker compose exec php php bin/console doctrine:schema:create --no-interaction
```

**Скидання бази та повторне створення схеми**

```bash
docker compose exec php php bin/console doctrine:database:drop --force --if-exists
docker compose exec php php bin/console doctrine:database:create
docker compose exec php php bin/console doctrine:schema:create --no-interaction
```

**Завантаження фікстур (тестових або початкових даних)**

```bash
docker-compose php bin/console doctrine:fixtures:load --no-interaction
```

8. Запуск тестів:


```bash
docker compose exec php sh -c 'php bin/phpunit'
```




## Зупинка проєкту

```bash
docker-compose down
```

---

## API Specification

### Загальна інформація

* **Base URL**: `/v1/api/users`
* **Методи**: `GET`, `POST`, `PUT`, `DELETE`
* **Формат запитів та відповідей**: JSON
* **Авторизація**: Bearer Token

---

## Сутність користувача (User)

### Поля

* `id`
* `login`
* `phone`
* `pass`





---

## Структура проєкту

```
api_users/
├── config/           # Конфігурація Symfony
├── docker/           # Конфігурація Docker
├── public/           # Публічна директорія
├── src/              # Вихідний код застосунку
│   ├── Controller/   # Контролери
│   │   ├── V1/       # Версія 1 API
│   │   │   └── User/ # Дії для користувачів
│   ├── DataFixtures/ # Попередні дані для бази
│   ├── DTO/          # Data Transfer Objects
│   ├── Entity/       # Doctrine Entities
│   ├── Enum/         # Переліки (Enums)
│   ├── EventSubscriber/ # Підписники на події
│   ├── Exception/    # Кастомні виключення
│   ├── HTTP/         # HTTP-специфічні класи (роути, відповіді)
│   ├── Repository/   # Репозиторії
│   ├── Security/     # Безпека (автентифікація, токени)
│   └── Service/      # Бізнес-логіка
├── templates/        # Twig-шаблони (за потреби)
├── Dockerfile        # PHP-образ
├── docker-compose.yml
└── composer.json

```

---

