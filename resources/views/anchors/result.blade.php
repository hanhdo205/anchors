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
			@if(in_array($key,$access))
				@php ($class = 'class=table-active')
			@else
				@php ($class = '')
			@endif
            <tr {{ $class }}>
                <th scope="row">{{ $key + 1 }}</th>
                <td>{{ $value['title'] }}</td>
                <td><a href="/anchors/getanchor/{{ $id }}/{{ $key }}">{{ $value['link'] }}</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
	* Using command: <strong>php artisan getRank {Keyword ID}</strong>
@endsection