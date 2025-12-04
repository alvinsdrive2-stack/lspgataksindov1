<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class PendaftaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pendaftaran');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'admin' => 'required',
                'wilayah' => 'required',
                'pengusung' => 'required',
                'provinsi' => 'nullable',
                'nama_instansi' => 'nullable',
                'email_instansi' => 'nullable|email',
                'email' => 'required|email',
                'referensi' => 'nullable',
                'tuk' => 'required',
                'alamat' => 'required',
                'pengaju' => 'required',
                'telepon_pengaju' => 'required',
                'telepon_admin' => 'required',
                'tanggal_uji' => 'required|date',
                'asesi' => 'required|integer',
                'jenis_tuk' => 'required',
                'dokumen_pengajuan' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'dokumen_perjanjian' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'dokumentasi' => 'required|file|mimes:pdf,doc,docx|max:2048',
            ]);
        } catch(\Exception $e) {
            dd("error", $e);
        }

        $dokumenPengajuan = $request->file('dokumen_pengajuan');
        $pengajuan_url_name = (string) Uuid::uuid4();
        if ($dokumenPengajuan) {
            Storage::disk("public")->put("dok_asesmen/foto/" . $pengajuan_url_name . '.' . $dokumenPengajuan->getClientOriginalExtension(), file_get_contents($dokumenPengajuan->getRealPath()));
            $full_pengajuan_url_name = Storage::disk('public')->url("dok_asesmen/foto/" . $pengajuan_url_name . '.' . $dokumenPengajuan->getClientOriginalExtension());
        } else {
            $full_pengajuan_url_name = null;
        }

        $dokumenPerjanjian = $request->file('dokumen_perjanjian');
        $perjanjian_url_name = (string) Uuid::uuid4();
        if ($dokumenPerjanjian) {
            Storage::disk("public")->put("dok_asesmen/foto/" . $perjanjian_url_name . '.' . $dokumenPerjanjian->getClientOriginalExtension(), file_get_contents($dokumenPerjanjian->getRealPath()));
            $full_perjanjian_url_name = Storage::disk('public')->url("dok_asesmen/foto/" . $perjanjian_url_name . '.' . $dokumenPerjanjian->getClientOriginalExtension());
        } else {
            $full_perjanjian_url_name = null;
        }

        $dokumentasi = $request->file('dokumentasi');
        $dokumentasi_url_name = (string) Uuid::uuid4();
        if ($dokumentasi) {
            Storage::disk("public")->put("dok_asesmen/foto/" . $dokumentasi_url_name . '.' . $dokumentasi->getClientOriginalExtension(), file_get_contents($dokumentasi->getRealPath()));
            $full_dokumentasi_url_name = Storage::disk('public')->url("dok_asesmen/foto/" . $dokumentasi_url_name . '.' . $dokumentasi->getClientOriginalExtension());
        } else {
            $full_dokumentasi_url_name = null;
        }

        // 📝 Save data to DB
        Pendaftaran::create([
            'wilayah' => $validated['wilayah'],
            'pengusung' => $validated['pengusung'],
            'provinsi' => $validated['provinsi'],
            'instansi' => $validated['nama_instansi'],
            'email_instansi' => $validated['email_instansi'],
            'email' => $validated['email'],
            'referensi' => $validated['referensi'] ?? null,
            'tuk' => $validated['tuk'],
            'alamat' => $validated['alamat'],
            'pengaju' => $validated['pengaju'],
            'telp_pengaju' => $validated['telepon_pengaju'],
            'admin' => $validated['admin'],
            'telp_admin' => $validated['telepon_admin'],
            'tanggal_uji' => $validated['tanggal_uji'],
            'jumlah_asesi' => $validated['asesi'],
            'jenis_tuk' => $validated['jenis_tuk'],
            'dokumen_pengajuan' => $full_pengajuan_url_name,
            'dokumen_perjanjian' => $full_perjanjian_url_name,
            'dokumentasi_foto' => $full_dokumentasi_url_name,
        ]);

        return back()->with("success", "Pengajuan berhasil!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Pendaftaran $pendaftaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pendaftaran $pendaftaran)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pendaftaran $pendaftaran)
    {
        //
    }
}
