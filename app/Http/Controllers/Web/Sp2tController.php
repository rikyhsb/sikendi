<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\Models\Sp2t;
use App\Models\Sp2tDetail;
use App\Models\Sp2tRevisi;
use App\Models\Anggaran;
use App\Models\Level;
use App\Models\Kegiatan;
use App\Models\Pegawai;
use App\Models\Program;
use App\Models\Belanja;
use App\Libraries\Access;
use App\Libraries\Common;
use Closure;

class Sp2tController extends Controller
{
    protected $title = 'SP2T (Surat Perintah Pembayaran Transfer)';
    protected $link  = 'sp2t';
    protected $api   = 'api/sp2t';
    protected $route = 'sp2t';
    protected $access;
    protected $common;
    protected $_nip;

    public function __construct()
    {
        $this->middleware(
            function ($request, Closure $next) {
                if (Cookie::get('login') == true) {
                    $access = new Access();
                    $this->common = new Common();
                    $this->access = $access->generateAccess(Cookie::get('level'));
                    $this->_nip = Cookie::get('nip');
                    return $next($request);
                } else {
                    return redirect('login');
                }
            }
        );
    }

    public function index()
    {
        $breadcrumb = [];
        $breadcrumb[0] = '<a href="' . url('dashboard') . '"><i class="fa fa-dashboard"></i> Dashboard</a>';
        $breadcrumb[1] = '<i class="fa fa-database"></i> ' . $this->title;

        $data = [];
        $data['breadcrumb'] = $breadcrumb;
        $data['title']  = $this->title;
        $data['link'] = $this->link;
        $data['api'] = url($this->api);
        $data['route'] = url($this->route);
        $data['access'] = $this->access;
        return View::make('sp2t.index', $data);
    }

    public function create(Request $request)
    {
        $breadcrumb = [];
        $breadcrumb[0] = '<a href="' . url('dashboard') . '"><i class="fa fa-dashboard"></i> Dashboard</a>';
        $breadcrumb[1] = '<a href="' . url($this->route) . '"><i class="fa fa-database"></i> ' . $this->title . '</a>';
        $breadcrumb[2] = '<i class="fa fa-plus"></i> Tambah Data';

        if (Cookie::get('level') == 3) {
            $pegawai = Pegawai::where('nip', $this->_nip)->first();
            $bppkegiatan = Kegiatan::where('bendahara', $pegawai['id'])->get();
            $program_list = [];
            foreach ($bppkegiatan as $v) {
                $program_list[] = $v->program_id;
            }
            $program = Program::whereIn('id', array_unique($program_list))->get();
            $kegiatan = [];
            $belanja = [];
        } else {
            $program = Program::all();
            $kegiatan = [];
            $belanja = [];
        }

        $sp2t = Sp2t::find($request['sp2t']);

        $data = [];
        $data['title']  = $this->title;
        $data['link'] = $this->link;
        $data['breadcrumb'] = $breadcrumb;
        $data['api'] = url($this->api.'?sp2t='.$request['sp2t'].'&nip=' . $this->_nip);
        $data['act'] = 'create';
        $data['program'] = $program;
        $data['kegiatan'] = $kegiatan;
        $data['belanja'] = $belanja;
        $data['sp2t'] = $sp2t;
        $data['route'] = url($this->route . '/detail?id=' . $request['sp2t']);
        return View::make('sp2t.form', $data);
    }

