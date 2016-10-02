<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use Uuid\Uuid;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // fix   Notice: Use of undefined constant GLOB_BRACE - assumed 'GLOB_BRACE'
        define('GLOB_BRACE', 0);

        $objects = Fixtures::load(__DIR__.'/fixtures.yml', $manager, ['providers' => [$this]]);
    }

    public function uuid4()
    {
        return Uuid::create();
    }

    private static $arr = [
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

    public function purpose()
    {
        return next(self::$arr);
    }
}
