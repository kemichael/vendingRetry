<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class product extends Model
{
    use HasFactory;
    
    //検索処理
    public function searchList($request){
        //受け取ったform内のname="keyword"とnome="search-company"を変数に詰める。
        $keyword = $request->input('keyword');
        $searchCompany = $request->input('search-company');
        $min_price = $request->input('min_price');
        $max_price = $request->input('max_price');
        $min_stock = $request->input('min_stock');
        $max_stock = $request->input('max_stock');

        //productsテーブルとcompaniesテーブルをjoin
        $query = DB::table('products')
                    ->join('companies', 'products.company_id', '=', 'companies.id')
                    ->select('products.*', 'companies.company_name');

        //キーワードでwhere。
        //$keywordの箇所はダブルクォーテーションじゃないとうまく展開できないよ！
        if($keyword) {
            $query->where('products.product_name', 'like', "%{$keyword}%");
        }
        //企業idでwhere
        if($searchCompany) {
            $query->where('products.company_id', '=', "$searchCompany");
        }

        //価格下限
        if($min_price) {
            $query->where('products.price', '>=', $min_price);
        }
        //価格上限
        if($max_price) {
            $query->where('products.price', '<=', $max_price);
        }
        //在庫下限
        if($min_stock) {
            $query->where('products.price', '>=', $min_stock);
        }
        //在庫上限
        if($max_stock) {
            $query->where('products.price', '<=', $max_stock);
        }

        //条件に合うデータを全件取得。$productsに詰める
        $products = $query->get();

        return $products;
    }


    //idによる商品検索
    public function getProductById($id){
        $products=DB::table('products')->join('companies', 'products.company_id', '=', 'companies.id')
            ->select('products.*', 'companies.company_name')
            ->where('products.id', '=', $id)
            ->first();

        return $products;
    }

    // 新規登録
    public function registSubmit($request, $img_path){
        DB::table('products')->insert([
            'product_name'=> $request->input('product_name'),
            'company_id' => $request->input('company_id'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
            'comment' => $request->input('comment'),
            'img_path' => $img_path
        ]);
    }

    //更新処理（画像あり）
    public function registEdit($request, $img_path, $id){
        DB::table('products')
        ->where('products.id', '=', $id)
        ->update([
            'product_name'=> $request->input('product_name'),
            'company_id' => $request->input('company_id'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
            'comment' => $request->input('comment'),
            'img_path' => $img_path
        ]);
    }

    //更新処理（画像なし）
    public function registEditNoImg($request, $id){
        DB::table('products')
        ->where('products.id', '=', $id)
        ->update([
            'product_name'=> $request->input('product_name'),
            'company_id' => $request->input('company_id'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
            'comment' => $request->input('comment'),
        ]);
    }

    //削除処理
    public function destroyProduct($id) {
        $products = DB::table('products')
            ->where('products.id', '=', $id) ->delete();
    }
    
}
