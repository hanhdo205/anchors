@extends('layouts.app')

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
						@foreach ($errors->all() as $error)
							<div class="alert alert-danger">{{ $error }}</div>
						@endforeach
					@endif
			  <button type="submit" class="btn btn-primary">登録</button>
			</form>
		</div>
    <div class="col-8">
		<div class="alert alert-warning" role="alert">
		  キーワードを登録してください。
		</div>
    </div>
  </div>
</div>

@endsection