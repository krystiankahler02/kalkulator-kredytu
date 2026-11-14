<?php

use core\App;
use core\Utils;

App::getRouter()->setDefaultRoute('kredyt');
App::getRouter()->setLoginRoute('login');

Utils::addRoute('login', 'LoginCtrl');
Utils::addRoute('logout', 'LogoutCtrl', ['admin', 'user']);
Utils::addRoute('kredyt', 'KredytCtrl', ['admin', 'user']);
