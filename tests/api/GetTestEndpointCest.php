<?php

namespace App\Tests;

use App\Tests\ApiTester;

class GetTestEndpointCest
{

    public function _before(ApiTester $I)
    {

    }

    public function tryToGetInfoWhileLoggedOut(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/test');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"message":"Authentication Required"}');
    }

    public function tryToGetInfoWithWrongToken(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-AUTH-TOKEN', '12');
        $I->sendGET('/test');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"message":"Username could not be found."}');
    }

    public function tryToGetInfoLoggedIn(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $credentials = [
            'username' => 'test',
            'password' => 'test',
        ];
        $I->sendPOST('/login', $credentials);
        $auth_token = $I->grabDataFromResponseByJsonPath('auth_token')[0];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-AUTH-TOKEN', $auth_token);
        $I->sendGET('/test');

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['msg' => 'string']);
    }
}
