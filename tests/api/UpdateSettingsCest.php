<?php

namespace App\Tests;

use App\Tests\ApiTester;

class UpdateSettingsCest
{
    public function _before(ApiTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'admin');
        $I->click('login');
    }

    public function canUpdateAFontColor(ApiTester $I)
    {
        $I->haveInDatabase('settings_page',[
            'key' => 'menu_body',
            'property' => 'color',
            'value' => '#664434',
        ]);

        $I->seeInDatabase('settings_page',[
            'key' => 'menu_body',
            'property' => 'color',
            'value' => '#664434',
        ]);

        $I->sendPOST('/admin/page/updateSetting/menu_body/color/ffffff');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeInDatabase('settings_page',[
            'key' => 'menu_body',
            'property' => 'color',
            'value' => '#ffffff',
        ]);
    }

    public function canUpdateABackgroundColor(ApiTester $I)
    {
        $I->haveInDatabase('settings_page',[
            'key' => 'menu_body',
            'property' => 'background-color',
            'value' => '#664434',
        ]);

        $I->seeInDatabase('settings_page',[
            'key' => 'menu_body',
            'property' => 'background-color',
            'value' => '#664434',
        ]);

        $I->sendPOST('/admin/page/updateSetting/menu_body/background-color/ffffff');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeInDatabase('settings_page',[
            'key' => 'menu_body',
            'property' => 'background-color',
            'value' => '#ffffff',
        ]);
    }

    public function failUpdatingAnUnexistingField(ApiTester $I)
    {
        $I->sendPOST('/admin/page/updateSetting/menu_body/non-existing/ffffff');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"message":"Property not found');
    }

    public function canUpdateAFontSize(ApiTester $I)
    {
        $I->haveInDatabase('settings_page',[
            'key' => 'menu_body',
            'property' => 'font-size',
            'value' => '20px',
        ]);

        $I->seeInDatabase('settings_page',[
            'key' => 'menu_body',
            'property' => 'font-size',
            'value' => '20px',
        ]);

        $I->sendPOST('/admin/page/updateSetting/menu_body/font-size/15px');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeInDatabase('settings_page',[
            'key' => 'menu_body',
            'property' => 'font-size',
            'value' => '15px',
        ]);
    }
}
