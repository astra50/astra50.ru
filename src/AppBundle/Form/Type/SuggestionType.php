<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Enum\SuggestionType as SuggestionTypeEnum;
use AppBundle\Form\Model\Suggestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class SuggestionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\TextType::class, [
                'label' => 'Ф.И.О.',
            ])
            ->add('phone', Type\TextType::class, [
                'label' => 'Контактный телефон',
            ])
            ->add('email', Type\EmailType::class, [
                'label' => 'Электронная почта',
            ])
            ->add('type', Type\ChoiceType::class, [
                'label' => 'Тип обращения',
                'choices' => SuggestionTypeEnum::all(),
                'choice_label' => 'name',
            ])
            ->add('text', Type\TextareaType::class, [
                'label' => 'Текст обращения',
                'attr' => ['rows' => 16],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Suggestion::class,
        ]);
    }
}
