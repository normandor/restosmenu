<?php

namespace App\Tests;

use App\Tests\ApiTester;

class UpdateRestaurantCest
{
    public function _before(ApiTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'admin');
        $I->click('login');
    }

    public function canRemoveALogoFromARestaurant(ApiTester $I)
    {
        $I->haveInDatabase('restaurant',[
            'id' => 5,
            'logo_url' => 'image',
        ]);

        $I->sendPOST('/admin/page/restaurant/removeLogo/5');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"detail":"Image removed"');

        $I->seeInDatabase('restaurant',[
            'id' => 5,
            'logo_url' => null,
        ]);
    }

    public function canRemoveAnInexisingLogoFromARestaurant(ApiTester $I)
    {
        $I->haveInDatabase('restaurant',[
            'id' => 5,
            'logo_url' => null,
        ]);

        $I->sendPOST('/admin/page/restaurant/removeLogo/5');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"detail":"Image already empty"');

        $I->seeInDatabase('restaurant',[
            'id' => 5,
            'logo_url' => null,
        ]);
    }
}
