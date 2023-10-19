<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\product;

class SaleController extends Controller
{
    public function buy(Request $request){
        $product_model = new product();
        $sale_model = new sale();

        $id = $request->input('product_id');
        $product = $product_model->getProductById($id);

        //商品なし
        if(!$product){
            return response()->json('商品がありません');
        }
        //在庫なし
        if($product->stock <= 0){
            return response()->json('在庫がありません');
        }

        try {
            DB::beginTransaction();
            //productsテーブルのstock減算
            $buy = $sale_model->decStock($id);
            //salesテーブルにインサート
            $sale_model->registSale($id);
            
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
        }

        //購入処理後の情報を返却
        return response()->json($buy);
        
    }
}
