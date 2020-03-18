@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">URL: {{ $result['link'] }}</div>

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

	<table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Anchor Text</th>
            <th scope="col">Anchor Type</th>
            <th scope="col">Anchor URL</th>
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
	<script>
      const anchors = <?php echo json_encode($anchors) ;?>;
      console.table(anchors['data']);
    </script>
@endsection