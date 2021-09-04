<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserCreateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
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
            ->add('agreeTerms', CheckboxType::class, [
                'constraints' => [new IsTrue(message: 'You must agree term to create new account')],
                'mapped' => false,
            ])
            ->add('subscribeToNewsletter', CheckboxType::class)
            ->add('register', SubmitType::class, [
                'attr' => ['class' => 'btn-primary']
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class,]);
    }
}
