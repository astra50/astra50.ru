<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Area;
use App\Entity\Purpose;
use App\Form\Model\PaymentModel;
use App\Form\Transformer\MoneyTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class PaymentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('purpose', EntityType::class, [
                'label' => 'Платёжная цель',
                'class' => Purpose::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('entity')
                        ->where('entity.archivedAt IS NULL')
                        ->orderBy('entity.id', 'DESC');
                },
                'choice_label' => 'name',
                'choice_value' => 'id',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Выберите платёжную цель',
                'translation_domain' => false,
            ])
            ->add('area', EntityType::class, [
                'label' => 'Участки',
                'class' => Area::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('entity')
                        ->orderBy('entity.number + 0', 'ASC');
                },
                'choice_label' => 'number',
                'choice_value' => 'id',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Выберите участок',
                'translation_domain' => false,
            ])
            ->add($builder->create('amount', Type\MoneyType::class, [
                'label' => 'Сумма',
                'currency' => 'RUB',
                'divisor' => 100,
                'translation_domain' => false,
            ])->addModelTransformer(new MoneyTransformer()))
            ->add('isPositive', Type\ChoiceType::class, [
                'label' => 'Тип',
                'choices' => [
                    'Начисление' => true,
                    'Списание' => false,
                ],
                'multiple' => false,
                'expanded' => true,
                'required' => true,
                'translation_domain' => false,
            ])
            ->add('comment', Type\TextType::class, [
                'label' => 'Комментарий',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => PaymentModel::class,
            ]);
    }
}
