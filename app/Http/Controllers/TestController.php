<?php

namespace App\Http\Controllers;

use App\Libs\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Zhuud\Proxy\RouteProxy;

class TestController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Support\Facades\Route $route
     * @return mixed
     */
    public function index(Request $request, Route $route)
    {
        return RouteProxy::parse()
            ->setMaxCalls(10)
            ->verMaxCalls($request)
            ->dispatch($request,$route::getRoutes())
            ->getResult();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function test(Request $request)
    {
        $argc = [
            'appId' => '123456',
            'timeStamp' => '123456',
            'nonceStr' => '123456',
            'params' => [
                'a' => '1',
                'c' => '2',
                'b' => '3',
            ],
        ];
        Arr::kSort($argc);
        return $argc;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
