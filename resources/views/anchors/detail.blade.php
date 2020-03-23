@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">URL: <a href="{{ $result['link'] }}" target="_blank">{{ $result['link'] }}</a></div>

                <div class="panel-body">
					<table class="table table-bordered">
					  <thead>
						<tr>
						  <th>Title</th>
						  <th>Description</th>
						</tr>
					  </thead>
					  <tbody>
						<tr>
						  <td>
							{{ $result['title'] }}
						  </td>
						  <td>
							{{ $result['description'] }}
						  </td>
						</tr>
					  </tbody>
					</table>
                </div>
            </div>
        </div>
    </div>

	<table class="table detail-table">
        <thead class="thead-dark">
        <tr>
            <th nowrap scope="col">ID</th>
            <th nowrap scope="col">Anchor Text</th>
            <th nowrap scope="col">Anchor Type</th>
            <th nowrap scope="col">Anchor URL</th>
        </tr>
        </thead>
        <tbody>
        @foreach($anchors as $key => $anchor)
            <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <td>{{ $anchor['text'] }}</td>
                <td>{{ $anchor['type'] }}</td>
                <td>{{ $anchor['url'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
	{{ $anchors->links() }}
@endsection