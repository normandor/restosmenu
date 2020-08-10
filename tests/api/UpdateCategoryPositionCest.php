<?php

namespace App\Tests;

use App\Tests\ApiTester;

class UpdateCategoryPositionCest
{
    public function _before(ApiTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'admin');
        $I->click('login');

        $I->deleteFromTable('category');
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
        $I->haveInDatabase('category',
            [
                'id'            => 3,
                'category_type' => 'basico',
                'enabled'       => 1,
                'restaurant_id' => 1,
                'currency_id'   => 1,
                'order_show'    => 3,
            ]);
    }

    public function canUpdateToAPositionBefore(ApiTester $I)
    {
        $I->seeInDatabase('category',
            [
                'id'         => 1,
                'order_show' => 1,
            ]);

        $I->sendPOST('/page/updateOrder/2/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeInDatabase('category',
            [
                'id'         => 2,
                'order_show' => 1,
            ]);
        $I->seeInDatabase('category',
            [
                'id'         => 1,
                'order_show' => 2,
            ]);
        $I->seeInDatabase('category',
            [
                'id'         => 3,
                'order_show' => 3,
            ]);
    }

    public function canUpdateToAPositionAfter(ApiTester $I)
    {
        $I->seeInDatabase('category',
            [
                'id'         => 1,
                'order_show' => 1,
            ]);

        $I->sendPOST('/page/updateOrder/1/3');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeInDatabase('category',
            [
                'id'         => 2,
                'order_show' => 1,
            ]);
        $I->seeInDatabase('category',
            [
                'id'         => 3,
                'order_show' => 2,
            ]);
        $I->seeInDatabase('category',
            [
                'id'         => 1,
                'order_show' => 3,
            ]);
    }
}
