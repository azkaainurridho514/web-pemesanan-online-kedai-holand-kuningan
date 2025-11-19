<table>
    <thead>
        <tr>
            <th>Produk</th>
            <th>Jumlah Terjual</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reports as $report)
        <tr>
            <td>{{ $report->product_name }}</td>
            <td>{{ $report->total_sold }}</td>
            <td>{{ $report->total_revenue }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
