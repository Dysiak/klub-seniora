# System Zarządzania Klubem Seniora

System webowy do zarządzania zajęciami w klubie seniora, obsługujący 4 role użytkowników: Senior, Instruktor, Koordynator i Administrator.

## Wymagania

- **XAMPP** (wersja 3.3.0 lub nowsza)
  - PHP 8.0+
  - MySQL/MariaDB
  - Apache Server
- Przeglądarka internetowa (Chrome, Firefox, Edge)

## Instalacja i Uruchomienie

### 1. Pobierz projekt
```bash
# Skopiuj folder Klub_seniora_system do katalogu htdocs w XAMPP
# Domyślna lokalizacja: C:\xampp\htdocs\
```

### 2. Uruchom XAMPP
- Otwórz **XAMPP Control Panel**
- Uruchom moduły:
  - **Apache** (kliknij "Start")
  - **MySQL** (kliknij "Start")

### 3. Utwórz bazę danych
1. Otwórz przeglądarkę i przejdź do: `http://localhost/phpmyadmin`
2. Kliknij zakładkę **SQL**
3. Skopiuj i wykonaj w terminalu zawartość pliku: `database/klub_seniora.sql`


### 4. Uruchom aplikację
Otwórz przeglądarkę i wejdź na:
```
http://localhost/Klub_seniora_system/public/
```

##  Konta Testowe

### Senior
- **Login:** `senior1` / **Hasło:** `haslo123`
- **Funkcje:** Przeglądanie zajęć, zapisywanie się, anulowanie rezerwacji

### Instruktor
- **Login:** `instruktor1` / **Hasło:** `haslo123`
- **Funkcje:** Przeglądanie listy uczestników swoich zajęć

### Koordynator
- **Login:** `koordynator1` / **Hasło:** `haslo123`
- **Funkcje:** Tworzenie, edycja i usuwanie zajęć

### Administrator
- **Login:** `admin` / **Hasło:** `haslo123`
- **Funkcje:** Zarządzanie użytkownikami (CRUD, aktywacja/deaktywacja)

## Struktura Projektu

```
Klub_seniora_system/
├── database/
│   ├── klub_seniora.sql        # Baza danych
├── src/
│   └── models/                 # Klasy modelu (OOP)
│       ├── Database.php        # Singleton bazy danych
│       ├── User.php            # Klasa abstrakcyjna użytkownika
│       ├── Senior.php          # Model seniora
│       ├── Instruktor.php      # Model instruktora
│       ├── Koordynator.php     # Model koordynatora
│       ├── Administrator.php   # Model administratora
│       ├── Zajecia.php         # Model zajęć
│       ├── Rezerwacja.php      # Model rezerwacji
│       └── Sala.php            # Model sali
└── public/                     # Publiczny katalog aplikacji
    ├── index.php               # Strona główna
    ├── login.php               # Logowanie
    ├── logout.php              # Wylogowanie
    ├── dashboard.php           # Router dashboardów
    ├── css/
    │   └── style.css           # Stylizacja (Bootstrap 5)
    ├── js/
    │   ├── app.js              # Logika aplikacji
    │   └── main.js             # Funkcje pomocnicze
    ├── includes/
    │   ├── navbar.php          # Pasek nawigacyjny
    │   └── footer.php          # Stopka
    ├── dashboards/             # Panele według ról
    │   ├── senior_dashboard.php
    │   ├── instruktor_dashboard.php
    │   ├── koordynator_dashboard.php
    │   └── admin_dashboard.php
    └── api/                    # Endpointy API
        ├── senior/
        │   ├── zapisz.php      # Zapis na zajęcia
        │   └── anuluj.php      # Anulowanie rezerwacji
        ├── instruktor/
        │   └── lista_uczestnikow.php
        ├── koordynator/
        │   ├── utworz_zajecia.php
        │   ├── edytuj_zajecia.php
        │   └── usun_zajecia.php
        └── admin/
            ├── utworz_uzytkownika.php
            ├── edytuj_uzytkownika.php
            ├── toggle_status.php
            └── usun_uzytkownika.php
```

## Bezpieczeństwo

- Hasła hashowane algorytmem `password_hash()` (bcrypt)
- Sesje PHP do zarządzania autoryzacją
- Walidacja danych po stronie serwera
- Zabezpieczenie przed SQL Injection (prepared statements)
- Kontrola dostępu oparta na rolach (RBAC)

## Rozwiązywanie Problemów

### Apache nie startuje
- Sprawdź czy port 80 nie jest zajęty (Skype, IIS)
- W XAMPP Control Panel kliknij "Config" → "Service and Port Settings"
- Zmień port na 8080 i używaj: `http://localhost:8080/`

### MySQL nie startuje
- Sprawdź czy port 3306 nie jest zajęty
- Zakończ inne procesy MySQL w Menedżerze Zadań

### Błąd połączenia z bazą
1. Sprawdź czy MySQL jest uruchomiony w XAMPP
2. Zweryfikuj dane dostępowe w `src/models/Database.php`
3. Upewnij się, że baza `klub_seniora` istnieje w phpMyAdmin

### Nie można się zalogować
- Sprawdź czy wykonałeś `update_passwords.sql`
- Użyj kont testowych podanych powyżej
- Sprawdź czy sesje PHP działają (plik `php.ini`)

## Technologie

- **Backend:** PHP 8.0
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Baza danych:** MySQL 8.0
- **Framework CSS:** Bootstrap 5.1.3
- **Architektura:** MVC (Model-View-Controller)
- **Paradygmat:** OOP (Object-Oriented Programming)


**Autor:** Klaudia Łacińska, nr. indeksu: 52731, informatyka, gr. PGK.
