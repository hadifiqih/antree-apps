@extends('layouts.app')

@section('content')
    <table id="servisMasuk" class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Ticket Order</th>
                <th>Sales</th>
                <th>Customer</th>
                <th>Kategori</th>
                <th>Pekerjaan</th>
                <th>Spesifikasi</th>
                <th>Omset</th>
                <th>Bukti Pembayaran</th>
                <th>Status</th>
            </tr>
        </thead>
    </table>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        $('#servisMasuk').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('antrian-service.index') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'ticket_order', name: 'ticket_order' },
                { data: 'sales', name: 'sales' },
                { data: 'customer', name: 'customer' },
                { data: 'kategori', name: 'kategori' },
                { data: 'pekerjaan', name: 'pekerjaan' },
                { data: 'spesifikasi', name: 'spesifikasi' },
                { data: 'omset', name: 'omset' },
                { data: 'bukti_pembayaran', name: 'bukti_pembayaran' },
                { data: 'status', name: 'status' },
            ]
        });
    });
</script>
@endsection
