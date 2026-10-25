# Kalkulator rat kredytu

Prosty kalkulator w PHP z formularzem w języku polskim. Oblicza pierwszą ratę miesięczną, łączne odsetki, całkowitą spłatę i liczbę rat dla rat równych lub malejących.

Oryginalny kod znajduje się w `kredyt.php`. Plik `public/index.php` udostępnia go jako stronę główną. Aplikacja nie wymaga bazy danych ani bibliotek Composer.

## Uruchomienie na komputerze przez Docker

W katalogu projektu:

```bash
docker compose up -d
```

Otwórz <http://127.0.0.1:8080/>. Serwer jest dostępny tylko na tym komputerze. Kontener wznawia pracę po ponownym uruchomieniu Dockera.

Zatrzymanie:

```bash
docker compose down
```

## Uruchomienie bez Dockera

Wymagany jest PHP CLI, np. PHP 8.4:

```bash
php -S 127.0.0.1:8080 -t public
```

Zatrzymaj serwer skrótem `Ctrl+C`.

## Przykład

Dla kwoty 20 000 zł, oprocentowania 8% rocznie i okresu 5 lat rata równa wynosi 405,53 zł. Dla tych samych parametrów pierwsza rata malejąca wynosi 466,67 zł.

## Zakres obliczeń

Obliczenia zakładają stałe oprocentowanie i miesięczne raty. Nie uwzględniają prowizji, ubezpieczeń ani innych opłat.

GitHub przechowuje kod źródłowy. Do wykonania PHP potrzebny jest serwer PHP; GitHub Pages nie uruchamia plików PHP.
