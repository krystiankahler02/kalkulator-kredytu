<?php

namespace app\controllers;

use core\App;
use core\RoleUtils;

class LogoutCtrl {

    public function action_logout() {
        RoleUtils::removeRole('admin');
        RoleUtils::removeRole('user');
        App::getRouter()->redirectTo('login');
    }
}
