<?php

namespace App\Tests\Form\Type;

namespace App\Tests\Form\Type;

use App\Form\Type\CategoryType;
use App\Entity\Category;
use Symfony\Component\Form\Test\TypeTestCase;

class CategoryTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'name' => 'test',
        ];

        $model = new Category();

        $form = $this->factory->create(CategoryType::class, $model);

        $expected = new Category();
        $expected->setName('test');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $model);
    }

    public function testCustomFormView()
    {
        $formData = new Category();
        $formData->setName('testName');

        $view = $this->factory->create(CategoryType::class, $formData)
            ->createView();

        $this->assertArrayHasKey('name', $view->vars);
        $this->assertSame('category', $view->vars['name']);
    }
}