    public function edit(Request $request)
    {
        $breadcrumb = [];
        $breadcrumb[0] = '<a href="' . url('dashboard') . '"><i class="fa fa-dashboard"></i> Dashboard</a>';
        $breadcrumb[1] = '<a href="' . url($this->route) . '"><i class="fa fa-database"></i> ' . $this->title . '</a>';
        $breadcrumb[2] = '<i class="fa fa-wrench"></i> Ubah Data';

        $sp2t = Sp2tDetail::find($request['id']);
        $sp2t_data = Sp2t::find($sp2t->sp2t_id);

        if (Cookie::get('level') == 3) {
            $pegawai = Pegawai::where('nip', $this->_nip)->first();
            $bppkegiatan = Kegiatan::where('bendahara', $pegawai['id'])->get();
            $program_list = [];
            $kegiatan_list = [];
            $belanja_list = [];
            foreach ($bppkegiatan as $v) {
                $program_list[] = $v->program_id;
            }
            foreach ($bppkegiatan as $v) {
                $kegiatan_list[] = $v->id;
            }
            foreach ($bppkegiatan as $v) {
                $kegiatan_list[] = $v->id;
            }
            $program = Program::whereIn('id', array_unique($program_list))->get();
            $kegiatan = Kegiatan::whereIn('program_id', array_unique($program_list))->get();
            $belanja = Belanja::whereIn('kegiatan_id', array_unique($kegiatan_list))->get();
        } else {
            $program = Program::all();
            $kegiatan = [];
            $belanja = [];
        }

        $data = [];
        $data['title']  = $this->title;
        $data['link'] = $this->link;
        $data['sp2t'] = $sp2t;
        $data['program'] = $program;
        $data['kegiatan'] = $kegiatan;
        $data['belanja'] = $belanja;
        $data['breadcrumb'] = $breadcrumb;
        $data['api'] = url($this->api . '?nip='.$this->_nip.'&sp2t='.$sp2t->sp2t_id.'&id=' . $sp2t->id);
        $data['act'] = 'edit';
        $data['sp2t'] = $sp2t;
        $data['sp2t_data'] = $sp2t_data;
        $data['route'] = url($this->route . '/detail?id=' . $sp2t->sp2t_id);
        return View::make('sp2t.form', $data);
    }

    public function detail(Request $request)
    {
        $breadcrumb = [];
        $breadcrumb[0] = '<a href="' . url('dashboard') . '"><i class="fa fa-dashboard"></i> Dashboard</a>';
        $breadcrumb[1] = '<a href="' . url($this->route) . '"><i class="fa fa-database"></i> ' . $this->title . '</a>';
        $breadcrumb[2] = '<i class="fa fa-wrench"></i> Detail';

        $sp2t = Sp2t::with('pegawai')->find($request['id']);
        $sp2tdetail = Sp2tDetail::where('sp2t_id', $sp2t->id)->with('kegiatan', 'program', 'belanja')->get();
        $sp2trevisi = Sp2tRevisi::where('sp2t_id', $sp2t->id)->get();

        $revisi   = ['kassubag' => [] , 'verifikatur' => []];
        $approval = ['kassubag' => $sp2t->approval_kassubag , 'verifikatur' => $sp2t->approval_verifikatur];

        $level = Level::find(Cookie::get('level'));

        if (count($sp2trevisi) > 0) {
            foreach ($sp2trevisi as $v) {
                if ($v->role == 'kassubag') {
                    array_push($revisi['kassubag'], $v);
                } elseif ($v->role == 'verifikatur') {
                    array_push($revisi['verifikatur'], $v);
                }
            }
        }

        $data = [];
        $data['title']  = $this->title;
        $data['link'] = $this->link;
        $data['sp2t'] = $sp2t;
        $data['sp2tdetail'] = $sp2tdetail;
        $data['revisi'] = $revisi;
        $data['approval'] = $approval;
        $data['role'] = strtolower($level->nama_level);
        $data['breadcrumb'] = $breadcrumb;
        $data['api'] = url($this->api . '?nip='.$this->_nip);
        $data['send_api'] = url($this->api . '/nominal/send?sp2t='.$sp2t->id.'&nip='.$this->_nip);
        $data['print_api'] = url($this->api . '/print?id='.$sp2t->id);
        $data['act'] = 'edit';
        $data['route'] = url($this->route);
        $data['access'] = $this->access;
        return View::make('sp2t.detail', $data);
    }
}
