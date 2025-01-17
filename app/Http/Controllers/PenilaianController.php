<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(){
        $data = Penilaian::with('user')
                ->whereHas('user', function ($query) {
                    $query->where('role', 'karyawan');
                })
                ->get();
                $user = User::where('role','karyawan')->get();
        return view('admin.penilaian-karyawan',compact('data','user'));
    }

    public function submit(Request $request){
        $itung_prestasi_kerja = ($request->quality + $request->workload + $request->speed + $request->achievement) / 4;
        $prestasi_kerja = $itung_prestasi_kerja * 0.30;

        $kehadiran = $request->kehadiran * 0.30;
        $itung_kemampuan_kerja = ($request->planning + $request->flexibility + $request->inovation + $request->jobskill + $request->potency + $request->comprehensive_thingking) / 6;
        $kemampuan_kerja = $itung_kemampuan_kerja * 0.20;
        
        $itung_sikap_kerja = ($request->cooperative + $request->responsibility + $request->attitude + $request->execution + $request->moral_behavior) / 5;
        $sikap_kerja = $itung_sikap_kerja * 0.20;
        
        $total = $prestasi_kerja + $kehadiran + $sikap_kerja + $kemampuan_kerja;
        // dd($total);
        $getUser = User::find($request->user_id);
        
        // dd($nilai_akhir);
        Penilaian::create([
            'user_id'=>$request->user_id,
            'departemen_id'=>$getUser->departemen_id,
            'from_date'=>$request->from_date,
            'to_date'=>$request->to_date,
            'quality'=>$request->quality,
            'workload'=>$request->workload,
            'speed'=>$request->speed,
            'achievement'=>$request->achievement,
            'kehadiran'=>$request->kehadiran,
            'planning'=>$request->planning,
            'flexibility'=>$request->flexibility,
            'inovasion'=>$request->inovation,
            'jobskill'=>$request->jobskill,
            'potency'=>$request->potency,
            'comprehensive_thingking'=>$request->comprehensive_thingking,
            'coopertative'=>$request->cooperative,
            'responsibility'=>$request->responsibility,
            'attitude'=>$request->attitude,
            'execution'=>$request->execution,
            'moral_behavior'=>$request->moral_behavior,
            'nilai_akhir'=>$total,
        ]);

        return redirect('/admin/penilaian-karyawan')->with(['success'=>'Data berhasil Ditambah.']);
    }

    public function edit($id){
        $data = Penilaian::find($id);
        $user = User::where('role','karyawan')->get();
        return view('admin.edit-penilaian-karyawan',compact('data','user'));
    }

    public function update(Request $request, $id){
        $data = Penilaian::find($id);
        
        $itung_prestasi_kerja = ($request->quality + $request->workload + $request->speed + $request->achievement) / 4;
        $prestasi_kerja = $itung_prestasi_kerja * 0.30;

        $kehadiran = $request->kehadiran * 0.30;
        $itung_kemampuan_kerja = ($request->planning + $request->flexibility + $request->inovation + $request->jobskill + $request->potency + $request->comprehensive_thingking) / 6;
        $kemampuan_kerja = $itung_kemampuan_kerja * 0.20;
        
        $itung_sikap_kerja = ($request->cooperative + $request->responsibility + $request->attitude + $request->execution + $request->moral_behavior) / 5;
        $sikap_kerja = $itung_sikap_kerja * 0.20;
        
        $total = $prestasi_kerja + $kehadiran + $sikap_kerja + $kemampuan_kerja;
        $getUser = User::find($request->user_id);
        $data->update([
            'user_id'=>$request->user_id,
            'departemen_id'=>$getUser->departemen_id,
            'from_date'=>$request->from_date,
            'to_date'=>$request->to_date,
            'quality'=>$request->quality,
            'workload'=>$request->workload,
            'speed'=>$request->speed,
            'achievement'=>$request->achievement,
            'kehadiran'=>$request->kehadiran,
            'planning'=>$request->planning,
            'flexibility'=>$request->flexibility,
            'inovasion'=>$request->inovation,
            'jobskill'=>$request->jobskill,
            'potency'=>$request->potency,
            'comprehensive_thingking'=>$request->comprehensive_thingking,
            'coopertative'=>$request->cooperative,
            'responsibility'=>$request->responsibility,
            'attitude'=>$request->attitude,
            'execution'=>$request->execution,
            'moral_behavior'=>$request->moral_behavior,
            'nilai_akhir'=>$total,
        ]);
        return redirect('/admin/penilaian-karyawan')->with(['success'=>'Data berhasil Diupdate.']);
    }

    public function delete($id){
        $data = Penilaian::find($id);
        $data->delete();
        return redirect('/admin/penilaian-karyawan')->with(['success'=>'Data berhasil Dihapus.']);
    }

    public function laporan(){
        $data = Penilaian::with('user')
                ->whereHas('user', function ($query) {
                    $query->where('role', 'karyawan');
                })
                ->get();
        return view('admin.laporan',compact('data'));
    }

    public function download(Request $request){
        $filename = "laporan_penilaian_".date('Y-m-d').".xls";		 
      header("Content-Type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=\"$filename\"");

    $data = Penilaian::with('user.departemen')->whereDate('from_date', '>=', $request->from_date)
    ->whereDate('to_date', '<=', $request->to_date)
    ->get();
        foreach ($data as $key) {
            if ($key->nilai_akhir > 4.1) {
                $key->status_nilai = 'Sempurna';
            } elseif ($key->nilai_akhir > 3.1) {
                $key->status_nilai = 'Baik';
            } elseif ($key->nilai_akhir > 2.1) {
                $key->status_nilai = 'Cukup';
            } else {
                $key->status_nilai = 'Kurang';
            }
        }
        $from_date = date('d F', strtotime($request->from_date));
        $to_date = date('d F', strtotime($request->to_date));

        $current_year = date('Y');
        
        $dataHtml = '<table border="1">
          <tr>
            <td colspan="3">Perusahaan</td>
            <td colspan="5">: PT. Mada Wikri Tunggal</td>
        </tr>
        <tr>
            <td colspan="3">Periode / Tahun</td>
            <td colspan="5">: '.$from_date.' - '.$to_date.' / '.$current_year.'</td>
        </tr>
        <tr class="header">
           		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" rowspan=3 height="51" align="center" valign=middle bgcolor="#CCFFCC"><b>No</b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" rowspan=3 align="center" valign=middle bgcolor="#CCFFCC"><b>Nama</b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" rowspan=3 align="center" valign=middle bgcolor="#CCFFCC"><b>NIK</b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" rowspan=3 align="center" valign=middle bgcolor="#CCFFCC"><b>Jabatan</b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 3px double #000000" rowspan=3 align="center" valign=middle bgcolor="#CCFFCC"><b>Divisi/Departemen</b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" colspan=4 align="center" valign=middle bgcolor="#CCFFCC"><b>Prestasi Kerja</b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#CCFFCC"><b>Kedisiplinan Kerja</b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" colspan=6 align="center" valign=middle bgcolor="#CCFFCC"><b>Kemampuan Kerja</b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" colspan=5 align="center" valign=middle bgcolor="#CCFFCC"><b>Sikap Kerja</b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" rowspan=3 align="center" valign=middle bgcolor="#FF99CC"><b>Total Point</b></td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" rowspan=3 align="center" valign=middle bgcolor="#FF99CC"><b>Grade</b></td>
        	</tr>
	<tr>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" colspan=4 align="center" valign=middle bgcolor="#FFFF99">NiLAI Akhir (NA)</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99">NiLAI Akhir (NA)</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" colspan=6 align="center" valign=middle bgcolor="#FFFF99">NiLAI Akhir (NA)</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" colspan=5 align="center" valign=middle bgcolor="#FFFF99">NiLAI Akhir (NA)</td>
        
        </tr>
        <tr>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="1" sdnum="1033;">1</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="2" sdnum="1033;">2</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="3" sdnum="1033;">3</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="4" sdnum="1033;">4</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="5" sdnum="1033;">5</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="6" sdnum="1033;">6</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="7" sdnum="1033;">7</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="8" sdnum="1033;">8</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="9" sdnum="1033;">9</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="10" sdnum="1033;">10</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="11" sdnum="1033;">11</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 3px double #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="12" sdnum="1033;">12</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="13" sdnum="1033;">13</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="14" sdnum="1033;">14</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="15" sdnum="1033;">15</td>
		<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000" align="center" valign=middle bgcolor="#FFFF99" sdval="16" sdnum="1033;">16</td>

       
    ';
        $dataHtml .= '</tr>';
            if(!empty($data))
            $no = 1;

            foreach($data as $key => $item) {
                $dataHtml .= "<tr>
                    <td align=center>".$no++."</td>
                    <td align=center>".$item->user->name."</td>
                    <td align=center>".$item->user->nik."</td>
                    <td align=center>".$item->user->jabatan->jabatan."</td>
                    <td align=center>".$item->user->departemen->departemen."</td>
                    <td align=center>".$item->quality."</td>
                    <td align=center>".$item->workload."</td>
                    <td align=center>".$item->speed."</td>
                    <td align=center>".$item->achievement."</td>
                    <td align=center>".$item->kehadiran."</td>
                    <td align=center>".$item->planning."</td>
                    <td align=center>".$item->flexibility."</td>
                    <td align=center>".$item->inovasion."</td>
                    <td align=center>".$item->jobskill."</td>
                    <td align=center>".$item->potency."</td>
                    <td align=center>".$item->comprehensive_thingking."</td>
                    <td align=center>".$item->coopertative."</td>
                    <td align=center>".$item->responsibility."</td>
                    <td align=center>".$item->attitude."</td>
                    <td align=center>".$item->execution."</td>
                    <td align=center>".$item->moral_behavior."</td>
                    <td align=center>".$item->nilai_akhir."</td>
                    <td align=center>".$item->status_nilai."</td>";
                $dataHtml .= "</tr>";
            }
        $dataHtml .= '</table>';
        
        echo $dataHtml;
    }
}
