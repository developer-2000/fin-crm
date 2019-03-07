<?php

use Illuminate\Database\Seeder;

class FillIntegrationCodesStatusesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        //kazpost codes
//        $status_mappings = '{"BOXISS_UNDO":"Отмена вручения из а-я","AVIASND":"Отправка на рейс","AVIASND_UNDO":"Отмена отправки на рейс","BAT":"Партионный прием","CUSTOM_RET":"Возврат с таможни","CUSTSRT_SND":"Выпуск с таможни(с хранения)","CUSTSTR_RET":"Возврат с таможни (с хранения)","DELAY_RET":"Возврат с таможни","DLV":"Ожидает клиента","DLV_POBOX":"Доставка в а/я","DPAY":"Вручено","ISSPAY":"Вручено","ISSSC":"Вручено","NON":"Не выдано","NONDLV":"Не доставлено","NONDLV_S":"Возврат на хранение","NONDLV_Z":"Ожидает клиента, На хранение","NON_S":"Не выдано","PRC":"Поступление","RCP":"Прием 1","RCPOPS":"Прием","RDR":"Досыл","RDRSC":"Досыл","RDRSCSTR":"Досыл","RET":"Возврат","RETSC":"Возврат","RETSCSTR":"Возврат","RPODELAY":"Задержка на таможенном досмотре","SND":"Отправка из постамата","SNDDELAY":"Выпуск задержанного  из  таможенного досмотра на возврат","SNDZONE":"Поступление на участок сортировки","SNDZONE_T":"Выпущено таможней","SRTRPOREG":"Сортировка","SRTSND":"Отправка из участка сортировки","SRTSNDB":"Отправка из СЦ","SRTSNDB_UNDO":"Отмена отправки","SRTSNDIM":"Отправка из СЦ","SRTSNDIM_UNDO":"Отмена отправки","SRTSND_UNDO":"Отмена отправки транспорта","SRT_CUSTOM":"Передано таможне","STR":"Хранение","STRSC":"Возврат с хранения","TRNRPO":"Прибытие","TRNSRT":"Прибытие транспорта в сортцентр","BOXISS":"Вручение из а/я","DEL_STR":"Удаление с хранения","DLV_POBOX_UNDO":"Отмена доставки в а/я","DLV_UNDO":"Отмена доставки","NONTRNOPS":"Неприбытие","NONTRNSRT":"Неприбытие","PRNNTC2_UNDO":"Отмена  хранения","RDRSCSTR_UNDO":"Отмена досыла с хранения в СЦ","RDRSC_UNDO":"Отмена досыла в СЦ","RDR_UNDO":"Отмена досыла","REGPBT":"Регистрация на участке","REGPBT_UNDO":"Отмена регистрации","REGSRT":"Регистрация на участке","REGSRT_UNDO":"Отмена регистрации","RETSCSTR_UNDO":"Отмена возврата с хранения в СЦ","RETSC_UNDO":"Отмена возврата в СЦ","RET_UNDO":"Отмена возврата","RPODELAY_UNDO":"Отмена задержки РПО","SNDDELAY_UNDO":"Отмена выпуска задержанного","SNDZONE_T_UNDO":"Отмена выпуска из участка ТК","SNDZONE_UNDO":"Отмена передачи в зону сортировки","SRTRPOREG_UNDO":"Отмена приписки к емкости (документу)","SRT_CUSTOM_UNDO":"Отмена передачи на таможенный контроль","STRCUST":"Передать на хранение","STRCUST_UNDO":"Отмена передачи на хранение","TRN":"Прибытие транспорта","TRNBAG":"Прибытие емкости","TRNSRT_UNDO":"Отмена прибытия","TRN_UNDO":"Отмена прибытия транспорта","CORRECT":"Корректировка данных отправления","EME":"Отправление задержано на таможне","EDA":" Находится на входящем участке обмена","EDB":"Отправление предъявлено таможне","EDC":"Отправление возвращено из таможни","EDD":"Отправление поступило в промежуточный сортировочный центр","EDE":"Отправление покинуло промежуточный сортировочный центр","EDF":"Отправление в пункте доставки на хранении","EDG":"Отправление передано почтальону/курьеру на доставку","EDH":"Отправление поступило в пункт самовывоза","EDX":"Отправление задержано контролирующими органами","EMA":"Прием отправления","EMB":"Отправление прибыло в промежуточный пункт обмена","EMC":"Отправление покинуло промежуточный сортцентр","EMD":"Отправление прибыло в промежуточный пункт обмена","EMF":"Отправление покинуло пункт обмена в стране получателя","EMG":"Отправление прибыло в пункт выдачи","EMH":"Доставка отправления почтальоном/курьером не состоялась","EMI":"Отправление успешно доставлено","EMJ":"Прибытие в транзитный пункт обмена","EXA":"Отправление передано на таможню страны отправителя","EXB":"Отправление получено таможней страны отправителя","EXC":"Отправление успешно прошло таможенный контроль","EXD":"Отправление задержано в пункте обмена","EXX":"Отправка исходящего отправления отменена","TRNPST":"Прибытие в постамат","STRPST":"Хранение в постамате","RETPST":"Выемка из постамата","TRANSITRCV":"Прибытие в СЦ(транзит)","TRANSITSND":"Отправка из СЦ(транзит)"}';
//        $kazpostArray = json_decode($status_mappings, true);
//
//        //Novaposhta codes
//        $novapostaCodes = [
//            1   => 'Нова пошта очікує надходження від відправника',
//            2   => 'Видалено',
//            3   => 'Номер не знайдено',
//            4   => 'Відправлення у місті ХХXХ. (Статус для межобластных отправлений)',
//            41  => 'Відправлення у місті ХХXХ. (Статус для услуг локал стандарт и локал экспресс - доставка в пределах города)',
//            5   => 'Відправлення прямує до міста YYYY',
//            6   => 'Відправлення у місті YYYY, орієнтовна доставка до ВІДДІЛЕННЯ-XXX dd-mm.
//    Очікуйте додаткове повідомлення про прибуття.',
//            7   => 'Прибув на відділення',
//            8   => 'Прибув на відділення',
//            9   => 'Відправлення отримано',
//            10  => 'Відправлення отримано %DateReceived%.
//Протягом доби ви одержите SMS-повідомлення про надходження грошового переказу
//та зможете отримати його в касі відділення «Нова пошта».',
//            11  => 'Відправлення отримано %DateReceived%.
//Грошовий переказ видано одержувачу.',
//            14  => 'Відправлення передано до огляду отримувачу',
//            101 => 'На шляху до одержувача',
//            102 => 'Відмова одержувача',
//            103 => 'Відмова одержувача',
//            108 => 'Відмова одержувача',
//            104 => 'Змінено адресу',
//            105 => 'Припинено зберігання',
//            106 => 'Одержано і є ТТН грошовий переказ',
//            107 => 'Нараховується плата за зберігання',
//        ];
//
//        //Viettel codes
//        $viettelCodes = [
//            100 => 'Tiếp nhận đơn hàng từ đối tác',
//            101 => 'ViettelPost yêu cầu hủy đơn hàng',
//            102 => 'Đơn hàng chờ xử lý',
//            103 => 'Giao cho bưu cục',
//            104 => 'Giao cho Bưu tá đi nhận',
//            105 => 'Buu Tá đã nhận hàng',
//            106 => 'Đối tác yêu cầu lấy lại hàng',
//            107 => 'Đối tác yêu cầu hủy qua API',
//            200 => 'Nhận từ bưu tá - Bưu cục gốc',
//            201 => 'Hủy nhập phiếu gửi',
//            202 => 'Sửa phiếu gủi',
//            300 => 'Đóng bảng kê đi',
//            301 => 'Ðóng túi gói',
//            302 => 'Đóng Chuyến thư',
//            303 => 'Đóng tuyến xe',
//            400 => 'Nhận bảng kê đến',
//            401 => 'Nhận Túi gói',
//            402 => 'Nhận chuyến thư',
//            403 => 'Nhận chuyến xe',
//            500 => 'Giao bưu tá đi phát',
//            501 => 'Thành công - Phát thành công',
//            502 => 'Chuyển hoàn bưu cục gốc',
//            503 => 'Hủy - Theo yêu cầu khách hàng',
//            504 => 'Thành công - Chuyển trả người gửi',
//            505 => 'Tồn - Thông báo chuyển hoàn bưu cục gốc',
//            506 => 'Tồn - Khách hàng nghỉ, không có nhà',
//            507 => 'Tồn - Khách hàng đến bưu cục nhận',
//            508 => 'Phát tiếp',
//            509 => 'Chuyển tiếp bưu cục khác',
//            510 => 'Hủy phân công phát',
//            515 => 'Bưu cục phát duyệt hoàn',
//            550 => 'Đơn Vị Yêu Cầu Phát Tiếp'
//        ];
//        //Wefast codes
//
//        $wefastCodes = [
//            000 => "Chờ gửi vận đơn",
//            001 => "Đã gửi vận đơn",
//            002 => "Nhân viên duyệt vận đơn",
//            003 => "Gửi vận đơn thất bại",
//            100 => "Tiếp nhận vận đơn",
//            101 => "Hủy theo yêu cầu",
//            102 => "Trung tâm từ chối vận đơn",
//            103 => "Giao bưu cục",
//            104 => "Bưu tá đi nhận hàng",
//            105 => "Bưu tá đã nhận hàng",
//            106 => "Bưu tá không nhận được hàng",
//            109 => "Bưu cục chuyển trả đơn hàng",
//            200 => "Nhận từ bưu tá - Bưu cục gốc",
//            202 => "Tách tải kiện",
//            300 => "Đóng bảng kê đi",
//            301 => "Đóng túi gói",
//            302 => "Đóng chuyến thư",
//            303 => "Gửi chuyến thư đi",
//            400 => "Nhận bảng kê đến",
//            401 => "Nhận túi thư",
//            402 => "Nhận chuyến thư",
//            403 => "Nhận chuyến xe",
//            500 => "Bưu tá đi phát",
//            501 => "Phát thành công",
//            502 => "Đang chuyển hoàn",
//            503 => "Hủy - Theo yêu cầu khách hàng",
//            504 => "Đã chuyển hoàn",
//            505 => "Tồn - Sai địa chỉ phát",
//            506 => "Tồn - Khách hàng nghỉ không có nhà",
//            507 => "Tồn - Khách hàng đến bưu cục nhận",
//            512 => "Tồn - Khách hàng từ chối nhận",
//            513 => "Tồn - Điện thoại không liên lạc được",
//            508 => "Chuyển tiếp",
//            509 => "Giữ lại bưu cục phát xử lý",
//            510 => "Hủy phân công phát",
//            600 => "Đang đối soát",
//            601 => "Đã đối soát",
//            602 => "Đối soát không thành công",
//            603 => "Đã thanh toán",
//            604 => "Chưa thanh toán",
//            605 => "Đã tính cước",
//            607 => "Đã tính cước và cước chuyển hoàn"
//        ];
//
//        $measoft = [
//            'NEW' => 'Новый',
//            'PICKUP' => 'Забран у отправителя',
//            'ACCEPTED' => 'Получен складом',
//            'INVENTORY' => 'Инвентаризация',
//            'DEPARTURING' => 'Планируется отправка',
//            'DEPARTURE' => 'Отправлено со склада',
//            'DELIVERY' => 'Выдан курьеру на доставку',
//            'COURIERDELIVERED' => 'Доставлен (предварительно)',
//            'COMPLETE' => 'Доставлен',
//            'PARTIALLY' => 'Доставлен частично',
//            'COURIERRETURN' => 'Курьер вернул на склад',
//            'CANCELED' => 'Не доставлен (Возврат/Отмена)',
//            'RETURNING' => 'Планируется возврат',
//            'RETURNED' => 'Возвращен',
//            'CONFIRM' => 'Согласована доставка',
//            'DATECHANGE' => 'Перенос',
//            'NEWPICKUP' => 'Создан забор',
//            'UNCONFIRM' => 'Не удалось согласовать доставку',
//            'PICKUPREADY' => 'Готов к выдаче',
//        ];

        $ninjaxpress = [
            'Staging'                        => 'Staging',
            //	A Order has been created and is in Staging phase
            'Pending Pickup'                 => 'Pending Pickup',
            //Order has been confirmed and is pending pickup
            'Van en-route to pickup'         => 'Van en-route to pickup',
            //A van has been dispatched to pick up Order
            'En-route to Sorting Hub'        => 'En-route to Sorting Hub',
            //Order has been picked up and is en-route to Sorting Hub
            'Arrived at Sorting Hub'         => 'Arrived at Sorting Hub',
            //Order has arrived at Sorting Hub and has been processed successfully
            'Arrived at Origin Hub'          => 'Arrived at Origin Hub',
            //Order has arrived at Origin Hub and has been processed successfully
            'On Vehicle for Delivery'        => 'On Vehicle for Delivery',
            //Order is on van, en-route to delivery
            'Completed'                      => 'Completed',
            //Delivery has been successfully completed
            'Pending Reschedule'             => 'Pending Reschedule',
            //Delivery has failed and the Order is pending re-schedule
            'Pickup fail'                    => 'Pickup fail',
            //Pickup has failed and the Order is awaiting re-schedule
            'Cancelled'                      => 'Cancelled',
            //Order has been cancelled
            'Returned to Sender'             => 'Returned to Sender',
            //Delivery of Order has failed repeatedly, sending back to Sender
            'Parcel Size'                    => 'Parcel Size',
            //The parcel size of an Order has been changed
            'Arrived at Distribution Point'  => 'Arrived at Distribution Point',
            //The parcel has been placed at the Distribution Point for customer collection
            'Successful Delivery'            => 'Successful Delivery',
            //Proof of delivery is now ready
            'Successful Pickup'              => 'Successful Pickup',
            //Proof of pickup is now ready
            'Parcel Weight'                  => 'Parcel Weight',
            //The parcel weight of an Order has been changed
            'Cross Border Transit'           => 'Cross Border Transit',
            ///Order is in cross border leg or is pending tax payment from consignee if required
            'Customs Cleared'                => 'Customs Cleared',
            //Order is ready for pickup from customs warehouse
            'Customs Held'                   => 'Customs Held',
            //Order is in customs clearance exception
            'Return to Sender Triggered'     => 'Return to Sender Triggered',
            //Return to Sender request has been triggered
            'Pending Pickup at Distribution' => 'Pending Pickup at Distribution',
            //Point	Order has been received at Distribution Point and is pending pickup
            'Parcel Measurements Update'     => 'Parcel Measurements Update'
            //The parcel size, or parcel weight, or parcel dimensions of an Order has been changed
        ];

        $codes = [
//            4  => $kazpostArray,
//            16 => $wefastCodes,
//            1  => $novapostaCodes,
//            17 => $viettelCodes,
//            3  => $measoft,
21 => $ninjaxpress
        ];

        foreach ($codes as $key => $code) {
            foreach ($code as $keyCode => $rowCode) {
                \App\Models\Api\CodeStatus::updateOrCreate([
                    'integration_id' => $key,
                    'status_code'    => $keyCode,
                    'status'         => $rowCode,
                ], [], ['system_status_id' => 0]);
            }
        }
    }
}
