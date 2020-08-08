<?php

namespace App\Tests;

use App\Tests\ApiTester;

class LoginCest
{

    public function _before(ApiTester $I)
    {

    }

    // tests
    public function tryToLogin(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $credentials = [
            'username' => 'test',
            'password' => 'test',
        ];
        $I->sendPOST('/login', $credentials);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
    }

}
