<?php

namespace App\Form\Type;

use App\Controller\PageController;
use App\Entity\Category;
use App\Entity\ComboDish;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ComboSelectedDishType extends AbstractType
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
            ->add('comboId',
                EntityType::class,
                [
                    'class' => Category::class,
                    'query_builder' => function (CategoryRepository $cr) {
                        return $cr->createQueryBuilder('u')
                            ->where('u.restaurantId = :restaurantId')
                            ->andWhere('u.categoryType = :categoryType')
                            ->setParameter('restaurantId', $this->user->getRestaurantId())
                            ->setParameter('categoryType', PageController::CATEGORY_COMBO)
                            ->orderBy('u.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'label' => 'Combo',
                ])
            ->add('dishId',
                HiddenType::class,
                [
                    'data' => $options['dishId'],
                ]);

        $builder->get('comboId')
            ->addModelTransformer(new CallbackTransformer(
                function ($comboIdAsInt) {
                    return $this->categoryRepository->findOneBy(['id' => $comboIdAsInt]);
                },
                function ($comboIdAsObject) {
                    return $comboIdAsObject->getId();
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ComboDish::class,
            'dishId' => 0,
        ]);
    }
}
