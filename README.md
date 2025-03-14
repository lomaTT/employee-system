# Employee system

## Opis
Aplikacja do zarządzania pracownikami w firmie. 
Umożliwia dodawanie oraz przeglądanie pracowników, dodawania oraz przeglądanie ich godzin pracy. 

## Technologie
- PHP 8.2
- Symfony 7.2
- MySQL 8.0
- Docker (docker-compose)

## Instalacja
1. Sklonuj repozytorium
2. Przejdź do katalogu z projektem
3. Skopiuj plik `.env.example` do `.env`
4. Uruchom komendę `composer install`
5. Uruchom komendę `docker compose up -d`
6. Uruchom migrację bazy danych `docker exec -it bash` i w katalogu employee-system uruchom `php bin/console doctrine:migrations:migrate`
7. Aplikacja dostępna jest pod adresem `http://localhost:8080`

## End-pointy
- GET `/employee/{id}` - zwraca informacje o pracowniku
- POST `/employee` - dodaje nowego pracownika (wymagane pola: `name`, `surname`)
- GET `/work-time/summary` - zwraca podsumowanie godzin pracy pracownika (wymagane pola: `employeeId`, `date` - format `Y-m-d` lub `Y-m`)
- POST `/work-time` - dodaje godziny pracy pracownika (wymagane pola: `employeeId`, `startDateTime` - format `d-m-Y H:i:s`, `endDateTime` - format `d-m-Y H:i:s`)

Równieź był zaimplementowany system logowania zdarzeń w aplikacji. Logi zapisywane są w pliku `var/log`.
Testy w systemie nie zostały zaimplementowane, ponieważ nie było takiego wymagania i iłość kodu nie była duża.