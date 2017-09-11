<?php

declare(strict_types=1);

namespace App;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Roles
{
    /**
     * Член СНТ.
     */
    const COMMUNITY = 'ROLE_COMMUNITY';

    /**
     * Писатель новостей.
     */
    const NEWS_WRITER = 'ROLE_NEWS_WRITER';

    /**
     * Кассир
     */
    const CASHIER = 'ROLE_CASHIER';

    /**
     * Председатель.
     */
    const CHAIRMAN = 'ROLE_CHAIRMAN';

    /**
     * Роль для отображение админской панели навигации.
     */
    const EMPLOYEE = 'ROLE_EMPLOYEE';
}
