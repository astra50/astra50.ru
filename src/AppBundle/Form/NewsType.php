<?php

namespace AppBundle\Form;

use AppBundle\Model\NewsModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class NewsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Заголовок',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Содержание',
                'attr' => ['rows' => '10'],
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'Опубликовать?',
                'required' => false,
            ])
            ->add('internal', CheckboxType::class, [
                'label' => 'Виден только членам СНТ?',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => NewsModel::class,
            ]);
    }
}
