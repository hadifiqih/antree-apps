<?php

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Events\SendGlobalNotification;
use App\Notifications\AntrianWorkshop;
use App\Http\Controllers\JobController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\OrderController;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\DesignController;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;

use App\Http\Controllers\ReportController;

use App\Http\Controllers\AntrianController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DocumentationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/download/{file}', function ($file) {
    return response()->download(storage_path('app/public/' . $file));
})->name('download');

Route::get('/dashboard', function () {
    return view('page.dashboard');
});

// Reset Password ------------------------------

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);

})->middleware('guest')->name('password.email');

Route::get('/notification/mark-as-read/{id}', function ($id) {

    $user = auth()->user()->id;
    $notification = auth()->user()->unreadNotifications->where('id', $id)->first();
    $notification->markAsRead();

    if($notification->data['link'] == '/design'){
        return redirect()->route('design.index');
    }else{
        return redirect()->route('antrian.index');
    }
})->middleware('auth')->name('notification.markAsRead');

Route::get('/notification/mark-all-as-read', function () {

    $user = auth()->user()->id;
    $notifications = auth()->user()->unreadNotifications;
    $notifications->markAsRead();

    return redirect()->back();
})->middleware('auth')->name('notification.markAllAsRead');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token, 'email' => request()->query('email')]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
                ? redirect()->route('auth.login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');

// End Reset Password ---------------------

Route::group(['middleware' => 'auth'], function () {
    //Menuju Antrian Controller (Admin)
    Route::get('/antrian', [AntrianController::class, 'index'])->name('antrian.index');
    Route::get('/reset-antrian/{tiket}', [AntrianController::class, 'resetAntrian'])->name('antrian.reset');
    Route::get('/design', [DesignController::class, 'index'])->name('design.index');
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
});

Route::group(['middleware' => 'checkrole:admin'], function () {
    //Menuju Design Controller (Admin)
    Route::get('/antrian/{id}/edit', [AntrianController::class, 'edit'])->name('antrian.edit');
    Route::put('/antrian/{id}', [AntrianController::class, 'update'])->name('antrian.update');
    Route::delete('/antrian/{id}', [AntrianController::class, 'destroy'])->name('antrian.destroy');
});

//membuat route group untuk AuthController
Route::controller(AuthController::class)->group(function(){
    Route::get('/login', 'index')->name('auth.index');
    Route::get('/register', 'create')->name('auth.register');
    Route::post('/login', 'login')->name('auth.login');
    Route::post('/register', 'store')->name('auth.store');
    Route::get('/logout', 'logout')->name('auth.logout');
    Route::get('/beams-generateToken', 'generateToken')->name('beams.auth');
});

Route::controller(ReportController::class)->group(function(){
    Route::get('/report-workshop', 'pilihTanggal')->name('laporan.workshop');
    Route::post('/report-workshop-pdf', 'exportLaporanWorkshopPDF')->name('laporan-workshop-pdf');
    Route::get('/cetak-espk/{id}', 'cetakEspk')->name('cetak-espk');
    Route::get('/sales/report', 'reportSales')->name('report.sales');
    Route::post('/sales/report', 'reportSalesByDate')->name('report.salesByDate');
    Route::get('/report-form-order/{id}', 'reportFormOrder')->name('report.formOrder');
    Route::get('/report/custom-report/', 'customReport')->name('report.custom');
    //Admin Keuangan
    Route::get('/antrian/omset-global-sales', 'omsetGlobalSales')->name('omset.globalSales');
    Route::get('/antrian/omset-percabang', 'omsetPerCabang')->name('omset.perCabang');
    Route::get('/antrian/omset-perproduk', 'omsetPerProduk')->name('omset.perProduk');

	Route::get('/export-excel/workshop', 'exportExcel')->name('exportExcel.workshop');
	Route::get('/export-excel/customer', 'customerExcel')->name('exportExcel.customer');
	Route::get('/export-excel/hasil-iklan', 'hasilIklanExcel')->name('exportExcel.hasilIklan');
	Route::get('/export-excel/organik', 'organik')->name('exportExcel.organik');
    Route::get('/penjualan-bulanan-export', 'penjualanBulananExport')->name('penjualanBulananExport');
});

