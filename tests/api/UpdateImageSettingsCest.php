<?php

namespace App\Tests;

use App\Tests\ApiTester;

class UpdateImageSettingsCest
{
    public function _before(ApiTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'admin');
        $I->click('login');
    }

    public function canUpdateAMobileImageWidth(ApiTester $I)
    {
        $I->haveInDatabase('settings_image',[
            'key' => 'restaurant_logo',
            'property' => 'width',
            'value' => '10%',
            'value_mobile' => '10%',
            'restaurant_id' => '1',
        ]);

        $I->sendPOST('/admin/page/updateImageSetting/restaurant_logo/width/30%25/mobile');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeInDatabase('settings_image',[
            'key' => 'restaurant_logo',
            'property' => 'width',
            'value' => '10%',
            'value_mobile' => '30%',
            'restaurant_id' => '1',
        ]);
    }

    public function canUpdateADesktopImageWidth(ApiTester $I)
    {
        $I->haveInDatabase('settings_image',[
            'key' => 'restaurant_logo',
            'property' => 'width',
            'value' => '10%',
            'value_mobile' => '10%',
            'restaurant_id' => '1',
        ]);

        $I->sendPOST('/admin/page/updateImageSetting/restaurant_logo/width/30%25/desktop');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeInDatabase('settings_image',[
            'key' => 'restaurant_logo',
            'property' => 'width',
            'value' => '30%',
            'value_mobile' => '10%',
            'restaurant_id' => '1',
        ]);
    }

    public function canHaveErrorIfUnknownProperty(ApiTester $I)
    {
        $I->haveInDatabase('settings_image',[
            'key' => 'restaurant_logo',
            'property' => 'width',
            'value' => '10%',
            'value_mobile' => '10%',
            'restaurant_id' => '1',
        ]);

        $I->sendPOST('/admin/page/updateImageSetting/restaurant_logo/unknown/30%25/desktop');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
    }
}
