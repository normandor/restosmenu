<?php

namespace App\Form\Type;

use App\Controller\PageController;
use App\Entity\Category;
use App\Entity\Dish;
use App\Repository\CategoryRepository;
use Codeception\PHPUnit\Constraint\Page;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;

class DishType extends AbstractType
{
    private $categoryRepository;
    private $user;

    public function __construct(CategoryRepository $categoryRepository, Security $security)
    {
        $this->categoryRepository = $categoryRepository;
        $this->user = $security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'name'])
            ->add('description', TextType::class, ['label' => 'description', 'required' => false])
            ->add('categoryId',
                EntityType::class,
                [
                    'class' => Category::class,
                    'query_builder' => function (CategoryRepository $cr) {
                        return $cr->createQueryBuilder('u')
                            ->where('u.restaurantId = :restaurantId')
                            ->andWhere('u.categoryType = :categoryType')
                            ->setParameter('restaurantId', $this->user->getRestaurantId())
                            ->setParameter('categoryType', PageController::CATEGORY_BASICO)
                            ->orderBy('u.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'label' => 'category',
                ])
            ->add('price', NumberType::class, ['label' => 'price'])
            ->add('imageUrl', FileType::class, [
                'label' => 'Imagen',
                'data_class' => null,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/gif',
                            'GIF image',
                            'image/jpeg',
                            'JPEG image',
                            'image/tiff',
                            'TIFF image',
                            'image/bmp',
                            'Bitmap image',
                            'image/png',
                            'PNG image',
                        ],
                        'mimeTypesMessage' => 'Por favor agregar una imagen válida',
                    ])
                ],
            ]);

        $builder->get('categoryId')
            ->addModelTransformer(new CallbackTransformer(
                function ($categoryIdAsInt) {
                    return $this->categoryRepository->findOneBy(['id' => $categoryIdAsInt]);
                },
                function ($categoryIdAsObject) {
                    return $categoryIdAsObject->getId();
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dish::class,
        ]);
    }
}