Route::controller(DesignController::class)->group(function(){
    Route::post('/design/simpan-file-produksi', 'simpanFileProduksi')->name('simpanFileProduksi');
    Route::get('/design/download-file-produksi/{id}', 'downloadFileProduksi')->name('downloadFileProduksi');
});

Route::controller(EmployeeController::class)->group(function(){
    Route::get('/profile/{id}', 'show')->middleware('auth')->name('employee.show');
    Route::put('/profile/{id}', 'update')->middleware(['auth'])->name('employee.update');
    Route::post('/profile/upload-foto', 'uploadFoto')->middleware(['auth'])->name('employee.uploadFoto');
});

Route::controller(OrderController::class)->group(function(){
    Route::get('/order/create', 'create')->name('order.create');
    Route::post('/order', 'store')->name('order.store');
    Route::get('/order/{id}/edit', 'edit')->name('order.edit');
    Route::put('/order/{id}', 'update')->name('order.update');
    Route::delete('/order/{id}', 'destroy')->name('order.destroy');
    Route::get('/design', 'antrianDesain')->name('design.index');

    Route::get('/order/{id}/toAntrian', 'toAntrian')->middleware(['auth', 'checkrole:sales'])->name('order.toAntrian');
    Route::post('/order/tambahProdukByModal', 'tambahProdukByModal')->name('tambahProdukByModal');
    Route::get('/get-jobs-by-category/{category_id}', 'getJobsByCategory')->name('getJobsByCategory');
    Route::post('/order/set-desainer/', 'bagiDesain')->name('order.bagiDesain');
    //--------------------------------------------
    // Route File Desain FIX
    //--------------------------------------------
    Route::post('/order/upload-print-file', 'uploadPrintFile')->name('design.upload');
    Route::get('/design/submit-file-cetak/{id}', 'submitFileCetak')->name('submit.file-cetak');
    Route::post('/submit-link', 'submitLinkUpload')->name('submitLinkUpload');
    //--------------------------------------------
    // Route Revisi Desain
    //--------------------------------------------
    Route::get('/order/{id}/revisi-desain', 'revisiDesain')->name('order.revisiDesain');
    Route::put('/order/{id}/revisi-desain', 'updateRevisiDesain')->name('order.updateRevisiDesain');
    Route::post('/order/upload-revisi-desain', 'uploadRevisi')->name('uploadRevisi');
    Route::get('/order/{id}/submit-revisi-desain', 'submitRevisi')->name('submitRevisi');
    Route::post('/order/submit-revisi', 'submitLinkRevisi')->middleware('auth')->name('submitLinkRevisi');
    //--------------------------------------------
    // Route Reupload File
    //--------------------------------------------
    Route::post('/design/reupload-file', 'reuploadFileDesain')->name('design.reuploadFile');
    Route::get('/design/submit-reupload-file/{id}', 'submitReuploadFile')->name('submit.reupload');
    Route::post('/design/submit-reupload-link', 'submitLinkReupload')->name('submitLinkReupload');
});

