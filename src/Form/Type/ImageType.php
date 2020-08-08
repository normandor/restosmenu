<?php

namespace App\Form\Type;

use App\Entity\Image;
use App\Form\DataTransformer\FileToFilenameTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImageType extends AbstractType
{
    private $transformer;

    public function __construct(FileToFilenameTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
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

//        $builder->get('imageUrl')
//            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
