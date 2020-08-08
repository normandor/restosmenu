<?php

namespace App\Form\Type;

use App\Entity\Combo;
use App\Entity\ComboDish;
use App\Repository\ComboRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ComboSelectedDishType extends AbstractType
{
    private $comboRepository;
    private $user;

    public function __construct(ComboRepository $comboRepository, Security $security)
    {
        $this->comboRepository = $comboRepository;
        $this->user = $security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comboId',
                EntityType::class,
                [
                    'class' => Combo::class,
                    'query_builder' => function (ComboRepository $cr) {
                        return $cr->createQueryBuilder('u')
                            ->where('u.enabled = :enabled')
                            ->andWhere('u.restaurantId = :restaurantId')
                            ->setParameter('enabled',1)
                            ->setParameter('restaurantId', $this->user->getRestaurantId())
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
                    return $this->comboRepository->findOneBy(['id' => $comboIdAsInt]);
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
