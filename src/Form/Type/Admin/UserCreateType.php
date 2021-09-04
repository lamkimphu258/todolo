<?php

namespace App\Form\Type\Admin;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [new NotBlank(message: 'Email is required')],
                'attr' => ['placeholder' => 'example@email.com',],
            ])
            ->add('plainPassword', PasswordType::class, [
                'required' => true,
                'constraints' => [new NotBlank(message: 'Password is required')],
                'mapped' => false,
                'label' => 'Password',
            ])
            ->add('avatar', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Avatar',
                'constraints' => [
                    new Image()
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn-primary'],
                'label'=> 'Create',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class,]);
    }
}
