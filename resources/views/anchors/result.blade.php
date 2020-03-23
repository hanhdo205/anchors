@extends('layouts.app')

@section('content')

	<table class="table result-table">
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
	<!--* Using command: <strong>php artisan getAnchor</strong>-->
@endsection