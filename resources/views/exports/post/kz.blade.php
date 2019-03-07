<html>
<table>
    <thead>
    <tr>
        <th>Направление</th>
        <th text="left">1</th>
    </tr>
    <tr>
        <th>Вид РПО</th>
        <th text="left">3</th>
    </tr>
    <tr>
        <th>Категория РПО</th>
        <th text="left">4</th>
    </tr>
    <tr>
        <th>Отправитель</th>
        <th text="left">ТОО БУЛГАР МФ</th>
    </tr>
    <tr>
        <th>Регион назначения</th>
        <th text="left">01</th>
    </tr>
    <tr>
        <th>Индекс ОПС места приема</th>
        <th text="left">010013</th>
    </tr>
    <tr>
        <th>Всего РПО</th>
        <th text="left">{{ $orders->count() }}</th>
    </tr>
    </thead>

  <thead>
  <tr>
      <th bgcolor="ffff99">№</th>
      <th bgcolor="ffff99">ФИО</th>
      <th bgcolor="ffff99">Индекс</th>
      <th bgcolor="ffff99">Адрес</th>
      <th bgcolor="ffff00">ШПИ</th>
      <th bgcolor="ffff00">Вес (кг.)</th>
      <th bgcolor="ffff00">Сумма объявленной ценности</th>
      <th bgcolor="ffff00">Сумма нал. Платежа</th>
      <th bgcolor="ffff00">Сотовый номер №1</th>
  </tr>
  </thead>
  <tbody>
    @php $n =0; @endphp
    @foreach ($orders as $order)
      @php $n++; @endphp
      <tr>
        <td>{{ $n }}</td>
        <td>{{ $order->name_last . ' ' . $order->name_first . ' ' . $order->name_middle }}</td>
        <td>{{ $order->postal_code }}</td>
        <td>{{ $order->address }}</td>
        <td>{{ $order->track }}</td>
        <td></td>
        <td>{{ $order->price }}</td>
        <td>{{ $order->price }}</td>
        <td>{{ $order->phone }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
</html>
