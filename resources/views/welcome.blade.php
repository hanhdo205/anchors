@extends('layouts.app')
<!-- script -->
<link  href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

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
		<table class="table table-bordered data-table">
			<thead class="thead-dark">
				<tr>
					<th scope="col">ID</th>
					<th scope="col">キーワード</th>
					<th scope="col">ステータス</th>
					<th scope="col">登録日</th>
				</tr>
			</thead>
		</table>
		<script>
		   jQuery(document).ready( function ($) {
			$('.data-table').DataTable({
				   processing: true,
				   serverSide: true,
				   searching: false,
				   order: [[ 0, "desc" ]],
				   ajax: "{{ url('anchor-list') }}",
				   columns: [
							{ data: 'id', name: 'id' },
							{ data: 'keyword', name: 'keyword' },
							{ data: 'status', name: 'status' },
							{ data: 'created_at', name: 'created_at' }
						 ]
				});
			 });
		</script>
		<!--* Using command: <strong>php artisan getRank</strong>-->
		
    </div>
  </div>
</div>

@endsection