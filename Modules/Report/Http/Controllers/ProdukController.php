<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Helpers\UtilsHelper;
use App\Models\Barang;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DataTables;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $dari_tanggal = $request->input('dari_tanggal');
            $sampai_tanggal = $request->input('sampai_tanggal');
            $barang_id = $request->input('barang_id');
            if ($barang_id == '-') {
                $barang_id = null;
            }

            $getBarang = new Barang();
            $dataBarang = $getBarang->getReportBarang()
                ->join('penjualan_product', 'penjualan_product.barang_id', '=', 'barang.id', 'left')
                ->join('order_barang', 'order_barang.barang_id', '=', 'barang.id', 'left')
                ->select(
                    'barang.*',
                    'penjualan_product.transaksi_penjualanproduct',
                    'order_barang.updated_at as tanggal_orderbarang',
                    DB::raw('SUM(penjualan_product.jumlah_penjualanproduct) as jumlah_penjualanproduct'),
                    DB::raw('SUM(order_barang.qty_orderbarang) as qty_orderbarang'),
                )
                ->groupBy('barang.id');

            $dataBarang = $dataBarang->where(function ($query) use ($dari_tanggal, $sampai_tanggal) {
                if ($dari_tanggal != null) {
                    $query->whereDate('penjualan_product.transaksi_penjualanproduct', '>=', $dari_tanggal);
                }
                if ($sampai_tanggal != null) {
                    $query->whereDate('penjualan_product.transaksi_penjualanproduct', '<=', $sampai_tanggal);
                }
                $query->orWhere('penjualan_product.transaksi_penjualanproduct', null);
            })->where(function ($query) use ($dari_tanggal, $sampai_tanggal) {
                if ($dari_tanggal != null) {
                    $query->whereDate('order_barang.updated_at', '>=', $dari_tanggal);
                }
                if ($sampai_tanggal != null) {
                    $query->whereDate('order_barang.updated_at', '<=', $sampai_tanggal);
                }
                $query->orWhere('order_barang.updated_at', null);
            });


            if ($barang_id != null) {
                $dataBarang = $dataBarang->where('barang.id', $barang_id);
            }
            $dataBarang = $dataBarang->get();
            $dataBarang = $dataBarang->map(function ($item) {
                $item->total_sum = $item->jumlah_penjualanproduct + $item->qty_orderbarang;
                return $item;
            });

            return DataTables::of($dataBarang)
                ->addColumn('transaksi_penjualanproduct', function ($row) {
                    return $row->transaksi_penjualanproduct == null ? $row->tanggal_orderbarang == null ? '-' : UtilsHelper::tanggalBulanTahunKonversi($row->tanggal_orderbarang) : UtilsHelper::tanggalBulanTahunKonversi($row->transaksi_penjualanproduct);
                })
                ->addColumn('total_sum', function ($row) {
                    return UtilsHelper::formatUang($row->total_sum);
                })
                ->rawColumns(['total_sum'])
                ->toJson();
        }

        $dari_tanggal = date('d/m/Y');
        $sampai_tanggal = date('d/m/Y');
        $data = [
            'dari_tanggal' => $dari_tanggal,
            'sampai_tanggal' => $sampai_tanggal,
        ];
        return view('report::produk.index', $data);
    }
}
