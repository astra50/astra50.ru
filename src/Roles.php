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
    public const COMMUNITY = 'ROLE_COMMUNITY';

    /**
     * Писатель новостей.
     */
    public const NEWS_WRITER = 'ROLE_NEWS_WRITER';

    /**
     * Кассир
     */
    public const CASHIER = 'ROLE_CASHIER';

    /**
     * Председатель.
     */
    public const CHAIRMAN = 'ROLE_CHAIRMAN';

    /**
     * Роль для отображение админской панели навигации.
     */
    public const EMPLOYEE = 'ROLE_EMPLOYEE';

    /**
     * Доступ к публикации/редактированию отчётов.
     */
    public const REPORTS = 'ROLE_REPORTS';
}
