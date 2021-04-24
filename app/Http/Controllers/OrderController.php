<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\OrdersDataTable;
use App\Models\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(OrdersDataTable $dataTable)
    {
        return $dataTable->render('pages.orders.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $users      = User::all();

        return view('pages.orders.create')->with([
            'categories'    =>  $categories,
            'users'         =>  $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => ['required'],
            'body'          => ['required','min:0'],
            'excerpt'       => ['required','min:0'],
            'image'         => ['required','image'],
            'banner'         => ['required','image'],
            'author_id'     => ['required','exists:users,id'],
            'category_id'   => ['required','exists:categories,id'],
        ]);

        $order = Order::create($request->only(['title','body','excerpt', 'author_id','category_id']));

        $order->addMedia($request->file('image'))->toMediaCollection('image');
        $order->addMedia($request->file('banner'))->toMediaCollection('banner');

        return redirect()->route('orders.show',$order->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return view('pages.orders.show',[
            'order'=>$order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        $categories = Category::all();
        $users      = User::all();

        return view('pages.orders.edit',[
            'order'       =>  $order,
            'categories'    =>  $categories,
            'users'         =>  $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'title'         => ['required'],
            'body'          => ['required','min:0'],
            'excerpt'       => ['required','min:0'],
            'image'         => ['nullable','image'],
            'banner'         => ['nullable','image'],
            'author_id'     => ['required','exists:users,id'],
            'category_id'   => ['required','exists:categories,id'],
        ]);

        $order->update($request->only(['title','body', 'excerpt','author_id', 'category_id']));

        if ($request->has('image') && $request->file('image') !=null) {
            $order->clearMediaCollection('image');
            $order->addMedia($request->file('image'))->toMediaCollection('image');
        }

        if ($request->has('banner') && $request->file('banner') !=null) {
            $order->clearMediaCollection('banner');
            $order->addMedia($request->file('banner'))->toMediaCollection('banner');
        }

        return redirect()->route('orders.show',$order->id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        try{
            // $views = View::where(['post_id'=>$order->id,'type'=>'post'])->first();
            // $views->delete();
            $order->delete();
        }catch(\Exception $ex){}

        return redirect()->route('orders.index');
    }
}
