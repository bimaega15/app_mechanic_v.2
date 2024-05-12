<?php

namespace Modules\TransferStock\Http\Controllers;

use App\Http\Helpers\UtilsHelper;
use App\Models\Barang;
use App\Models\TransferStock;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class StockMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public $datastatis;
    public function __construct()
    {
        $this->datastatis = Config::get('datastatis');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $transferMasuk = new TransferStock();
            $data = $transferMasuk->getTransferStock()
                ->orWhere('cabang_id_penerima', session()->get('cabang_id'));

            return DataTables::eloquent($data)
                ->addColumn('created_at', function ($row) {
                    return UtilsHelper::tanggalBulanTahunKonversi($row->created_at);
                })
                ->addColumn('action', function ($row) {
                    $buttonDetail = '
                    <a class="btn btn-info btn-detail btn-sm" 
                    data-typemodal="extraLargeModal"
                    data-urlcreate="' . url('transferStock/masuk/' . $row->id) . '"
                    data-modalId="extraLargeModal"
                    >
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    ';

                    $button = '
                <div class="text-center">
                    ' . $buttonDetail . '
                </div>
                ';
                    return $button;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('transferstock::stokMasuk.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('stokMasuk::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $transferMasuk = new TransferStock();
        $row = $transferMasuk->getTransferStock()
            ->orWhere('cabang_id_penerima', session()->get('cabang_id'))
            ->find($id);
        $status_tstock = $this->datastatis['status_tstock'];
        $array_status_tstock = [];
        $status_tstock_data = [];
        foreach ($status_tstock as $key => $item) {
            $array_status_tstock[] = [
                'id' => $key,
                'label' => $item
            ];
            $status_tstock_data[] = strtolower($item);
        }
        return view('transferstock::stokMasuk.detail', compact('row', 'array_status_tstock', 'status_tstock_data'));
    }

    public function updateStatus(Request $request, $id)
    {
        $status_tstock = $request->input('status_tstock');
        $transferMasuk = new TransferStock();
        $row = $transferMasuk->getTransferStock()
            ->orWhere('cabang_id_penerima', session()->get('cabang_id'))
            ->find($id);

        if ($status_tstock == 'diterima') {
            // memindahkan stock
            $cabang_id_awal = $row->cabang_id_awal;
            $cabang_id_penerima = $row->cabang_id_penerima;
            foreach ($row->transferDetail as $key => $item) {
                $barang_id = $item->barang_id;
                $qty = $item->qty_tdetail;

                $getBarang = Barang::where('id', $barang_id)->first();
                $getBarangCabangPenerima = Barang::where('barcode_barang', $getBarang->barcode_barang)
                    ->where('cabang_id', $cabang_id_penerima)
                    ->first();

                if ($getBarangCabangPenerima) {
                    $getBarangCabangPenerima->update([
                        'stok_barang' => $getBarangCabangPenerima->stok_barang + $qty,
                    ]);
                }

                $getBarangCabangPemberi = Barang::where('barcode_barang', $getBarang->barcode_barang)
                    ->where('cabang_id', $cabang_id_awal)
                    ->first();

                if ($getBarangCabangPemberi) {
                    $getBarangCabangPemberi->update([
                        'stok_barang' => $getBarangCabangPemberi->stok_barang - $qty,
                    ]);
                }
            }
        }
        $transferMasuk = TransferStock::find($id);
        $transferMasuk->status_tstock = $status_tstock;
        $transferMasuk->tanggalditerima_tstock = date('Y-m-d H:i:s');
        $transferMasuk->users_id_diterima = Auth::id();
        $transferMasuk->save();

        return response()->json('Status berhasil diubah');
    }

    public function print($id)
    {
        $transferKeluar = new TransferStock();
        $row = $transferKeluar->getTransferStock()
            ->orWhere('cabang_id_penerima', session()->get('cabang_id'))
            ->find($id);

        return view('transferstock::stokKeluar.print', compact('row'));
    }
}
