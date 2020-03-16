@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"></div>

                <div class="panel-body">
                    <form action="/anchors" method="POST" role="form">
					@csrf <!-- {{ csrf_field() }} -->
					  <div class="form-group">
						<label for="keyword">Anchor Research System</label>
						<input type="text" name="keyword" class="form-control" id="keyword" placeholder="キーワードを登録してください。">
					  </div>
					  <input type="hidden" name="status" value="1">
					  <button type="submit" class="btn btn-primary">登録</button>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection