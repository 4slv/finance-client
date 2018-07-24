<?php

namespace ApiClient\App;

use MyCLabs\Enum\Enum;

/** Статусы задач */
class Status extends Enum
{
    /** Устанавливается по умолчанию для всех новых задач */
    const OPEN = 'open';

    /** Задача успешно выполнена */
    const SUCCESS = 'success';

    /** Задача успешно выполнена и закрыта */
    const CLOSE = 'close';

    /** Задача не выполнена, произошла ошибка */
    const ERROR = 'error';

    /** Задача отклонена, ошибка конфигурации задачи */
    const REJECT = 'reject';

    /** Задача не может быть выполнена, т.к. предыдущая задача завершена с ошибкой */
    const BLOCK = 'block';

    /** Превышено максимальное количество попыток */
    const CANCEL = 'cancel';
}