<?php
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
    <title>Kalkulator rat kredytu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /*podstawowy wyglad strony*/
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        /*glowny kontener*/
        .container {
            width: 100%;
            max-width: 450px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
        }

        /*naglowek*/
        h1 {
            text-align: center;
            font-size: 26px;
        }

        /*etykiety pol*/
        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        /*pola formularza*/
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 15px;
        }

        /*przycisk*/
        button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: #3569a8;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #254d7d;
        }

        /*komunikat o bledzie*/
        .error {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8dddd;
            color: #a00000;
        }

        /*wyniki*/
        .results {
            margin-top: 20px;
            padding: 15px;
            background-color: #edf4ed;
        }

        .main-result {
            font-size: 25px;
            font-weight: bold;
            color: #27632a;
        }

        /*wiersze wynikow*/
        .result-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 0;
            border-top: 1px solid #ccc;
        }

        .result-row strong {
            text-align: right;
        }
    </style>
</head>

<body>

    <main class="container">

        <h1>Kalkulator rat kredytu</h1>

        <!-- formularz z danymi kredytu-->
        <form method="POST">

            <label for="loanAmount">Kwota kredytu (zł)</label>
            <input
                type="number"
                id="loanAmount"
                name="loanAmount"
                min="0.01"
                max="100000000"
                step="0.01"
                placeholder="Np. 20000"
                value="<?= htmlspecialchars((string)$loanAmount, ENT_QUOTES, 'UTF-8') ?>"
                required
            >

            <label for="annualInterestRate">Oprocentowanie roczne (%)</label>
            <input
                type="number"
                id="annualInterestRate"
                name="annualInterestRate"
                min="0"
                max="100"
                step="0.01"
                placeholder="Np. 8"
                value="<?= htmlspecialchars((string)$annualInterestRate, ENT_QUOTES, 'UTF-8') ?>"
                required
            >

            <label for="loanYears">Okres spłaty (lata)</label>
            <input
                type="number"
                id="loanYears"
                name="loanYears"
                min="1"
                max="50"
                step="1"
                placeholder="Np. 5"
                value="<?= htmlspecialchars((string)$loanYears, ENT_QUOTES, 'UTF-8') ?>"
                required
            >

            <label for="paymentType">Rodzaj rat</label>
            <select id="paymentType" name="paymentType">
                <option value="equal"
                    <?= $paymentType == "equal" ? "selected" : "" ?>>
                    Raty rowne
                </option>

                <option value="decreasing"
                    <?= $paymentType == "decreasing" ? "selected" : "" ?>>
                    Raty malejace
                </option>
            </select>

            <button type="submit">Oblicz ratę</button>

        </form>

        <!--wyswietlenie bledu jesli dane sa niepoprawne-->
        <?php if ($errorMessage != ""): ?>
            <div class="error">
                <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!--wyniki po obliczeniach-->
        <?php if ($calculationCompleted): ?>

            <section class="results">

                <h2>Wyniki obliczeń</h2>

                <p>Pierwsza miesięczna rata:</p>

                <p class="main-result">
                    <?= number_format($monthlyPayment, 2, ',', ' ') ?> zl
                </p>

                <div class="result-row">
                    <span>Kwota kredytu:</span>
                    <strong>
                        <?= number_format($loanAmount, 2, ',', ' ') ?> zl
                    </strong>
                </div>

                <div class="result-row">
                    <span>Łączne odsetki:</span>
                    <strong>
                        <?= number_format($totalInterest, 2, ',', ' ') ?> zl
                    </strong>
                </div>

                <div class="result-row">
                    <span>Całkowita spłata:</span>
                    <strong>
                        <?= number_format($totalRepayment, 2, ',', ' ') ?> zł
                    </strong>
                </div>

                <div class="result-row">
                    <span>Liczba rat:</span>
                    <strong><?= $loanYears * 12 ?></strong>
                </div>

            </section>

        <?php endif; ?>
    </main>

</body>
</html>
