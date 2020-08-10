<?php

namespace App\Form\Type;

use App\Entity\Dish;
use App\Entity\ComboDish;
use App\Repository\DishRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ComboDishType extends AbstractType
{
    private $dishRepository;
    private $user;

    public function __construct(DishRepository $comboRepository, Security $security)
    {
        $this->dishRepository = $comboRepository;
        $this->user = $security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dishId',
                EntityType::class,
                [
                    'class' => Dish::class,
                    'query_builder' => function (DishRepository $cr) {
                        return $cr->createQueryBuilder('u')
                            ->where('u.restaurantId = :restaurantId')
                            ->setParameter('restaurantId', $this->user->getRestaurantId())
                            ->orderBy('u.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'label' => 'Plato',
                ])
            ->add('comboId',
                HiddenType::class,
                [
                    'data' => $options['comboId'],
                ]);

        $builder->get('dishId')
            ->addModelTransformer(new CallbackTransformer(
                function ($dishIdAsInt) {
                    return $this->dishRepository->findOneBy(['id' => $dishIdAsInt]);
                },
                function ($dishIdAsObject) {
                    return $dishIdAsObject->getId();
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ComboDish::class,
            'comboId' => 0,
        ]);
    }
}
