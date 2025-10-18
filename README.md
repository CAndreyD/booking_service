# Service Booking

## Описание

Service Booking — это система бронирования услуг, разработанная на Laravel + Inertia.js.
Проект позволяет:

- Просматривать список доступных услуг
- Открывать расписание (Пн–Сб, 10:00–20:00)
- Бронировать свободные временные слоты
- Проверять занятость времени в реальном времени

Используются современные подходы Laravel:

- Сервисы (BookingService, SlotService)
- Inertia.js + Vue (или React)
- Carbon для работы с временем
- Транзакции и блокировки (lockForUpdate) для защиты от коллизий при бронировании

## Требования

- PHP >= 8.2
- Composer >= 2.6
- Node.js >= 18
- npm или yarn
- MySQL

## Установка и запуск

### 1. Клонирование репозитория и переход в папку проекта

```bash
   git clone https://github.com/username/service-booking.git
   cd service-booking
```

### 2. Установка зависимостей PHP и Node

```bash
   composer install
   npm install
```

### 3. Создание файла окружения

```bash
   cp .env.example .env
```

### 4. Генерация ключа приложения

```bash
   php artisan key:generate
```

### 5. Выполнение миграций

```bash
   php artisan migrate
```

### 6. Наполнение базы тестовыми данными

```bash
   php artisan db:seed
```

### 7. Сборка фронтенда

```bash
   npm run build
```

### 8. Запуск сервера разработки

```bash
   php artisan serve
```
