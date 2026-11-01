# Kalkulator rat kredytu

Kalkulator w PHP z prostym logowaniem przez sesję, bez bazy danych.

## Uruchomienie w XAMPP

Projekt powinien znajdować się w katalogu `/opt/lampp/htdocs/kalkulator-kredytu`.

Po uruchomieniu Apache otwórz <http://localhost/kalkulator-kredytu/>.

Konta przykładowe z materiałów:

- login `admin`, hasło `admin`
- login `user`, hasło `user`

Oba konta mają dostęp do kalkulatora. Po wylogowaniu ponownie pojawia się formularz logowania.

## Pliki

- `kredyt.php` — obliczenia i widok kalkulatora; na początku sprawdzenie sesji
- `app/security/check.php` — sprawdzenie dostępu
- `app/security/login.php` — obsługa logowania
- `app/security/login_view.php` — formularz logowania
- `app/security/logout.php` — wylogowanie
- `config.php` — adres aplikacji i ścieżki
- `public/css/style.css` — wygląd kalkulatora

Obliczenia zakładają stałe oprocentowanie i miesięczne raty. Nie uwzględniają prowizji ani innych opłat.
