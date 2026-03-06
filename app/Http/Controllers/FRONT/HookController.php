<?php


namespace App\Http\Controllers\FRONT;


use App\Http\Helpers\Helpers;
use App\Models\Country;
use Illuminate\Http\Request;

class HookController
{

    function countries(Request $request)
    {
        $lists=Country::query()->where(['status'=>1])->get();
        $data=[];
        foreach ($lists as $list){
            $data[]=[
                "id"=>$list->id,
                "name"=>$list->name,
                "numcode"=>$list->numcode,
                "phonecode"=>$list->phonecode,
                "flag"=>$list->flag,
            ];
        }
        return Helpers::success($data);
    }
}
