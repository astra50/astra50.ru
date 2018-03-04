<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Area;
use App\Entity\Enum\Calculation;
use App\Entity\Enum\Schedule;
use App\Entity\Purpose;
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
class PurposeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class, [
                'label' => 'Наименование',
                'translation_domain' => false,
                'disabled' => $options['name_disabled'],
            ])
            ->add($builder->create('amount', Type\MoneyType::class, [
                'label' => 'Сумма',
                'currency' => 'RUB',
                'divisor' => 100,
                'translation_domain' => false,
            ])
                ->addModelTransformer(new MoneyTransformer())
            )
            ->add('schedule', Type\ChoiceType::class, [
                'label' => 'Расписание',
                'choices' => Schedule::all(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'multiple' => false,
                'expanded' => true,
                'translation_domain' => false,
            ])
            ->add('calculation', Type\ChoiceType::class, [
                'label' => 'Начисление',
                'choices' => Calculation::all(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'multiple' => false,
                'expanded' => true,
                'translation_domain' => false,
            ])
            ->add('areas', EntityType::class, [
                'label' => 'Участки',
                'class' => Area::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('entity')
                        ->orderBy('entity.number + 0', 'ASC');
                },
                'choice_label' => 'number',
                'choice_value' => 'id',
                'choice_attr' => function (Area $area) {
                    return ['data-select-rule' => '0' === $area->getNumber() ? 'exclude' : 'include'];
                },
                'group_by' => function (Area $area) {
                    $street = $area->getStreet();

                    return $street ? $street->getName() : 'Без улицы';
                },
                'multiple' => true,
                'expanded' => true,
                'translation_domain' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Purpose::class,
                'name_disabled' => false,
            ]);
    }
}
