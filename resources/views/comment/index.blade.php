@extends('layout')
@section('content')
<table class="table">
  <thead>
    <tr>
      <th scope="col"> Date </th>
      <th scope="col"> Name </th>
      <th scope="col"> ShortDesc</th>
      <th scope="col"> Desc </th>
      <th scope="col"> Preview image </th>
    </tr>
  </thead>
  <tbody>
    @foreach($comments as $comment)
    <tr>
      <th scope="row"> {{$comment->title}}</th>
      <td> {{$comment->text}}</td>
      <td> <a href="/article/{{$comment->article_id}}">  {{$comment->article_name}} </a> </td>
      <td> {{$comment->name}}</td>
      <td>
        @if($comment->accept == 'true')
         <a class="btn btn-warning" href="/comment/{{$comment->id}}/reject">  Reject </a> 
        @else
        <a class="btn btn-success" href="/comment/{{$comment->id}}/accept">  Accept </a>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection