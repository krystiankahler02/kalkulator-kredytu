<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator rat kredytu</title>
    <link rel="stylesheet" href="{$conf->app_root}/css/style.css">
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
                <a class="logout-link" href="{url action='logout'}">Wyloguj</a>
            </div>
        </header>

        <main>
            <div class="intro">
                <p class="eyebrow">Policz. Porównaj. Zaplanuj.</p>
                <h1>Twój kredyt w liczbach.</h1>
                <p>Sprawdź wysokość miesięcznej raty i łączną kwotę do spłaty. Wystarczy kilka danych.</p>
            </div>

            <div class="calculator">
                <section class="form-card" aria-labelledby="form-heading">
                    <div class="section-heading">
                        <h2 id="form-heading">Parametry kredytu</h2>
                        <span class="step-number" aria-hidden="true">01</span>
                    </div>
                    <p class="form-note">Wpisz dane, aby obliczyć swoją ratę.</p>

                    <form method="post" action="{url action='kredyt'}">
                        <div class="field">
                            <label for="loanAmount">Kwota kredytu</label>
                            <div class="input-wrap">
                                <input type="number" id="loanAmount" name="loanAmount" min="0.01" max="100000000" step="0.01" placeholder="Np. 20000" aria-label="Kwota kredytu (zł)" value="{$loanAmount|escape:'html'}" required>
                                <span aria-hidden="true">zł</span>
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="field">
                                <label for="annualInterestRate">Oprocentowanie roczne</label>
                                <div class="input-wrap">
                                    <input type="number" id="annualInterestRate" name="annualInterestRate" min="0" max="100" step="0.01" placeholder="Np. 8" aria-label="Oprocentowanie roczne (%)" value="{$annualInterestRate|escape:'html'}" required>
                                    <span aria-hidden="true">%</span>
                                </div>
                            </div>
                            <div class="field">
                                <label for="loanYears">Okres spłaty</label>
                                <div class="input-wrap">
                                    <input type="number" id="loanYears" name="loanYears" min="1" max="50" step="1" placeholder="Np. 5" aria-label="Okres spłaty (lata)" value="{$loanYears|escape:'html'}" required>
                                    <span aria-hidden="true">lat</span>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label for="paymentType">Rodzaj rat</label>
                            <select id="paymentType" name="paymentType">
                                <option value="equal" {if $paymentType == 'equal'}selected{/if}>Raty równe</option>
                                <option value="decreasing" {if $paymentType == 'decreasing'}selected{/if}>Raty malejące</option>
                            </select>
                        </div>

                        <button type="submit">Oblicz ratę <span aria-hidden="true">→</span></button>

                        {if $errorMessage != ''}
                            <div class="error" role="alert">{$errorMessage|escape:'html'}</div>
                        {/if}
                    </form>
                </section>

                <div>
                    <section class="results" aria-labelledby="results-heading">
                        <div class="section-heading">
                            <h2 id="results-heading">Podsumowanie</h2>
                            <span class="step-number" aria-hidden="true">02</span>
                        </div>

                        <div class="payment">
                            <p>{if $calculationCompleted && $paymentType == 'decreasing'}Pierwsza miesięczna rata{else}Miesięczna rata{/if}</p>
                            <p class="main-result">{if $calculationCompleted}{$monthlyPaymentFormatted}{else}—{/if}<small>zł</small></p>
                            <p class="payment-note">
                                {if !$calculationCompleted}
                                    Uzupełnij formularz, aby zobaczyć wynik.
                                {elseif $paymentType == 'decreasing'}
                                    Kolejne raty będą coraz niższe.
                                {else}
                                    Stała rata przy podanym oprocentowaniu.
                                {/if}
                            </p>
                        </div>

                        <div class="result-row">
                            <span>Kwota kredytu</span>
                            <strong>{if $calculationCompleted}{$loanAmountFormatted} zł{else}—{/if}</strong>
                        </div>
                        <div class="result-row">
                            <span>Łączne odsetki</span>
                            <strong>{if $calculationCompleted}{$totalInterestFormatted} zł{else}—{/if}</strong>
                        </div>
                        <div class="result-row">
                            <span>Liczba rat</span>
                            <strong>{if $calculationCompleted}{$loanYears * 12}{else}—{/if}</strong>
                        </div>
                        <div class="result-row result-total">
                            <span>Całkowita spłata</span>
                            <strong>{if $calculationCompleted}{$totalRepaymentFormatted} zł{else}—{/if}</strong>
                        </div>
                    </section>
                    <div class="info">
                        <span class="info-icon" aria-hidden="true">i</span>
                        <p>Obliczenia zakładają stałe oprocentowanie. Nie obejmują prowizji, ubezpieczeń ani dodatkowych opłat.</p>
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
