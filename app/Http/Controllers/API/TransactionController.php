<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
     // Function transaction
   public function all(Request $request){
    $id = $request->input('id');
    $limit = $request->input('limit', 6);
    $status = $request->input('status');

    // Mengecek transaksi berdasarkan id
    if($id){
        $transaction = Transaction::with(['items.product'])->find($id);

        // Jika transaksi ada, maka akan mengembalikan pesan berhasil
        if($transaction){
            return ResponseFormatter::success(
                $transaction, 'Data transaksi berhasil diambil'
            );
         // Jika transaksi tidak ada, maka akan mengembalikan pesan error
        }else{
            return ResponseFormatter::error(
                null, 'Data transaksi tidak ada', 404
            );
        }
    }

    $transaction = Transaction::with(['items.product'])->where('users_id', Auth::user()->id);

    if($status){
        $transaction->where('status', $status);
    }

    return ResponseFormatter::success(
        $transaction->paginate($limit), 'Data list transaksi berhasil diambil'
    );
}

// Function checkout
public function checkout(Request $request){

 // Validasi data yang diinput
    $request->validate([
        'items' => 'required|array',
        'items.*.id' => 'exists:products,id', // Mengecek setiap item yang dicheckout berdasarkan id di tabel products
        'total_price' => 'required',
        'shipping_price' => 'required',
        'status' => 'required|in:PENDING,SUCCESS,CANCELED,FAILED,SHIPPING,SHIPPED'
    ]);

    // Membuat variabel $transaction yang menyimpan semua data yang diinput
    $transaction = Transaction::create([
        'users_id' => Auth::user()->id,
        'address' => $request->address,
        'total_price' => $request->total_price,
        'shipping_price' => $request->shipping_price,
        'status' => $request->status,
    ]);

    // Membuat looping array dikarenakan checkout product dapat lebih dari 1 dan disimpan dalam bentuk array
    foreach($request->items as $product){
        TransactionItem::create([
             'users_id' => Auth::user()->id,
             'products_id' => $product['id'],
             'transactions_id' => $transaction->id,
             'quantity' => $product['quantity']
        ]);
    }

    // Mengembalikan nilai ke relasi items.product lalu memberikan pesan
    return ResponseFormatter::success($transaction->load('items.product'), 'Transaksi berhasil');
}
}
