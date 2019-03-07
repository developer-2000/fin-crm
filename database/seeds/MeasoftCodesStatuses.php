<?php

use Illuminate\Database\Seeder;

class MeasoftCodesStatuses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $measoft = \App\Models\TargetConfig::where('alias', 'measoft')->first();

        $statuses = [

            // Добавлена новая запись в адреса.
            'NEW' => 'Новый',

            // Сделали прием корр. (f10 в заказах или через функции).
            'ACCEPTED' => 'Получен складом',

            // Отсканировали корр. в "Функции"-"Инвентаризация корреспонденции".
            'INVENTORY' => 'Инвентаризация',

            // Запланировали в манифест.
            'DEPARTURING' => 'Планируется отправка',

            // В манифесте поставили дату отправки и отправление в мешке.
            'DEPARTURE' => 'Отправлено со склада',

            // При выдаче курьеру, стаус в выдаче "на руках".
            'DELIVERY' => 'Выдан курьеру на доставку',

            // Статус со слов курьера - "Доставлено" или статус в выдаче
            // "Доставлено".
            'COURIERDELIVERED' => 'Доставлен (предварительно)',

            // Установлена дата и время вручения.
            'COMPLETE' => 'Доставлен',

            // Установлена дата и время вручения, но есть возвраты вложений.
            'PARTIALLY' => 'Доставлен частично',

            // Статус в выдаче "не доставлено" или "не доставлено по причине".
            'COURIERRETURN' => 'Возвращено курьером (курьер вернул на склад)',

            // Установлена только дата вручения, без времени.
            'CANCELED' => 'Не доставлен (Возврат/Отмена)',

            // Отсканировали в Акте возврата корр.
            'RETURNING' => 'Планируется возврат',

            // У акта возврата корр. Поставили дату отправки.
            'RETURNED' => 'Возвращен',

            // Установлен статус корр. из переменной
            // "Корреспонденция"-"Статусы успешного согласования доставки".
            'CONFIRM' => 'Согласована доставка',

            // Изменена плановая дата доставки.
            'DATECHANGE' => 'Перенос',

            // Добавлено новая запись в адреса, но это забор
            // (номер=0 и штрихкод = "").
            'NEWPICKUP' => 'Создан забор',

            // Установлен статус корр. из переменной
            // "Корреспонденция"-Статусы НЕ успешного согласования доставки.
            'UNCONFIRM' => 'Не удалось согласовать доставку',

            // Если получатель пункт  ПВЗ  и корр. Приняли в ПВЗ через Прием
            // (f10 в заказах или через функции).
            'PICKUPREADY' => 'Готов к выдаче',

            // Забран у отправителя - это если в этом же заказе
            // есть нулевая позиция, и ей поставили статус доставлено.
            'PICKUP' => 'Забран у отправителя',
        ];

        $procStatuses = [
            'CANCELED'  => \App\Models\ProcStatus::where('action', 'returned')->value('id'),
            'COMPLETE'  => \App\Models\ProcStatus::where('action', 'received')->value('id'),
            'DELIVERY'  => \App\Models\ProcStatus::where('action', 'sent')->value('id'),
            'DEPARTURE' => \App\Models\ProcStatus::where('action', 'sent')->value('id'),
        ];

        foreach ($statuses as $code => $name) {
            \App\Models\Api\CodeStatus::firstOrCreate([
                'integration_id' => $measoft->id,
                'status_code'    => $code,
            ], [
                'status' => $name,
                'system_status_id' => $procStatuses[$code] ?? 0
            ]);
        }

        $status = new \App\Models\ProcStatus();
        $status->project_id = 0;
        $status->name = "Ошибка API";
        $status->type = \App\Models\ProcStatus::TYPE_SENDERS;
        $status->priority = 9;
        $status->locked = 1;
        $status->action_alias = 'api_errors';
        $status->save();
    }
}
