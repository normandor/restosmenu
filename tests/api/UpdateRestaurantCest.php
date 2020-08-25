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
        $I->haveInDatabase('category',[
            'restaurant_id' => 5,
            'category_type' => 'image',
            'name' => 'restaurant_logo',
            'image_url' => 'restaurant_logo',
        ]);

        $I->sendPOST('/admin/page/restaurant/removeLogo/5');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"detail":"Image removed"');

        $I->seeInDatabase('category',[
            'restaurant_id' => 5,
            'category_type' => 'image',
            'name' => 'restaurant_logo',
            'image_url' => null,
        ]);
    }

    public function canRemoveAnInexisingLogoFromARestaurant(ApiTester $I)
    {
        $I->haveInDatabase('category',[
            'restaurant_id' => 5,
            'category_type' => 'image',
            'name' => 'restaurant_logo',
            'image_url' => null,
        ]);

        $I->sendPOST('/admin/page/restaurant/removeLogo/5');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"detail":"Image already empty"');

        $I->seeInDatabase('category',[
            'restaurant_id' => 5,
            'category_type' => 'image',
            'name' => 'restaurant_logo',
            'image_url' => null,
        ]);
    }
}
