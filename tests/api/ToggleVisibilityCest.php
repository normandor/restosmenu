<?php

namespace App\Tests;

use App\Tests\ApiTester;

class ToggleCategoryVisibilityCest
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

    public function canToggleAVisibility(ApiTester $I)
    {
        $I->seeInDatabase('category',
            [
                'id'      => 1,
                'enabled' => 1,
            ]);

        $I->sendPOST('/category/toggleVisibility/2');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT);

        $I->seeInDatabase('category',
            [
                'id'      => 2,
                'enabled' => 0,
            ]);
        $I->seeInDatabase('category',
            [
                'id'      => 1,
                'enabled' => 1,
            ]);
        $I->seeInDatabase('category',
            [
                'id'      => 3,
                'enabled' => 1,
            ]);
    }

    public function getErrorForInexistingCategory(ApiTester $I)
    {
        $I->sendPOST('/category/toggleVisibility/9');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
    }
}
