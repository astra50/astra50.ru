<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class LoadFixtures implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // fix   Notice: Use of undefined constant GLOB_BRACE - assumed 'GLOB_BRACE'
        if (!defined('GLOB_BRACE')) {
            define('GLOB_BRACE', 0);
        }

        Fixtures::load(__DIR__.'/fixtures.yml', $manager, ['providers' => [$this]]);
    }

    /**
     * @var array
     */
    private static $purposes = [
        'Ежемесячный платёж',
        'Ремонт дороги',
        'Постройка футбольного поля',
        'Помощь нищим',
        'Реконструкция хокейного клуба',
        'Ремонт эстокады',
        'Постройка гаража на 200 машиномест',
        'Прокладка оптоволоконной линии',
        'Постройка большого адронного коллайдера',
        'Реконструкция трактороного завода',
        'Закупка ракетного топлива для космодрома',
        'Преобретение тепловоза',
        'Аренда трансатлантического лайнера',
        'Ремонт вертолётной площадки',
        'Постройка цеха по ремонту вертолётов',
        'Постройка бомбоубежища',
        'Замаливание грехов в церкви',
        'Лечение председателя',
        'Организация чемпионата мира по кёрлингу',
        'Постройка второго энергоблока на АЭС',
        'Лечение грыжи Петровича',
        'Установка статуи председателю',
        'Обучение спецотряда быстрого реагирования из членов СНТ',
        'Закупка запчастей для ракетоносителя Энергия',
        'Оплата хостинга',
        'Оплата разработки сайта',
        'Реставрация монумента Арнольду Шварценегеру',
        'Установка плазменной панели на воротах',
        'Открытие Председатель Центр',
        'Отправка избранного за водным чипом',
        'Откуп рейдерам',
    ];

    /**
     * @return string
     */
    public function purpose(): string
    {
        $value = current(self::$purposes);
        next(self::$purposes);

        return $value;
    }

    /**
     * @var array
     */
    private static $streets = [
        'Новомарьинская',
        'Братиславская',
        'Люблинская',
        'Остаповская',
        'Волгоградская',
        'Комсомольская',
    ];

    /**
     * @return string
     */
    public function street(): string
    {
        $value = current(self::$streets);
        next(self::$streets);

        return $value;
    }
}
