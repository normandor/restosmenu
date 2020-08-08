<?php

namespace App\Tests\Controller;

use App\Tests\AcceptanceTester;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerCest
{
    public function tryToFailLoggingIn(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('username', 'john');
        $I->fillField('password', 'coltrane');
        $I->click('login');
        $I->see('Error en usuario');
        $I->seeInCurrentUrl('/login');
    }

    public function tryToLogIn(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('username', 'test');
        $I->fillField('password', 'test');
        $I->click('login');
        $I->see('Bienvenidos al tablero de mando');
        $I->seeInCurrentUrl('/');
    }

    public function tryToLogIn2(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->fillField('username', 'test');
        $I->fillField('password', 'test');
        $I->click('login');
        $I->see('Bienvenidos al tablero de mando');
        $I->seeInCurrentUrl('/');
    }
}
