@extends('layouts.app')

@section('content')

	<table class="table table-bordered data-table result-table">
        <thead class="thead-dark">
        <tr>
            <th nowrap scope="col">Rank ID</th>
            <th nowrap scope="col">Title</th>
            <th nowrap scope="col">URL</th>
        </tr>
        </thead>
        <tbody>
        @foreach($results as $key => $value)
            <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <td>{{ $value['title'] }}</td>
                <td>
					@if($status == 4)
						<a href="/anchors/getanchor/{{ $id }}/{{ $key }}">{{ $value['link'] }}</a>
					@else
						{{ $value['link'] }}
					@endif
				</td>
            </tr>
        @endforeach
        </tbody>
    </table>
	<script>
	   jQuery(document).ready( function ($) {
		$('.data-table').DataTable({
			   processing: true,
			   searching: false,
			   order: [[ 0, "asc" ]],
			   bPaginate: false,
			   language:
				{
					 "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json"
				}
			});

		 });
	</script>
	<div class="pb-5"></div>
	<!--* Using command: <strong>php artisan getAnchor</strong>-->
@endsection