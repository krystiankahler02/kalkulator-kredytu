<?php

namespace app\controllers;

use core\App;
use core\ParamUtils;
use core\RoleUtils;

class LoginCtrl {

    public function action_login() {
        if (RoleUtils::inRole('admin') || RoleUtils::inRole('user')) {
            App::getRouter()->redirectTo('kredyt');
        }

        $login = '';
        $messages = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim(ParamUtils::getFromPost('login') ?? '');
            $pass = ParamUtils::getFromPost('pass') ?? '';

            if ($login === '') {
                $messages[] = 'Nie podano loginu';
            }

            if ($pass === '') {
                $messages[] = 'Nie podano hasła';
            }

            if (empty($messages)) {
                if (($login === 'admin' && $pass === 'admin') || ($login === 'user' && $pass === 'user')) {
                    RoleUtils::addRole($login);
                    App::getRouter()->redirectTo('kredyt');
                }

                $messages[] = 'Niepoprawny login lub hasło';
            }
        }

        App::getSmarty()->assign('login', $login);
        App::getSmarty()->assign('messages', $messages);
        App::getSmarty()->display('Login.tpl');
    }
}
