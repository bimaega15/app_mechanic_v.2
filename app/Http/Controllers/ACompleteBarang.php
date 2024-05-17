<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class ACompleteBarang extends Controller
{
    public function barang(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page');

        $getBarang = Barang::query()->groupBy('barcode_barang');

        if ($search != '') {
            $getBarang = $getBarang->where(function ($query) use ($search) {
                $query->where('barcode_barang', 'like', '%' . $search . '%')
                    ->orWhere('nama_barang', 'like', '%' . $search . '%');
            });
        }

        $getBarang = $getBarang->selectRaw('
        barcode_barang,
        MAX(nama_barang) as nama_barang,
        MAX(satuan_id) as satuan_id,
        MAX(deskripsi_barang) as deskripsi_barang,
        MAX(snornonsn_barang) as snornonsn_barang,
        MAX(stok_barang) as stok_barang,
        MAX(hargajual_barang) as hargajual_barang,
        MAX(lokasi_barang) as lokasi_barang,
        MAX(kategori_id) as kategori_id,
        MAX(status_barang) as status_barang,
        MAX(cabang_id) as cabang_id');

        $getBarang = $getBarang->paginate(10, ['*'], 'page', $page);

        $countFiltered = Barang::query()->groupBy('barcode_barang');
        if ($search != '') {
            $countFiltered = $countFiltered->where(function ($query) use ($search) {
                $query->where('barcode_barang', 'like', '%' . $search . '%')
                    ->orWhere('nama_barang', 'like', '%' . $search . '%');
            });
        }
        $countFiltered = $countFiltered->selectRaw('barcode_barang')->get()->count();

        return response()->json([
            'results' => $getBarang,
            'count_filtered' => $countFiltered,
        ]);
    }
}
