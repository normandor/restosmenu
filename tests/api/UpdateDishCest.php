<?php

namespace App\Tests;

use App\Tests\ApiTester;

class UpdateDishCest
{
    public function _before(ApiTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'admin');
        $I->click('login');
    }

    public function canRemoveAnImageFromADish(ApiTester $I)
    {
        $I->haveInDatabase('dish',[
            'id' => 1,
            'imageUrl' => 'image',
        ]);

        $I->sendPOST('/admin/page/dish/removeImage/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"detail":"Image removed"');

        $I->seeInDatabase('dish',[
            'id' => 1,
            'imageUrl' => null,
        ]);
    }

    public function canRemoveAnInexisingImageFromADish(ApiTester $I)
    {
        $I->haveInDatabase('dish',[
            'id' => 1,
            'imageUrl' => null,
        ]);

        $I->sendPOST('/admin/page/dish/removeImage/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"detail":"Image already empty"');

        $I->seeInDatabase('dish',[
            'id' => 1,
            'imageUrl' => null,
        ]);
    }
}
