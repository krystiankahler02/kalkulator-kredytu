<?php

namespace app\controllers;

use core\App;

class KredytCtrl {

    public function action_kredyt() {
        $loanAmount = '';
        $annualInterestRate = '';
        $loanYears = '';
        $paymentType = 'equal';
        $errorMessage = '';
        $monthlyPayment = 0;
        $totalInterest = 0;
        $totalRepayment = 0;
        $calculationCompleted = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loanAmount = str_replace(',', '.', trim($_POST['loanAmount'] ?? ''));
            $annualInterestRate = str_replace(',', '.', trim($_POST['annualInterestRate'] ?? ''));
            $loanYears = trim($_POST['loanYears'] ?? '');
            $paymentType = $_POST['paymentType'] ?? 'equal';

            if (!is_numeric($loanAmount) || !is_numeric($annualInterestRate) || !ctype_digit($loanYears)) {
                $errorMessage = 'Wpisz poprawne wartosci we wszystkich polach.';
            } else {
                $loanAmount = (float) $loanAmount;
                $annualInterestRate = (float) $annualInterestRate;
                $loanYears = (int) $loanYears;

                if (
                    $loanAmount <= 0 ||
                    $loanAmount > 100000000 ||
                    $annualInterestRate < 0 ||
                    $annualInterestRate > 100 ||
                    $loanYears < 1 ||
                    $loanYears > 50
                ) {
                    $errorMessage = 'Kwota musi byc wieksza od 0, oprocentowanie musi wynosic od 0 do 100%, a okres od 1 do 50 lat.';
                } elseif (!in_array($paymentType, ['equal', 'decreasing'], true)) {
                    $errorMessage = 'Wybierz poprawny rodzaj rat.';
                } else {
                    $numberOfPayments = $loanYears * 12;
                    $monthlyInterestRate = $annualInterestRate / 100 / 12;

                    if ($paymentType === 'equal') {
                        if ($monthlyInterestRate == 0) {
                            $monthlyPayment = $loanAmount / $numberOfPayments;
                        } else {
                            $monthlyPayment = $loanAmount * (
                                $monthlyInterestRate * pow(1 + $monthlyInterestRate, $numberOfPayments)
                            ) / (
                                pow(1 + $monthlyInterestRate, $numberOfPayments) - 1
                            );
                        }

                        $totalRepayment = $monthlyPayment * $numberOfPayments;
                        $totalInterest = $totalRepayment - $loanAmount;
                    } else {
                        $principalPayment = $loanAmount / $numberOfPayments;
                        $monthlyPayment = $principalPayment + $loanAmount * $monthlyInterestRate;
                        $remainingBalance = $loanAmount;

                        for ($i = 0; $i < $numberOfPayments; $i++) {
                            $interest = $remainingBalance * $monthlyInterestRate;
                            $totalInterest += $interest;
                            $remainingBalance -= $principalPayment;
                        }

                        $totalRepayment = $loanAmount + $totalInterest;
                    }

                    $monthlyPayment = round($monthlyPayment, 2);
                    $totalInterest = round($totalInterest, 2);
                    $totalRepayment = round($totalRepayment, 2);
                    $calculationCompleted = true;
                }
            }
        }

        App::getSmarty()->assign('loanAmount', $loanAmount);
        App::getSmarty()->assign('annualInterestRate', $annualInterestRate);
        App::getSmarty()->assign('loanYears', $loanYears);
        App::getSmarty()->assign('paymentType', $paymentType);
        App::getSmarty()->assign('errorMessage', $errorMessage);
        App::getSmarty()->assign('monthlyPayment', $monthlyPayment);
        App::getSmarty()->assign('totalInterest', $totalInterest);
        App::getSmarty()->assign('totalRepayment', $totalRepayment);
        App::getSmarty()->assign('calculationCompleted', $calculationCompleted);
        App::getSmarty()->assign('monthlyPaymentFormatted', number_format($monthlyPayment, 2, ',', ' '));
        App::getSmarty()->assign('loanAmountFormatted', number_format((float) $loanAmount, 2, ',', ' '));
        App::getSmarty()->assign('totalInterestFormatted', number_format($totalInterest, 2, ',', ' '));
        App::getSmarty()->assign('totalRepaymentFormatted', number_format($totalRepayment, 2, ',', ' '));
        App::getSmarty()->display('Kredyt.tpl');
    }
}
