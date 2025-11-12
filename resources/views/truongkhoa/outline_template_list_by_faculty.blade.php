@extends($layout ?? 'layouts.apptruongkhoa')

@section('content')
    @include('shared.crud-template', [
        'title' => $title,
        'tableId' => $tableId,
        'ajaxUrl' => $ajaxUrl,
        'deleteUrl' => $deleteUrl,
        'createUrl' => $createUrl,
        'columns' => $columns,
    ])
@endsection
