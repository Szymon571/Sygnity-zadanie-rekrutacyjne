# Currency Watch

Aplikacja korzysta z danych udostępnianych przez Narodowy Bank Polski (NBP) i pozwala śledzić kursy walut oraz ceny złota.

## Na czym się skupiłem

W projekcie zależało mi głównie na:

- pracy z zewnętrznym API (NBP),
- obsłudze brakujących danych (weekendy, dni bez publikacji kursów),
- budowie reaktywnego UI z użyciem Livewire,
- utrzymaniu czytelnej struktury widoków (Blade components),
- podstawowym cachowaniu, żeby ograniczyć liczbę zapytań do API.

## Funkcjonalności

### Autoryzacja i profil

- rejestracja i logowanie użytkownika,
- zmiana hasła,
- usunięcie konta,
- dane profilu: imię, nazwisko, nickname,
- generowanie avatara na podstawie emaila (ui-avatars).

### Dashboard

- lista obserwowanych walut z aktualnymi kursami NBP,
- wyszukiwarka walut,
- sprawdzanie kursu historycznego dla wybranej waluty i daty,
- fallback na ostatni dostępny kurs (np. weekendy),
- podsumowanie aktualnej ceny złota,
- wykres i tabela cen złota (przełączane widoki),
- powiadomienia toast przy dodawaniu/usuwaniu walut.

### Lokalizacja

- obsługa języka polskiego i angielskiego,
- możliwość zmiany języka w trakcie działania aplikacji,
- przetłumaczone komunikaty walidacji i autoryzacji.

## Stack technologiczny

- PHP 8.4+
- Laravel 13
- Livewire 4 + Flux UI
- Tailwind CSS 4
- Chart.js
- MySQL jako baza developerska

## Zewnętrzne API

- NBP API: https://api.nbp.pl/
- UI Avatars: https://ui-avatars.com/

## Wymagania

- PHP 8.4+
- Composer
- Node.js + npm
- MySQL

## Uruchomienie lokalne

### 1. Instalacja zależności

```bash
composer install
npm install
```

### 2. Konfiguracja środowiska

```bash
cp .env.example .env
php artisan key:generate
```

Następnie uzupełnij w `.env` dane dostępowe do lokalnej bazy MySQL.

### 3. Migracje

```bash
php artisan migrate
```

### 4. Frontend

Tryb developerski:

```bash
npm run dev
```

Build produkcyjny:

```bash
npm run build
```

### 5. Uruchomienie aplikacji

```bash
php artisan serve
```

## Testy

Projekt ma odseparowane środowisko testowe i używa SQLite in-memory dla testów.

```bash
php artisan test
```
