@extends('layouts.app')

<!-- Style -->
<style>
body
{
    counter-reset: Count-Value;     
}
table
{
    border-collapse: separate;
}
tr td:first-child:before
{
  counter-increment: Count-Value;   
  content: counter(Count-Value);
}
</style>
@section('content')
<div class="container">
	<div class="row">
		<div class="col-4">
			<form action="/anchors" method="POST" role="form">
				@csrf <!-- {{ csrf_field() }} -->
				<div class="form-group">
					<textarea id="keyword" name="keyword" class="form-control" rows="4" cols="50"  placeholder="キーワードを登録してください。" autofocus="autofocus"></textarea>
				</div>
					@if ($errors->any())
						<div class="alert alert-danger">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
			  <button type="submit" class="btn btn-primary">登録</button>
			</form>
		</div>
    <div class="col-8">
		@if(isset($anchors[0]->id))
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
					<td></td>
					<td>
						@if($anchor->status > 1)
							<a href="/anchors/getrank/{{$anchor->keyword}}">{{ $anchor->keyword }}</a>
						@else
							{{ $anchor->keyword }}
						@endif
					</td>
					<td>@switch($anchor->status)
							@case(4)
								{{ Config::get('constants.status.4') }}
								@break
							@case(3)
								{{ Config::get('constants.status.3') }}
								@break
							@case(2)
								{{ Config::get('constants.status.2') }}
								@break
							@default
								{{ Config::get('constants.status.1') }}
								@break
						@endswitch
					</td>
					<td>{{ date_format($anchor->created_at,'m/d/yy') }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<!--* Using command: <strong>php artisan getRank</strong>-->
	@else
		キーワードを登録してください。
	@endif
	{!! $anchors->links() !!}
    </div>
  </div>
</div>

@endsection