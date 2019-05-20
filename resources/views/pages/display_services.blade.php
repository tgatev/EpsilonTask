@extends('layouts.Layout')

@section('content')
    <table class="table table-bordered" id="Services-table">
        <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Vlan</th>
            <th>Nni Vlan</th>
            <th>Created</th>
            <th>Expires</th>
            <th>Protected</th>
            <th>Bandwidth</th>
            <th>Paused</th>
            <th>Expired</th>
            <th>Type</th>
            <th>Type Short Name</th>
            <th>Service Town</th>
            <th>Status</th>
            <th>Port</th>
            <th>B Port</th>
        </tr>
        </thead>
    </table>
@stop

@push('scripts')
<script>
    $(function() {
        $('#Services-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url :'{!! route('ServicesDatatable.data') !!}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'vlan', name: 'vlan' },
                { data: 'nni_vlan', name: 'nni_vlan' },
                { data: 'created', name: 'created' },
                { data: 'expires', name: 'expires' },
                { data: 'protected', name: 'protected' },
                { data: 'bandwidth', name: 'bandwidth' },
                { data: 'paused', name: 'paused' },
                { data: 'expired', name: 'expired' },
                { data: 'type', name: 'type' },
                { data: 'type_short_name', name: 'type_short_name' },
                { data: 'service_town', name: 'service_town' },
                { data: 'status', name: 'status' },
                { data: 'port', name: 'port' },
                { data: 'b_port', name: 'b_port' }
            ]
        });
    });
</script>
@endpush