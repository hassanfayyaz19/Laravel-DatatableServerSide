<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function allPosts(Request $request)
{

    $columns = array(
        0 =>'id',
        1 =>'username',
        2=> 'name',
        3=> 'email',
        4=> 'created_at',
        5=> 'id',
    );

    $totalData = Post::count();

    $totalFiltered = $totalData;

    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    if(empty($request->input('search.value')))
    {
        $posts = Post::offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
    }
    else {
        $search = $request->input('search.value');

        $posts =  Post::where('id','LIKE',"%{$search}%")
            ->orWhere('username', 'LIKE',"%{$search}%")
            ->orWhere('name', 'LIKE',"%{$search}%")
            ->orWhere('email', 'LIKE',"%{$search}%")
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

        $totalFiltered = Post::where('id','LIKE',"%{$search}%")
            ->orWhere('username', 'LIKE',"%{$search}%")
            ->count();
    }

    $data = array();
    if(!empty($posts))
    {
        foreach ($posts as $post)
        {
            /* $show =  route('posts.show',$post->id);
             $edit =  route('posts.edit',$post->id);*/

            $nestedData['id'] = $post->id;
            $nestedData['username'] = $post->username;
            $nestedData['name'] = $post->name;
            $nestedData['email'] = $post->email;
            $nestedData['created_at'] = date('j M Y h:i a',strtotime($post->created_at));
            $nestedData['options'] = "&emsp;<a href='#' title='SHOW' ><span class='fa fa-list'></span>edit</a>
                                      &emsp;<a href='#' title='EDIT' ><span class='glyphicon glyphicon-edit'></span>delete</a>";
            $data[] = $nestedData;

        }
    }

    $json_data = array(
        "draw"            => (int)$request->input('draw'),
        "recordsTotal"    => (int)$totalData,
        "recordsFiltered" => (int)$totalFiltered,
        "data"            => $data
    );

    echo json_encode($json_data);
}

}
