<?php

namespace App\Form;

use App\Entity\MyCollection;
use App\Entity\MyObject;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('image', FileType::class, [
                'label' => "Image de l'objet",
                'mapped' => false,
            ])
            ->add('description')
            ->add('is_active', CheckboxType::class, [
                'label' => 'Is Active',
                'required' => false,
                'data' => false, // Valeur par défaut
            ])
            ->add('user', EntityType::class, [
                'label' => 'User assignment',
                'class' => User::class,
                'choice_label' => 'nickname',
            ])
            ->add('myobjects', EntityType::class, [
                'label' => 'Objects assignments',
                'class' => MyObject::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MyCollection::class,
        ]);
    }
}
