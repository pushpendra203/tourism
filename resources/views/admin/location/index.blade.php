@extends('admin.layout')
@section('title','Locations')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @component('admin.components.content-header',['breadcrumb'=>['Dashboard'=>'admin/dashboard']])
            @slot('title') Location @endslot
            @slot('add_btn') <a href="{{url('admin/location/create')}}" class="btn btn-primary btn-sm">Add New</a> @endslot
            @slot('active') Location @endslot
        @endcomponent
        <!-- /.content-header -->
        <!-- show data table component -->
        @component('admin.components.data-table',['thead'=>
            ['S NO.','Location Name','Status','Action']
        ])
            @slot('table_id') location-list @endslot
        @endcomponent
        @stop
@section('pageJsScripts')
<!-- DataTables -->
<script src="{{asset('public/assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/assets/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/assets/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('public/assets/js/responsive.bootstrap4.min.js')}}"></script>
<script type="text/javascript">
    var table = $("#location-list").DataTable({
        processing: true,
        serverSide: true,
        ajax: "location",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex',sWidth: '40px'},
            {data: 'location', name: 'location'},
            {data: 'status', name: 'status'},
            {
                data: 'action',
                name: 'action',
                orderable: true,
                searchable: true,
                sWidth: '100px'
            }
        ]
    });
</script>
@stop