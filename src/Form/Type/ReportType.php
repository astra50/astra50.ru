<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Enum\Financing;
use App\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class ReportType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('year', ChoiceType::class, [
                'placeholder' => 'Выберите значение',
                'choices' => array_reverse(Report::allowedYears()),
                'choice_label' => function ($value) {
                    return $value;
                },
                'label' => 'Год',
                'required' => true,
            ])
            ->add('month', ChoiceType::class, [
                'placeholder' => 'Выберите значение',
                'choices' => range(1, 12),
                'choice_label' => function ($value) {
                    return $value;
                },
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('financing', ChoiceType::class, [
                'choices' => Financing::all(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'label' => 'Финансирование',
                'required' => true,
                'expanded' => true,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => \App\Entity\Enum\ReportType::all(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'label' => 'Тип',
                'required' => true,
                'expanded' => true,
            ])
            ->add('url', UrlType::class, [
                'label' => 'Ссылка для скачивания отчёта',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Report::class,
            ]);
    }
}
