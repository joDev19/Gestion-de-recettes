<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class CategoryType extends AbstractType
{
    public function __construct(private FormListenerFactory $form_listener_factory) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => ""
            ])
            ->add('slug', TextType::class, [
                'empty_data' => "",
                'required' => false
            ])
            ->add('recipes', EntityType::class, [
                'class' => Recipe::class,
                'choice_label' => 'title',
                'multiple' => true,
                'by_reference' => false,
                'expanded' => true,
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Créer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->form_listener_factory->autoSlug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->form_listener_factory->timestamps())
        ;
    }




    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
