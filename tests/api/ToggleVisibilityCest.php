<?php

namespace App\Tests;

use App\Tests\ApiTester;

class ToggleVisibilityCest
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

        $I->haveInDatabase('dish',
            [
                'id'            => 1,
                'name'          => 'p1',
                'enabled'       => 1,
                'restaurant_id' => 1,
                'currency_id'   => 1,
                'price'         => 3,
            ]);

        $I->haveInDatabase('dish',
            [
                'id'            => 2,
                'name'          => 'p2',
                'enabled'       => 1,
                'restaurant_id' => 1,
                'currency_id'   => 1,
                'price'         => 3,
            ]);
    }

    public function canToggleACategoryVisibility(ApiTester $I)
    {
        $I->seeInDatabase('category',
            [
                'id'      => 1,
                'enabled' => 1,
            ]);

        $I->sendPOST('/admin/category/toggleVisibility/2');
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
        $I->sendPOST('/admin/category/toggleVisibility/9');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
    }

    public function canToggleADishVisibility(ApiTester $I)
    {
        $I->seeInDatabase('dish',
            [
                'id'      => 1,
                'enabled' => 1,
            ]);

        $I->sendPOST('/admin/dish/toggleVisibility/1');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT);

        $I->seeInDatabase('dish',
            [
                'id'      => 1,
                'enabled' => 0,
            ]);
        $I->seeInDatabase('category',
            [
                'id'      => 2,
                'enabled' => 1,
            ]);
    }

    public function getErrorForInexistingDish(ApiTester $I)
    {
        $I->sendPOST('/admin/dish/toggleVisibility/9');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
    }

    public function canToggleAnImageVisibility(ApiTester $I)
    {
        $I->haveInDatabase('category',
            [
                'id'            => 4,
                'category_type' => 'image',
                'name'          => 'restaurant_logo',
                'enabled'       => 1,
                'restaurant_id' => 1,
                'currency_id'   => 1,
                'order_show'    => 3,
            ]);
        $I->haveInDatabase('settings_image',
            [
                'id'            => 1,
                'key'           => 'restaurant_logo',
                'property'      => 'visible',
                'value'         => 'true',
                'restaurant_id' => 1,
            ]);

        $I->sendPOST('/admin/category/toggleVisibility/4');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT);

        // must remain enabled
        $I->seeInDatabase('category',
            [
                'id'      => 4,
                'enabled' => 1,
            ]);
        $I->seeInDatabase('settings_image',
            [
                'id'       => 1,
                'property' => 'visible',
                'value'    => 'false',
            ]);
    }
}
