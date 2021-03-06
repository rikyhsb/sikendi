<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Program;
use App\Models\Belanja;
use App\Models\Anggaran;
use App\Libraries\Access;
use App\Libraries\Common;
use Closure;

class AnggaranController extends Controller
{
    protected $title = 'Anggaran';
    protected $link  = 'anggaran';
    protected $api   = 'api/anggaran';
    protected $route = 'anggaran';
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

        $program = Program::all();
        $kegiatan = [];
        $belanja = [];

        $tahun = $this->common->generate_year();

        $data = [];
        $data['breadcrumb'] = $breadcrumb;
        $data['title']  = $this->title;
        $data['link'] = $this->link;
        $data['api'] = url($this->api);
        $data['route'] = url($this->route);
        $data['access'] = $this->access;
        $data['program'] = $program;
        $data['kegiatan'] = $kegiatan;
        $data['belanja'] = $belanja;
        $data['tahun'] = $tahun;
        return View::make('anggaran.index', $data);
    }

    public function create()
    {
        $breadcrumb = [];
        $breadcrumb[0] = '<a href="' . url('dashboard') . '"><i class="fa fa-dashboard"></i> Dashboard</a>';
        $breadcrumb[1] = '<a href="' . url($this->route) . '"><i class="fa fa-database"></i> ' . $this->title . '</a>';
        $breadcrumb[2] = '<i class="fa fa-plus"></i> Tambah Data';

        $program = Program::all();
        $kegiatan = [];
        $belanja = [];

        $tahun = $this->common->generate_year();

        $data = [];
        $data['title']  = $this->title;
        $data['link'] = $this->link;
        $data['breadcrumb'] = $breadcrumb;
        $data['api'] = url($this->api.'?nip=' . $this->_nip);
        $data['act'] = 'create';
        $data['program'] = $program;
        $data['kegiatan'] = $kegiatan;
        $data['belanja'] = $belanja;
        $data['tahun'] = $tahun;
        $data['route'] = url($this->route);
        return View::make('anggaran.form', $data);
    }

    public function edit(Request $request)
    {
        $breadcrumb = [];
        $breadcrumb[0] = '<a href="' . url('dashboard') . '"><i class="fa fa-dashboard"></i> Dashboard</a>';
        $breadcrumb[1] = '<a href="' . url($this->route) . '"><i class="fa fa-database"></i> ' . $this->title . '</a>';
        $breadcrumb[2] = '<i class="fa fa-wrench"></i> Ubah Data';

        $anggaran = Anggaran::find($request['id']);

        $program = Program::all();
        $kegiatan = Kegiatan::all();
        $belanja = Belanja::all();
        $tahun = $this->common->generate_year();

        $data = [];
        $data['title']  = $this->title;
        $data['link'] = $this->link;
        $data['anggaran'] = $anggaran;
        $data['breadcrumb'] = $breadcrumb;
        $data['api'] = url($this->api . '?nip='.$this->_nip.'&id=' . $anggaran->id);
        $data['act'] = 'edit';
        $data['program'] = $program;
        $data['kegiatan'] = $kegiatan;
        $data['belanja'] = $belanja;
        $data['tahun'] = $tahun;
        $data['route'] = url($this->route);
        return View::make('anggaran.form', $data);
    }
}
