<?php

namespace App\Form;

use App\Entity\Pokemon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PokemonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('attaque')
            ->add('defense')
            ->add('estCapture')
            ->add('image', FileType::class, ['mapped' => false, 'required' => false, 'constraints' => [new File(['maxSize' => '1024k',
                'mimeTypes' => ['image/jpeg', 'image/png','image/gif','image/svg+xml'], 'mimeTypesMessage' => 'Please upload a valid image'])]]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pokemon::class,
        ]);
    }
}
