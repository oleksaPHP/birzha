# Company API

Тестове завдання на Laravel 12.

Що є в рішенні:
- `POST /api/company` — створення / оновлення компанії;
- `GET /api/company/{edrpou}/versions` — список версій;
- валідація через `FormRequest`;
- міграції для `companies` і `company_versions`;
- feature-тести;
- Docker для локального запуску.

## Як реалізовано

Основну логіку виніс у `CompanyService`, щоб не тримати її в контролері.
Для цього тестового не став ускладнювати рішення окремими абстракціями: тут одна сутність, тому простіше і читабельніше мати окремий сервіс саме під компанії.

При створенні компанії записується перша версія.
При повторному запиті з тим самим `edrpou`:
- якщо дані не змінилися — повертається `duplicate`;
- якщо змінилися — оновлюється запис у `companies` і створюється новий запис у `company_versions`.

У `company_versions` зберігаються:
- `company_id`
- `version`
- `name`
- `edrpou`
- `address`
- `old_data`
- `new_data`
- timestamps

## Запуск

```bash
docker compose up -d --build
```

API буде доступне на `http://localhost:8000`.

## Приклад запиту

```bash
curl -X POST http://localhost:8000/api/company \
  -H "Content-Type: application/json" \
  -d '{
    "name": "ТОВ Українська енергетична біржа",
    "edrpou": "37027819",
    "address": "01001, Україна, м. Київ, вул. Хрещатик, 44"
  }'
```

## Тести

```bash
docker compose exec app php artisan test
```
