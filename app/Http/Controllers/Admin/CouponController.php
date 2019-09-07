<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Coupon;
use App\Image;
use Validator;
use DB;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *s
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.coupons.index', ['coupons' => Coupon::query()->paginate()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->edit();
    }

    /**
     * Display the specified resource.
     *
     * @param Coupon $coupon
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Coupon $coupon)
    {
        return view('admin.coupons.show', ['coupon' => $coupon]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Coupon $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon=null)
    {
        return view('admin.coupons.edit', ['coupon' => $coupon]);
    }

    /*
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Coupon $coupon
     * @return mixed
     */
    public function update(Request $request, Coupon $coupon=null)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:60',
            'url' => 'required|url|string|max:60',
            'description' => 'string|nullable|max:10000',
        ]);

//        $validator->sometimes(['thumbnail_filename'], 'required', function ($input) use ($request){
//            return $request->method() == "PUT";
//        });

        if ($validator->fails()) {
            return redirect()->back()
                ->with(['status' => false, 'message' => 'データの更新に失敗しました。入力値を確認してください。'])
                ->withErrors($validator->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            // カテゴリの更新
            if (is_null($coupon)) {
                $coupon = new Coupon;
            }

            $coupon->name = $request->get('name');
            $coupon->url = $request->get('url');
            $coupon->description = $request->get('description');
            $coupon->prefecture_id = $request->get('prefecture_id') ?? 13;
            $coupon->address1 = $request->get('address1') ?? "";
            $coupon->address2 = $request->get('address2') ?? "";
            $coupon->tel_num = $request->get('tel_num') ?? "";
            $coupon->post_code = $request->get('post_code') ?? "";
            $coupon->address_building = $request->get('address_building') ?? "";
            $coupon->city_id = $request->get('city_id') ?? 111;
            $coupon->lat = $request->get('lat') ?? 111;
            $coupon->lon = $request->get('lon') ?? 111;
            $coupon->deleted_at = null;
            $coupon->save();

            if ($request->hasFile('thumbnail_filename')) {
                if (!empty($image)) {
                    // imageの差し替え
                    $image->delete();
                }
                $image = new Image;
                $image->couponCoupon($request->thumbnail_filename);
            }
            if (!empty($image)) {
                $image->alt = $request->get('image_alt');
                $image->coupon_id = $coupon->id;
                $image->save();
            }
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('admin.coupons.edit')->with(['status' => false, 'message' => 'データの更新に失敗しました。システム管理者に問い合わせください。']);
        }
        DB::commit();

        return redirect()->route('admin.coupons')->with(['status' => true, 'message' => 'データの更新が完了しました']);
    }
}
