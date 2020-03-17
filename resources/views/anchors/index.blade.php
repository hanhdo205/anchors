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
                <td><a href="/anchors/result/{{$anchor->keyword}}">{{ $anchor->keyword }}</a></td>
                <td>{{ $anchor->status }}</td>
                <td>{{ $anchor->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
	{!! $anchors->links() !!}
	<script>
      const artists = <?php echo json_encode($anchors) ;?>;
      console.table(artists['data']);
    </script>
@endsection