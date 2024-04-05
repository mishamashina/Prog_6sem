@extends('layout')
@section('content')
<table class="table">
  <thead>
    <tr>
      <th scope="col">Title</th>
      <th scope="col">Text</th>
      <th scope="col">Article</th>
      <th scope="col">User</th>
      <th scope="col">Accept/Reject</th>
    </tr>
  </thead>
  <tbody>
    @foreach($comments as $comment)
    <tr>
      <th scope="row">{{$comment->title}}</th>
      <th scope="row">{{$comment->text}}</th>
      <td><a href="/article/{{$comment->article_id}}">{{$comment->text}}</a></td>
      <td>{{$comment->desc}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
{{$comments->links()}}
@endsection