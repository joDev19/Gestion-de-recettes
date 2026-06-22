<?php

namespace App\Form;

use App\Entity\Recipe;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Sequentially;

class RecipeType extends AbstractType
{
    public function __construct(private FormListenerFactory $form_listener_factory)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => ''
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                // 'constraints' => new Sequentially([
                //     new Length(min: 10),
                //     new Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: "Ceci n'est pas un slug valid")
                // ])
            ])
            ->add('content', TextareaType::class, [
                'empty_data' => ''
            ])
            ->add('thumbnailFile', FileType::class)
            ->add('duration')
            ->add('Save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->form_listener_factory->autoSlug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->form_listener_factory->timestamps())
        ;
    }
    public function autoSlug(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        if (empty($data['slug'])) {
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger->slug($data['title']));
            $event->setData($data);
        }
    }
    public function autoSetDateForCreationOrUpdate(PostSubmitEvent $event): void
    {

        $data = $event->getData();
        if (!($data instanceof Recipe)) {
            return;
        }
        $data->setUpdatedAt(new DateTimeImmutable());
        if (!($data->getId())) {
            $data->setCreatedAt(new DateTimeImmutable());
        }
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            // 'validation_groups' => ['Default']
        ]);
    }
}
