@extends('layouts.app')

@section('content')

	<table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">Rank ID</th>
            <th scope="col">Title</th>
            <th scope="col">URL</th>
        </tr>
        </thead>
        <tbody>
        @foreach($results as $key => $value)
            <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <td>{{ $value['title'] }}</td>
                <td>
					@if($status == 4)
						<a href="/anchors/getanchor/{{ $keyword }}/{{ $key }}">{{ $value['link'] }}</a>
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