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
                <td><a href="/anchors/getanchor/{{ $id }}/{{ $key }}">{{ $value['link'] }}</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
	<script>
      const results = <?php echo json_encode($results) ;?>;
      console.table(results);
    </script>
	* Using command: <strong>php artisan getRank {Keyword ID}</strong>
@endsection