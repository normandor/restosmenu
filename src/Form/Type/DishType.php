<?php

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Dish;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

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
            ->add('description', TextType::class, ['label' => 'description'])
            ->add('categoryId',
                EntityType::class,
                [
                    'class' => Category::class,
                    'query_builder' => function (CategoryRepository $cr) {
                        return $cr->createQueryBuilder('u')
                            ->where('u.restaurantId = :restaurantId')
                            ->setParameter('restaurantId', $this->user->getRestaurantId())
                            ->orderBy('u.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'label' => 'category',
                ])
            ->add('price', NumberType::class, ['label' => 'price']);

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
