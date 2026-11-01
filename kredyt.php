<?php
require_once dirname(__FILE__).'/config.php';
include _ROOT_PATH.'/app/security/check.php';

//zmienne:
//dane podane przez uzytkownika
$loanAmount = "";
$annualInterestRate = "";
$loanYears = "";
$paymentType = "equal";

//komunikat o bledzie
$errorMessage = "";

//wyniki obliczen
$monthlyPayment = 0;
$totalInterest = 0;
$totalRepayment = 0;

//wykonanie obliczen
$calculationCompleted = false;

//sprawdzanie poprawnosci
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //zmiana kropki na przecinek zeby akceptowac np 200,5
    $loanAmount = str_replace(",", ".", trim($_POST["loanAmount"] ?? ""));
    $annualInterestRate = str_replace(",", ".", trim($_POST["annualInterestRate"] ?? ""));
    $loanYears = trim($_POST["loanYears"] ?? "");
    $paymentType = $_POST["paymentType"] ?? "equal";

    //walidacja danych
    if (
        !is_numeric($loanAmount) ||
        !is_numeric($annualInterestRate) ||
        !ctype_digit($loanYears)
    ) {
        $errorMessage = "Wpisz poprawne wartosci we wszystkich polach.";

    } else {

        //zmiana na odpowiednie typy danych
        $loanAmount = (float) $loanAmount;
        $annualInterestRate = (float) $annualInterestRate;
        $loanYears = (int) $loanYears;

        //sprawdzenie czy mieszcza sie w zakresie
        if (
            $loanAmount <= 0 ||
            $loanAmount > 100000000 ||
            $annualInterestRate < 0 ||
            $annualInterestRate > 100 ||
            $loanYears < 1 ||
            $loanYears > 50
        ) {
            $errorMessage = "Kwota musi byc wieksza od 0, oprocentowanie musi wynosic od 0 do 100%, a okres od 1 do 50 lat.";

        } elseif (!in_array($paymentType, ["equal", "decreasing"], true)) {
            $errorMessage = "Wybierz poprawny rodzaj rat.";

        } else {

            //obliczanie liczby rat (miesieczne)
            $numberOfPayments = $loanYears * 12;

            //zmiana oprocentowania z rocznego na miesieczne
            $monthlyInterestRate = $annualInterestRate / 100 / 12;

            //RATY ROWNE
            if ($paymentType == "equal") {

                //jesli oprocentowanie 0% to dzielimy kapital przez liczbe rat zeby nie dzielic przez 0
                if ($monthlyInterestRate == 0) {
                    $monthlyPayment = $loanAmount / $numberOfPayments;

                } else {

                    //wzor na wysokosc rownej raty miesiecznej
                    $rate = $monthlyInterestRate;
                    $number = $numberOfPayments;

                    $monthlyPayment = $loanAmount * (
                        $rate * pow(1 + $rate, $number)
                    ) / (
                        pow(1 + $rate, $number) - 1
                    );
                }

                //suma rat
                $totalRepayment = $monthlyPayment * $numberOfPayments;

                //odsetki=roznica miedzy splata a kapitalem
                $totalInterest = $totalRepayment - $loanAmount;

                //RATY MALEJACE
            } else {

                //czesc kapitalowa raty jest taka sama
                $principalPayment = $loanAmount / $numberOfPayments;

                //pierwsza rata najwyzsza bo odsetki obliczane sa od calej pozyczonej kwoty
                $monthlyPayment = $principalPayment
                    + $loanAmount * $monthlyInterestRate;

                //poczatkowa kwota kapitalu do splaty
                $remainingBalance = $loanAmount;

                //odsetki dla kazdego miesiaca osobno
                for ($i = 0; $i < $numberOfPayments; $i++) {

                    //odsetki od kapitalu ktory
                    $interest = $remainingBalance * $monthlyInterestRate;

                    //odsetki+laczna suma
                    $totalInterest += $interest;

                    //zmniejszenie kapitalu o splacona czesc
                    $remainingBalance -= $principalPayment;
                }

                //calkowita splata=kapital+odsetki
                $totalRepayment = $loanAmount + $totalInterest;
            }

            //zaokraglenie do 2 miejsc p przzecinku
            $monthlyPayment = round($monthlyPayment, 2);
            $totalInterest = round($totalInterest, 2);
            $totalRepayment = round($totalRepayment, 2);

            $calculationCompleted = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator rat kredytu</title>
    <link rel="stylesheet" href="<?= _APP_ROOT ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="site-header">
            <div class="brand">
                <span class="brand-mark" aria-hidden="true">k</span>
                <span>kredyt.</span>
            </div>
            <div class="header-actions">
                <span class="header-note">Kalkulator rat kredytu</span>
                <a class="logout-link" href="<?= _APP_ROOT ?>/app/security/logout.php">Wyloguj</a>
            </div>
        </header>

        <main>
            <div class="intro">
                <p class="eyebrow">Policz. Porównaj. Zaplanuj.</p>
                <h1>Twój kredyt w liczbach.</h1>
                <p>Sprawdź wysokość miesięcznej raty i łączną kwotę do spłaty.
                    Wystarczy kilka danych.</p>
            </div>

            <div class="calculator">
                <section class="form-card" aria-labelledby="form-heading">
                    <div class="section-heading">
                        <h2 id="form-heading">Parametry kredytu</h2>
                        <span class="step-number" aria-hidden="true">01</span>
                    </div>
                    <p class="form-note">Wpisz dane, aby obliczyć swoją ratę.</p>

                    <form method="POST">
                        <div class="field">
                            <label for="loanAmount">Kwota kredytu</label>
                            <div class="input-wrap">
                                <input type="number" id="loanAmount" name="loanAmount"
                                    min="0.01" max="100000000" step="0.01"
                                    placeholder="Np. 20000" aria-label="Kwota kredytu (zł)"
                                    value="<?= htmlspecialchars((string)$loanAmount, ENT_QUOTES, 'UTF-8') ?>" required>
                                <span aria-hidden="true">zł</span>
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="field">
                                <label for="annualInterestRate">Oprocentowanie roczne</label>
                                <div class="input-wrap">
                                    <input type="number" id="annualInterestRate" name="annualInterestRate"
                                        min="0" max="100" step="0.01"
                                        placeholder="Np. 8" aria-label="Oprocentowanie roczne (%)"
                                        value="<?= htmlspecialchars((string)$annualInterestRate, ENT_QUOTES, 'UTF-8') ?>" required>
                                    <span aria-hidden="true">%</span>
                                </div>
                            </div>
                            <div class="field">
                                <label for="loanYears">Okres spłaty</label>
                                <div class="input-wrap">
                                    <input type="number" id="loanYears" name="loanYears"
                                        min="1" max="50" step="1"
                                        placeholder="Np. 5" aria-label="Okres spłaty (lata)"
                                        value="<?= htmlspecialchars((string)$loanYears, ENT_QUOTES, 'UTF-8') ?>" required>
                                    <span aria-hidden="true">lat</span>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label for="paymentType">Rodzaj rat</label>
                            <select id="paymentType" name="paymentType">
                                <option value="equal" <?= $paymentType == "equal" ? "selected" : "" ?>>Raty równe</option>
                                <option value="decreasing" <?= $paymentType == "decreasing" ? "selected" : "" ?>>Raty malejące</option>
                            </select>
                        </div>

                        <button type="submit">Oblicz ratę <span aria-hidden="true">→</span></button>

                        <?php if ($errorMessage != ""): ?>
                            <div class="error" role="alert">
                                <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </form>
                </section>

                <div>
                    <section class="results" aria-labelledby="results-heading">
                        <div class="section-heading">
                            <h2 id="results-heading">Podsumowanie</h2>
                            <span class="step-number" aria-hidden="true">02</span>
                        </div>

                        <div class="payment">
                            <p><?= $calculationCompleted && $paymentType == "decreasing" ? "Pierwsza miesięczna rata" : "Miesięczna rata" ?></p>
                            <p class="main-result">
                                <?= $calculationCompleted ? number_format($monthlyPayment, 2, ',', ' ') : "—" ?><small>zł</small>
                            </p>
                            <p class="payment-note">
                                <?php if (!$calculationCompleted): ?>
                                    Uzupełnij formularz, aby zobaczyć wynik.
                                <?php elseif ($paymentType == "decreasing"): ?>
                                    Kolejne raty będą coraz niższe.
                                <?php else: ?>
                                    Stała rata przy podanym oprocentowaniu.
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="result-row">
                            <span>Kwota kredytu</span>
                            <strong><?= $calculationCompleted ? number_format($loanAmount, 2, ',', ' ') . " zł" : "—" ?></strong>
                        </div>
                        <div class="result-row">
                            <span>Łączne odsetki</span>
                            <strong><?= $calculationCompleted ? number_format($totalInterest, 2, ',', ' ') . " zł" : "—" ?></strong>
                        </div>
                        <div class="result-row">
                            <span>Liczba rat</span>
                            <strong><?= $calculationCompleted ? $loanYears * 12 : "—" ?></strong>
                        </div>
                        <div class="result-row result-total">
                            <span>Całkowita spłata</span>
                            <strong><?= $calculationCompleted ? number_format($totalRepayment, 2, ',', ' ') . " zł" : "—" ?></strong>
                        </div>
                    </section>
                    <div class="info">
                        <span class="info-icon" aria-hidden="true">i</span>
                        <p>Obliczenia zakładają stałe oprocentowanie. Nie obejmują
                            prowizji, ubezpieczeń ani dodatkowych opłat.</p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="site-footer">
            <span>Kalkulator rat kredytu</span>
            <span>Raty równe i malejące · Kwoty w PLN</span>
        </footer>
    </div>
</body>
</html>
