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
		<table class="table table-bordered data-table wrap">
			<thead class="thead-dark">
				<tr>
					<th nowrap scope="col">ID</th>
					<th nowrap scope="col">キーワード</th>
					<th nowrap scope="col">ステータス</th>
					<th nowrap scope="col">登録日</th>
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
				   language:
					{
						 "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json"
					},
				   ajax: "{{ url('anchor-list') }}",
				   columns: [
							{ data: 'id', name: 'id' },
							{ data: 'keyword', name: 'keyword',fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
									if(oData.is_link > 1 && oData.is_link < 5) {
										$(nTd).html("<a href='/anchors/getrank/"+oData.id+"'>"+oData.keyword+"</a>");
									}
								}
							},
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