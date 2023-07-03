<?php

namespace App\Http\Controllers;

use TCPDF;
use App\Models\Gudang;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Models\SettingApp;
use Illuminate\Http\Request;
use App\Models\PembelianRetur;
use Carbon\Carbon;
use App\Models\PembelianDetail;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Models\PembelianReturDetail;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

class PembelianReturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            // Select columns from PembelianRetur table and join with Pembelian and Supplier tables
            $query = PembelianRetur::select('pembelian_retur.*', 'pembelian.tgl_invoice', 'pembelian.no_invoice', 'supplier.nama_supplier', 'supplier.email')
                ->leftJoin('pembelian', 'Pembelian_retur.id_pembelian', 'pembelian.id')
                ->leftJoin('supplier', 'pembelian.supplier_id', 'supplier.id');
            // dd($query->get());
            // Generate DataTables response
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_supplier', function ($item) {
                    return $item->nama_supplier;
                })
                ->addColumn('tgl_invoice', function ($item) {
                    $exp = explode(' ', $item->tgl_invoice);
                    return '<div class="text-end">' . ResponseFormatter::format_tanggal($exp[0]) . '</div>';
                })
                ->addColumn('tgl_nota_retur', function ($item) {
                    $exp = explode(' ', $item->tgl_nota_retur);
                    return '<div class="text-end">' . ResponseFormatter::format_tanggal($exp[0]) . '</div>';
                })
                ->addColumn('neto_retur', function ($item) {
                    return '<div class="text-end">' . ResponseFormatter::format_number($item->neto_retur) . '</div>';
                })
                ->addColumn('ignore_action', function ($item) {
                    return '
                    <div class="d-flex justify-content-start">
                        <a href="' . route('pembelian-retur.edit', $item->id) . '" class="btn btn-icon color-yellow mr-6" title="Edit" >
                            <i class="far fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-icon color-red mr-6 px-2" title="Delete" onclick="deleteData(`' . route('pembelian-retur.destroy', $item->id) . '`)">
                            <i class="far fa-trash-alt text-white"></i>
                        </button>
                    </div>
                    ';
                })
                ->addColumn('ignore_nota_retur', function ($item) {
                    $btn_kirim_email = ['url' => '/pembelian-retur/notaReturPdf?email=true&id=' . $item->id, 'label' => '', 'icon' => 'fas fa-paper-plane', 'attr' => ['target' => '_blank', 'class' => 'btn btn-icon color-lightblue px-2 me-1 kirim-email', 'data-bs-toggle' => 'tooltip', 'data-bs-title' => 'Kirim Nota Retur ke Email']];
                    if (!$item->email) {
                        $btn_kirim_email['attr']['disabled'] = 'disabled';
                        $btn_kirim_email['attr']['class'] = $btn_kirim_email['attr']['class'] . ' disabled';
                    }

                    return '<div class="btn-action-group">' .
                        ResponseFormatter::btn_link(['url' => '/pembelian-retur/notaReturPdf?ajax=true&id=' . $item->id, 'label' => '', 'icon' => 'fas fa-file-pdf', 'attr' => ['target' => '_blank', 'class' => 'btn btn-icon color-red text-white px-2 me-1 save-pdf', 'data-filename' => 'Nota Retur - ' . $item->no_nota_retur, 'data-bs-toggle' => 'tooltip', 'data-bs-title' => 'Download Nota Retur (PDF)']]) .
                        ResponseFormatter::btn_link($btn_kirim_email) .
                        '</div>';
                })
                ->rawColumns(['ignore_action', 'ignore_nota_retur'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('page.pembelian-retur.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pembelian_retur = new PembelianRetur;
        $pembelian_retur->tgl_nota_retur = date('Y-m-d');
        $barang = null;
        $gudang = Gudang::pluck('nama_gudang', 'id');
        return view('page.pembelian-retur.form', compact('pembelian_retur', 'barang',  'gudang'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $sub_total = 0;
        $total_diskon_item = 0;
        $total_qty = 0;
        $data_db_barang = [];

        foreach ($request->id_pembelian_detail as $key => $id_pembelian_detail) {
            $result = PembelianDetail::find($id_pembelian_detail);

            $harga_satuan = $result->harga_satuan;
            $qty = str_replace(['.'], '', $request->qty_barang_retur[$key]);
            $harga_barang_retur = $harga_satuan * $qty;

            // Calculate diskon_nilai and diskon_jenis
            $diskon_nilai = str_replace(['.'], '', $request->diskon_barang[$key]);
            $diskon_jenis = $request->diskon_barang_jenis[$key];
            $diskon_harga = 0;

            $is_numeric_diskon_nilai = is_numeric($diskon_nilai);
            $is_numeric_harga_barang_retur = is_numeric($harga_barang_retur);

            if ($is_numeric_diskon_nilai && $is_numeric_harga_barang_retur) {
                if ($diskon_nilai) {
                    $diskon_harga = $diskon_nilai;
                    if ($diskon_jenis == '%') {
                        $diskon_harga = round($harga_barang_retur * $diskon_nilai / 100);
                    }
                    $harga_barang_retur = $harga_barang_retur - $diskon_harga;
                    $total_diskon_item += $diskon_harga;
                }
            }

            $total_qty += $qty;
            $data_db_barang[$key] = [
                'id_pembelian_detail' => $id_pembelian_detail,
                'qty_retur' => $request->qty_barang_retur[$key],
                // 'harga_satuan' => $harga_satuan,
                'harga_total_retur' => $harga_satuan * $qty,
                'diskon_jenis_retur' => $diskon_jenis,
                'diskon_nilai_retur' => $diskon_nilai,
                'diskon_retur' => $diskon_harga,
                'harga_neto_retur' => $harga_barang_retur
            ];
            // Update sub_total
            $sub_total += $harga_barang_retur;
        }

        $data_db = [
            'id_pembelian' => $request->id_pembelian,
            'sub_total_retur' => $sub_total,
            'total_diskon_item_retur' => $total_diskon_item,
            'total_qty_retur' => $total_qty,
            'tgl_nota_retur' => Carbon::createFromFormat('d-m-Y', request('tgl_nota_retur'))->format('Y-m-d')
        ];
        // Calculate diskon_total_nilai and update sub_total
        $diskon_total_jenis = $request->diskon_total_jenis;
        $diskon_total_nilai = str_replace(['.'], '', $request->diskon_total_nilai);
        if (is_numeric($diskon_total_nilai)) {
            if ($diskon_total_jenis == '%') {
                $sub_total = $sub_total - round($sub_total * $diskon_total_nilai / 100);
            } else {
                $sub_total = $sub_total - $diskon_total_nilai;
            }
        }
        // Update data_db with diskon_jenis_transfer and diskon_nilai_transfer
        $data_db['diskon_jenis'] = $diskon_total_jenis;
        $data_db['diskon_nilai'] = $diskon_total_nilai != null ? $diskon_total_nilai : 0;
        // Calculate penyesuaian_transfer
        $operator = '';
        if ($request->penyesuaian_operator == '-') {
            $operator = '-';
        }
        $penyesuaian_nilai = str_replace('.', '', $request->penyesuaian_nilai);
        $data_db['penyesuaian'] = $operator . (is_numeric($penyesuaian_nilai) ? $penyesuaian_nilai : 0);
        // Calculate neto and update data_db
        $neto_retur = $sub_total + $data_db['penyesuaian'];
        if ($neto_retur < 0) {
            $neto_retur = 0;
        }
        $data_db['neto_retur'] = $neto_retur;

        if (!$request->id) {
            $lockTables = ['pembelian_retur WRITE', 'setting WRITE', 'sessions WRITE'];
            DB::transaction(function () use ($lockTables, &$data_db) {
                DB::statement('LOCK TABLES ' . implode(', ', $lockTables));
                $setting = SettingApp::where('type', 'nota_retur')->get()->keyBy('param');
                $no_nota_retur_pattern = $setting['no_nota_retur']['value'];
                $jml_digit = $setting['jml_digit']['value'];
                $maxNoSquence = PembelianRetur::where('tgl_nota_retur', 'LIKE', date('Y') . '%')->max('no_squence');
                $no_squence = $maxNoSquence ? $maxNoSquence + 1 : 1;
                $no_nota_retur = str_pad($no_squence, $jml_digit, '0', STR_PAD_LEFT);
                $no_nota_retur = str_replace('{{ nomor }}', $no_nota_retur, $no_nota_retur_pattern);
                $no_nota_retur = str_replace('{{ tahun }}', date('Y'), $no_nota_retur);
                $data_db['no_nota_retur'] = $no_nota_retur;
                $data_db['no_squence'] = $no_squence;
                // dd($data_db);

                $pembelian_retur = PembelianRetur::create($data_db);
                DB::statement('UNLOCK TABLES');
            });
        } else {
            $id_pembelian_retur = $request->id;
            $data_db['user_id_update'] = Auth::user()->id;
            $data_db['tgl_update'] = now();
            $pembelian_retur = PembelianRetur::find($id_pembelian_retur);
            $pembelian_retur->update($data_db);
        }

        // Update or create a new TransferBarang record
        if ($request->id) {
            $data_db['user_id_update'] = Auth::user()->id;
            $data_db['tgl_update'] = now();
            $pembelian_retur = PembelianRetur::findOrFail($request->id);
            $pembelian_retur->update($data_db);
            $pembelian_retur_id = $request->id;
        } else {
            $data_db['user_id_input'] = Auth::user()->id;
            $id = PembelianRetur::latest()->first()->id;
            // dd($id);
            $pembelian_retur = PembelianRetur::findOrFail($id);
            $pembelian_retur->update($data_db);
            $pembelian_retur_id = $pembelian_retur->id;
        }

        // dd($pembelian_retur_id);
        // Update data_db_barang with id_pembelian_retur
        foreach ($data_db_barang as &$val) {
            $val['id_pembelian_retur'] = $pembelian_retur_id;
        }

        // Delete and insert TransferBarangDetail records
        PembelianReturDetail::where('id_pembelian_retur', $request->id)->delete();
        PembelianReturDetail::insert($data_db_barang);
        // Return success response
        return ResponseFormatter::success([
            'data' => $pembelian_retur
        ], 'Success');
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

        $pembelian_retur = PembelianRetur::select('pembelian.*','supplier.nama_supplier','pembelian_retur.*')
        ->leftJoin('pembelian','pembelian.id','pembelian_retur.id_pembelian')
        ->leftJoin('supplier','supplier.id','pembelian.supplier_id')
        ->find($id);
        // dd($pembelian_retur);
        $barang = $pembelian_retur->getBarangByIdTransferBarang($id)->toArray();
        $gudang = Gudang::pluck('nama_gudang', 'id');


        return view('page.pembelian-retur.form', compact('pembelian_retur', 'barang', 'gudang'));
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
        $pembelian_retur = PembelianRetur::find($id);
        PembelianReturDetail::where('id_pembelian_retur',$pembelian_retur)->delete();
        $pembelian_retur->delete();

        return ResponseFormatter::success([
            'data' => null
        ], 'Deleted');
    }

    public function getDataDTListInvoice(Request $request)
    {
        return view('page.pembelian-retur.pembelian-retur-list-barang');
    }

    public function getDataInvoice(Request $request)
    {
        $query = Pembelian::select('pembelian.*', 'supplier.nama_supplier', 'supplier.alamat_supplier')
            ->leftJoin('supplier', 'pembelian.supplier_id', 'supplier.id');
        $data = $query->get();
        // dd($data);
        $id_pembelian = [];
        foreach ($data as $val) {
            $id_pembelian[] = $val->id;
        }
        // dd($id_pembelian);
        if ($id_pembelian) {
            $query = PembelianDetail::select('unit.satuan', 'barang.*', 'pembelian_detail.*')->leftJoin('barang', 'pembelian_detail.barang_id', 'barang.id')
                ->leftJoin('unit', 'barang.unit_id', 'unit.id')
                ->where('id_pembelian', join(',', $id_pembelian));
            $result = $query->get();
            $pembelian_detail = [];
            if ($result) {
                foreach ($result as $val) {
                    $pembelian_detail[$val->id_pembelian][] = $val;
                }

                foreach ($data as &$val) {
                    $val['detail'] = $pembelian_detail[$val->id];
                }
            }
        }
        // dd($data);
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('no_invoice', function ($item) {
                return '<span class="pembelian-detail">' . $item['no_invoice'] . '</span><span style="display:none" class="pembelian">' . json_encode($item) . '</span>';
            })
            ->addColumn('ignore_pilih', function ($item) {
                return  ResponseFormatter::btn_label(['label' => 'Pilih', 'attr' => ['data-id-pembelian' => $item['id'], 'class' => 'btn btn-success pilih-invoice btn-xs']]);
            })
            ->rawColumns(['ignore_pilih'])
            ->escapeColumns([])
            ->make(true);
    }



    public function notaReturPdf(Request $request)
    {
        // dd(asset('assets/img/logo_invoice.png'));
        $query = new PembelianRetur();
        $pembelian_retur = $query->getPembelianReturDetail($request->input('id'));

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setPageUnit('mm');

        // set document information
        $pdf->SetCreator('Koperasi.com');
        $pdf->SetAuthor('Koperasi.com');
        $pdf->SetTitle('Invoice #' . $pembelian_retur['data']['no_nota_retur']);
        $pdf->SetSubject('Nota Retur');

        $margin_left = 10; //mm
        $margin_right = 10; //mm
        $margin_top = 15; //mm
        $font_size = 10;

        $pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetProtection(array('modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble', 'print-high'), '', null, 0, null);

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', $font_size + 4, '', true);
        $pdf->SetMargins($margin_left, $margin_top, $margin_right, false);

        $pdf->AddPage();

        // $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0)));
        $pdf->SetTextColor(50, 50, 50);
        $pdf->Image(public_path('assets/img/logo_invoice.png'), 10, 20, 0, 0, 'PNG', 'https://koperasi.com');

        $image_dim = Image::make(public_path('assets/img/logo_invoice.png'));
        $x = $margin_left + ($image_dim->filesize() * 0.2645833333) + 5;
        $pdf->SetXY($x, $margin_top + 3);
        $pdf->Cell(0, 9, 'Koperasi.com', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
        $pdf->SetX($x);
        $pdf->SetFont('helvetica', '', $font_size, '', 'default', true);
        $pdf->Cell(0, 0, 'Jln. belum di input dinamis', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetX($x);
        $pdf->Cell(0, 0, 'Jln. belum di input dinamis', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetX($x);
        $pdf->Cell(0, 0, 'Jln. belum di input dinamis', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $barcode_style = array(
            'position' => 'R',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => $font_size,
            'stretchtext' => false
        );

        $pdf->SetY($margin_top + 10);
        $pdf->write1DBarcode($pembelian_retur['data']['no_nota_retur'], 'C128', '', '', '', 20, 0.4, $barcode_style, 'N');

        $pdf->ln(8);
        $pdf->SetFont('helvetica', 'B', $font_size + 10, '', 'default', true);
        $pdf->Cell(0, 0, 'NOTA RETUR', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        $pdf->ln(8);
        $pdf->SetFont('helvetica', 'B', $font_size, '', '', true);
        $pdf->Cell(0, 0, 'Penjual ', 0, 1);
        $pdf->ln(4);

        $pdf->SetFont('helvetica', '', $font_size, '', 'default', true);

        $y =  $pdf->GetY();
        $pdf->Cell(10, 0, 'Nama', 0, 1);
        $pdf->SetXY($margin_left + 13, $y);
        $pdf->Cell(10, 0, ':', 0, 1);
        $pdf->SetXY($margin_left + 15, $y);
        $pdf->Cell(10, 0, $pembelian_retur['supplier']['nama_supplier'], 0, 1);

        $y =  $pdf->GetY();
        $pdf->Cell(10, 0, 'Alamat', 0, 1);
        $pdf->SetXY($margin_left + 13, $y);
        $pdf->Cell(0, 0, ':', 0, 1);
        $pdf->SetXY($margin_left + 15, $y);
        $pdf->Cell(0, 0, $pembelian_retur['supplier']['alamat_supplier'], 0, 1);

        if (!empty($pembelian_retur['supplier']['nama_kecamatan'])) {
            $pdf->SetX($margin_left + 15);
            $pdf->Cell(0, 0, 'Kec. ' . $pembelian_retur['supplier']['nama_kecamatan'] . ', Kab. ' . $pembelian_retur['supplier']['nama_kabupaten'], 0, 1);
            $pdf->SetX($margin_left + 15);
            $pdf->Cell(0, 0, $pembelian_retur['supplier']['nama_propinsi'], 0, 1);
        }

        $pdf->ln(5);
        $pdf->SetFont('helvetica', 'B', $font_size, '', '', true);
        $y =  $pdf->GetY();
        $pdf->Cell(0, 0, 'Barang Yang Diretur', 0, 1);
        $pdf->SetFont('helvetica', '', $font_size, '', '', true);
        $pdf->SetY($y);

        $pdf->Cell(0, 0, ResponseFormatter::format_date($pembelian_retur['data']['tgl_nota_retur']), 0, 1, 'R', 0, '', 0, false, 'T', 'M');

        $pdf->ln(5);
        $pdf->SetFont('helvetica', '', $font_size, '', 'default', true);
        $border_color = '#CECECE';
        $background_color = '#efeff0';
        $tbl = <<<EOD
		<table border="0" cellspacing="0" cellpadding="6">
			<thead>
				<tr border="1" style="background-color:$background_color">
					<th style="width:5%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color;border-left-color:$border_color" align="center">No</th>
					<th style="width:35%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color;border-left-color:$border_color" align="center">Deskripsi</th>
					<th style="width:10%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color" align="center">Kuantitas Retur</th>
					<th style="width:10%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color" align="center">Harga Satuan</th>
					<th style="width:15%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color" align="center">Harga Total</th>
					<th style="width:10%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color" align="center">Diskon</th>
					<th style="width:15%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color" align="center">Harga Neto</th>
				</tr>
			</thead>
			<tbody>
		EOD;

        $no = 1;
        $format_number = 'Helper::format_number';
        foreach ($pembelian_retur['detail'] as $val) {
            $tbl .= <<<EOD
					<tr>
						<td style="width:5%;border-bottom-color:$border_color;border-right-color:$border_color;border-left-color:$border_color" align="center">$no</td>
						<td style="width:35%;border-bottom-color:$border_color;border-right-color:$border_color;border-left-color:$border_color">$val[nama_barang]</td>
						<th style="width:10%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color" align="right">{$format_number($val['qty_retur'])}</th>
						<th style="width:10%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color" align="right">{$format_number($val['harga_satuan'])}</th>
						<th style="width:15%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color" align="right">{$format_number($val['harga_total_retur'])}</th>
						<th style="width:10%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color" align="right">{$format_number($val['diskon_retur'])}</th>
						<th style="width:15%;border-top-color:$border_color;border-bottom-color:$border_color;border-right-color:$border_color" align="right">{$format_number($val['harga_neto_retur'])}</th>
					</tr>

		EOD;
            $no++;
        }

        $diskon = ResponseFormatter::format_number($pembelian_retur['data']['total_diskon_item_retur']);
        $total = ResponseFormatter::format_number($pembelian_retur['data']['neto_retur']);
        $sub_total = ResponseFormatter::format_number($pembelian_retur['data']['sub_total']);


        $tbl .= <<<EOD
				<tr style="background-color:$background_color">
					<td colspan="6" style="width:75%;border-bottom-color:$border_color;border-right-color:$border_color;border-left-color:$border_color">Subtotal</td>
					<td style="width:25%;border-bottom-color:$border_color;border-right-color:$border_color" align="right">$sub_total</td>
				</tr>
				<tr style="background-color:$background_color">
					<td colspan="6" style="width:75%;border-bottom-color:$border_color;border-right-color:$border_color;border-left-color:$border_color">Diskon</td>
					<td style="width:25%;border-bottom-color:$border_color;border-right-color:$border_color" align="right">$diskon</td>
				</tr>
				<tr style="background-color:$background_color">
					<td colspan="6" style="width:75%;border-bottom-color:$border_color;border-right-color:$border_color;border-left-color:$border_color">Total</td>
					<td style="width:25%;border-bottom-color:$border_color;border-right-color:$border_color" align="right">$total</td>
				</tr>
			</tbody>
		</table>
		EOD;

        $pdf->writeHTML($tbl, false, false, false, false, '');
        $pdf->ln(5);


        $pdf->ln(5);
        // $pdf->SetFont ('helvetica', '', $font_size, '', '', true );

        $pdf->SetY(-20);
        // $pdf->writeHTML('<hr style="background-color:#FFFFFF; border-bottom-color:#CCCCCC;height:0"/>', false, false, false, false, '');
        $pdf->writeHTML('<div style="background-color:#FFFFFF; border-bottom-color:#ababab;height:0"></div>', false, false, false, false, '');

        $pdf->ln(2);

        $pdf->SetFont('helvetica', 'I', $font_size, '', '', true);
        $pdf->SetTextColor(50, 50, 50);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 0, 'Terima kasih telah berbelanja ditempat kami. Kepuasan Anda adalah komitmen kami', 0, 1, 'L');
        $filename = 'Nota Retur - ' . str_replace(['/', '\\'], '_', $pembelian_retur['data']['no_nota_retur']) . '.pdf';
        $filepath_invoice = __DIR__ . '/assets/tmp/' . $filename;
        // dd($filepath_invoice);

        if ($request->input('ajax') == true) {
            $pdf->Output($filename, 'I');
            return response()->download($filepath_invoice, 'example.pdf')->deleteFileAfterSend(true);
        }
    }
}
