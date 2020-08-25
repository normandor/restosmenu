<?php

namespace App\Tests;

use App\Tests\ApiTester;

class CategoryControllerCest
{
    public function _before(ApiTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'admin');
        $I->click('login');

        $I->deleteFromTable('category');
    }

    public function canGetCountWhenNoneExisting(ApiTester $I)
    {
        $I->haveInDatabase('category',
            [
                'id'            => 1,
                'category_type' => 'basico',
                'enabled'       => 1,
                'restaurant_id' => 1,
                'currency_id'   => 1,
                'order_show'    => 1,
            ]);
        $I->haveInDatabase('category',
            [
                'id'            => 2,
                'category_type' => 'basico',
                'enabled'       => 1,
                'restaurant_id' => 1,
                'currency_id'   => 1,
                'order_show'    => 2,
            ]);

        $I->sendGET('/admin/category/getComboCount/');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"count":0}');
    }

    public function canGetCountWhenOneExisting(ApiTester $I)
    {
        $I->haveInDatabase('category',
            [
                'id'            => 1,
                'category_type' => 'combo',
                'enabled'       => 1,
                'restaurant_id' => 1,
                'currency_id'   => 1,
                'order_show'    => 1,
            ]);
        $I->haveInDatabase('category',
            [
                'id'            => 2,
                'category_type' => 'basico',
                'enabled'       => 1,
                'restaurant_id' => 1,
                'currency_id'   => 1,
                'order_show'    => 2,
            ]);

        $I->sendGET('/admin/category/getComboCount/');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"count":1}');
    }
}
