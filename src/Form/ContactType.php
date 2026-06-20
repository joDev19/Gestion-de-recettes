<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Entrez votre nom",
                'constraints' => new Sequentially([
                    new NotBlank(),
                    new Length(min: 5)
                ]),
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->add("message", TextareaType::class, [
                'constraints' => new Sequentially([
                    new Length(min: 5),
                    new NotBlank()
                ])
            ])
            ->add('service', ChoiceType::class, [
                'choices'  => [
                    'Informatique' => "informatique",
                    'Secretariat' => "secretariat",
                    'Recrutement' => "recrutement",
                ]
            ],)
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer votre message",
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
