# Kalkulator rat kredytu

Kalkulator rat kredytu w PHP oparty na frameworku Amelia. Obsługuje raty równe i malejące oraz logowanie przez sesję.

## Uruchomienie w XAMPP

1. Umieść projekt w katalogu `C:\xampp\htdocs\kalkulator-kredytu`.
2. Uruchom Apache w panelu XAMPP.
3. Otwórz `http://localhost/kalkulator-kredytu/public/`.

## Konta testowe

- login `admin`, hasło `admin`
- login `user`, hasło `user`

## Struktura

- `app/controllers` — obsługa logowania, wylogowania i kalkulatora
- `app/views` — widoki Smarty
- `public` — publiczny punkt wejścia aplikacji oraz style
- `routing.php` — definicje tras

Obliczenia zakładają stałe oprocentowanie i miesięczne raty. Nie uwzględniają prowizji, ubezpieczeń ani dodatkowych opłat.
