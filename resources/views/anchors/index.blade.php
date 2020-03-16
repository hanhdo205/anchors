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
                <th scope="row">{{$anchor->id}}</th>
                <td>{{$anchor->keyword}}</td>
                <td>{{$anchor->status}}</td>
                <td>{{$anchor->created_at}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection