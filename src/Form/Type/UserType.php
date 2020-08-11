<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Nombre',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Apellido',
            ])
            ->add('email', TextType::class, [
                'label' => 'E-mail',
            ])
            ->add('username', TextType::class, [
                'label' => 'Usuario',
            ])
            ->add('avatar_path', FileType::class, [
                'label' => 'Imagen de perfil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
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
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => 'Las contrasenas deben ser iguales',
                'first_options'  => array('label' => 'Contraseña'),
                'second_options' => array('label' => 'Repetir contraseña'),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