Route::controller(AntrianController::class)->group(function(){
    Route::post('/antrian/storeToAntrian', 'store')->middleware('auth')->name('antrian.store');
    Route::post('/antrian/updateDeadline', 'updateDeadline')->middleware('auth')->name('antrian.updateDeadline');
    Route::get('/antrian/dokumentasi/{id}', 'showDokumentasi')->middleware('auth')->name('antrian.showDokumentasi');
    Route::post('/antrian/storeDokumentasi', 'storeDokumentasi')->middleware('auth')->name('antrian.storeDokumentasi');
    Route::get('/design/download/{id}', 'downloadPrintFile')->name('design.download');
    Route::post('/list-machines', 'getMachine')->name('antrian.getMachine');
    Route::get('/estimator/index', 'estimatorIndex')->middleware('auth')->name('estimator.index');
    Route::post('/estimator/filterByCategory', 'estimatorFilter')->middleware('auth')->name('estimator.filter');
    Route::get('/antrian/showProgress/{id}', 'showProgress')->middleware('auth')->name('antrian.showProgress');
    Route::post('/antrian/storeProgress', 'storeProgressProduksi')->middleware('auth')->name('store.progressProduksi');
    Route::get('/antrian/mark-aman/{id}', 'markAman')->middleware('auth')->name('antrian.markAman');
    Route::get('/antrian/download-produksi-file/{id}', 'downloadProduksiFile')->middleware('auth')->name('antrian.downloadProduksi');
    Route::get('/antrian/reminder', 'reminderProgress')->middleware('auth')->name('antrian.reminder');
    Route::get('/antrian/tandai-selesai/{id}', 'markSelesai')->middleware('auth')->name('antrian.markSelesai');
    Route::get('/antrian/estimator-produksi/{id}', 'estimatorProduksi')->middleware('auth')->name('antrian.estimator-produksi');
    Route::get('/search-by-ticket', 'searchByTicket')->middleware('auth')->name('antrian.searchByTicket');
    Route::get('/result/search-by-ticket', 'resultSearchByTicket')->middleware('auth')->name('antrian.resultSearchByTicket');
});

Route::controller(PaymentController::class)->group(function(){
    Route::post('/payment/pelunasan', 'updatePelunasan')->name('payment.pelunasan');
});

Route::controller(ProductController::class)->group(function(){
    Route::get('/product', 'index')->name('product.index');
    Route::get('/product/create', 'create')->name('product.create');
    Route::post('/product', 'store')->name('product.store');
    Route::get('/product/{id}/edit', 'edit')->name('product.edit');
    Route::put('/product/{id}', 'update')->name('product.update');
    Route::delete('/product/{id}', 'destroy')->name('product.destroy');
});

Route::controller(CustomerController::class)->group(function(){
    Route::get('/customer', 'index')->name('customer.index');
    Route::get('/customer/create', 'create')->name('customer.create');
    Route::post('/customer', 'store')->name('customer.store');
    Route::get('/customer/search', 'search')->name('pelanggan.search');
    Route::get('/customer/searchByNama', 'searchById')->name('pelanggan.searchById');
    Route::post('/customer/store', 'store')->name('pelanggan.store');
    Route::get('/getProvinsi', 'getProvinsi')->name('getProvinsi');
    Route::get('/getKota', 'getKota')->name('getKota');
});

Route::controller(JobController::class)->group(function(){
    Route::get('/job/search', 'search')->name('job.search');
    Route::get('/job/searchByNama', 'searchByNama')->name('job.searchByNama');
});

Route::controller(DocumentationController::class)->group(function(){
    Route::get('/documentation/{id}', 'previewDokumentasi')->name('documentation.preview');
});

Route::controller(UserController::class)->group(function(){
    Route::get('/user/superadmin', 'index')->middleware(['auth', 'checkrole:superadmin'])->name('user.index');
    Route::get('/user/create', 'create')->middleware(['auth', 'checkrole:superadmin'])->name('user.create');
    Route::get('/user/{id}/edit', 'edit')->middleware(['auth', 'checkrole:superadmin'])->name('user.edit');
    Route::put('/user/update/{id}', 'update')->middleware(['auth', 'checkrole:superadmin'])->name('user.update');
    Route::delete('/user/{id}', 'destroy')->middleware(['auth', 'checkrole:superadmin'])->name('user.destroy');
});

Route::get('/error', function () {
    //menampilkan halaman error dan error message
    if (session('error')) {
        $error = session('error');
        return view('error', compact('error'));
    }
})->name('error.page');
