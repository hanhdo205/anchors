@extends('layouts.app')

@section('content')
    @if (Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
    @endif

    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">キーワード</th>
            <th scope="col">ステータス</th>
            <th scope="col">登録日</th>
        </tr>
        </thead>
        <tbody>
        @foreach($anchors as $anchor)
            <tr>
                <th scope="row">{{ $anchor->id }}</th>
                <td><a href="/anchors/getrank/{{$anchor->id}}">{{ $anchor->keyword }}</a></td>
                <td>@switch($anchor->status)
						@case(4)
							{{ Config::get('constant.status.4') }}
							@break
						@case(3)
							{{ Config::get('constant.status.3') }}
							@break
						@case(2)
							{{ Config::get('constant.status.2') }}
							@break
						@default
							{{ Config::get('constant.status.1') }}
							@break
					@endswitch
				</td>
                <td>{{ date_format($anchor->created_at,'m/d/yy') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
	{!! $anchors->links() !!}
	<script>
      const artists = <?php echo json_encode($anchors) ;?>;
      console.table(artists['data']);
    </script>
	* Using command: <strong>php artisan getRank {ID}</strong> to see the detail
@endsection