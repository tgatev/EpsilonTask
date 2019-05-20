@extends('layouts.Layout')

@section('content')
    <div class="align-text-top">
    <table id ="service_details" style="border: 2px solid lightblue">
        <thead>
            <td class="td-details">Service Name</td>
            <td class="td-details">Port Name</td>
            <td class="td-details">BPort Name</td>
            <td class="td-details">Service Status</td>

            <td class="td-details">2 way Latency</td>
            <td class="td-details">Discard</td>
        </thead>
        <tbody>
            <row>
                <td class="td-details" >{{$ServiceDetails->name}}</td>
                <td class="td-details">{{$ServiceDetails->port_name}}</td>
                <td class="td-details">{{$ServiceDetails->b_port_name}}</td>
                <td class="td-details">{{$ServiceDetails->status_state}}</td>
                <td class="td-details">{{$ServiceDetails->two_way_latency}}</td>
                <td class="td-details">{{$ServiceDetails->discard}}</td>
            </row>
        </tbody>

    </table>
    </div>
@stop
