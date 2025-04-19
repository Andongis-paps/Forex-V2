<?php

use Illuminate\Support\Facades\Route;

// Controller imports

// Branch Controllers
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\RegisterController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\BufferController;
use App\Http\Controllers\Web\BuyingTransactController;
use App\Http\Controllers\Web\SellingTransactController;
use App\Http\Controllers\Web\SerialsController;
use App\Http\Controllers\Web\RateReportsController;
use App\Http\Controllers\Web\SoldSerialsReportController;
use App\Http\Controllers\Web\UtilitiesController;
use App\Http\Controllers\Web\TransferForexController;
use App\Http\Controllers\Web\SessionController;
use App\Http\Controllers\Web\SocketIoController;
use App\Http\Controllers\Web\CSSReportController;
use App\Http\Controllers\RsetController;

// Window Based Controllers
use App\Http\Controllers\Window\BranchMaintenanceController;
use App\Http\Controllers\Window\CurrencyMaintenanceController;
use App\Http\Controllers\Window\ExpenseMaintenanceController;
use App\Http\Controllers\Window\RateMaintenanceController;
use App\Http\Controllers\Window\RateConfigurationController;
use App\Http\Controllers\Window\DenominationMaintenanceController;
use App\Http\Controllers\Window\TagsMaintenanceController;
use App\Http\Controllers\Window\ConfigurationController;
use App\Http\Controllers\Window\StopBuyingMaintenanceController;
use App\Http\Controllers\Window\CurrencyManualMaintenanceController;
use App\Http\Controllers\Window\CompanyLimitMaintenanceController;
use App\Http\Controllers\Window\RsetSeriesMaintenanceController;
// Admin/Trader
use App\Http\Controllers\Web\AdminBillTaggingController;
use App\Http\Controllers\Web\AdminSellingTransactController;
use App\Http\Controllers\Web\AdminReceiveTransfersController;
use App\Http\Controllers\Web\AdminCurrencyStockController;
use App\Http\Controllers\Web\AdminDPOFXController;
use App\Http\Controllers\Web\AdminDPOFXtockController;
use App\Http\Controllers\Window\FCFSeriesMaintenanceController;
use App\Http\Controllers\Web\AdminOnholdController;
use App\Http\Controllers\Window\TransactTypeMaintenanceController;
use App\Http\Controllers\Window\BulkLimitControllerMaintenance;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AdminBuyingTransactionController;
use App\Http\Controllers\Web\AdminSerialsController;
use App\Http\Controllers\Web\AdminRetailSellingController;
use App\Http\Controllers\Web\AdminTransCapController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\GlobalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[LoginController::class, 'login'])->name('login');

Route::post('authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
Route::get('resetattemp', [LoginController::class, 'resetattemp'])->name('resetattemp');

Route::group(['middleware' => ['auth', 'PreventBackHistory']], function () {
    Route::post('/rset', RsetController::class)->name('rset');
    Route::fallback(function () {return view('template.404');});
    Route::get('/test_socket_io', function () {return view('test_socket_io');});
    Route::post('save', [SocketIoController::class, 'save'])->name('save_socket');
    Route::post('changeBranch', [LoginController::class, 'changeBranch'])->name('change_branch');
    Route::post('/reportErrorShow', [CSSReportController::class, 'reportErrorShow'])->name('report_error');
    Route::post('/sendReportError', [CSSReportController::class, 'sendReportError'])->name('send_report_error');
    // Session Controller
    Route::get('userInfo', [SessionController::class, 'userInfo'])->name('user_info');
    Route::post('/updateTimeToggleSession', [SessionController::class, 'updateTimeToggleSession'])->name('updatetimetogglesession');
    Route::post('searchCustomer', [SessionController::class, 'searchCustomer'])->name('search_customer');
    Route::post('testAutoRunSchedulers', [SessionController::class, 'testAutoRunSchedulers'])->name('test_auto_run_scheds');
    Route::post('/rateUpdates', [GlobalController::class, 'rateUpdates'])->name('rate_updates');

    // Route::get('/testlang', function () {
    //     $api = Http::withoutVerifying()->get("http:/192.168.88.115:7191/api/jobs", [
    //         'search'    => null,
    //         'per_page'  => 12,
    //         'page'      => 1,
    //     ])->json();
     
    //     dd($api);
    // });

    // Route::get('/symlink', function () {
    //     Artisan::call('storage:link');
    // });

    // Unused routes group =================================================================================================================================================
        // Register / Create Account
        Route::get('register', [RegisterController::class, 'register']);
        Route::post('storeAccount', [RegisterController::class, 'storeAccount']);
        // Login / Authenticate
        // logout
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        // Utilities
        // Route::get('userLevelsDash', [LoginController::class, 'userLevelsDash']);
        Route::get('userLevels', [UtilitiesController::class, 'userLevels']);
        Route::get('branchBuffer', [UtilitiesController::class, 'branchBuffer'])->name('branchBuffer');
        Route::get('toggleOrderBy', [UtilitiesController::class, 'toggleOrderBy'])->name('toggleOrderBy');
        // 403
        // Unused Routes (For buying)
        // Route::post('serialsStock', [SellingTransactController::class, 'serialsStock']);
        // Route::get('editSellingTrans/{id}', [SellingTransactController::class, 'editSellingTrans'])->name('editsellingtransact');
        // Route::post('updateSellingTrans', [SellingTransactController::class, 'updateSellingTrans']);
        // Route::get('deleteSellingTrans', [SellingTransactController::class, 'deleteSellingTrans'])->name('delete_selling_transact');
        // Unused Routes (For selling)
        // Route::get('editBuyingTrans/{id}', [BuyingTransactController::class, 'editBuyingTrans'])->name('editbuyingtransact');
        // Route::post('updateBuyingTrans', [BuyingTransactController::class, 'updateBuyingTrans']);
        // Route::get('deleteBuyingTrans', [BuyingTransactController::class, 'deleteBuyingTrans'])->name('delete_buying_transact');
        // Reports ====================================================================================================================================================================
        // Reports - Rate Reports
        Route::get('rateReports' , [RateReportsController::class, 'rateReports']);
        Route::post('searchRateReports' , [RateReportsController::class, 'searchRateReports'])->name('searchratereports');
        // Reports - Sold Serials Reports
        Route::get('soldSerialsReports', [SoldSerialsReportController::class, 'soldSerialsReports']);
        Route::post('searchSoldSerialsReports' , [SoldSerialsReportController::class, 'searchSoldSerialsReports'])->name('searchsoldserialsreports');
    // Closing unused routes group =================================================================================================================================================

    // Routes for Branch Modules =================================================================================================================================================
    Route::prefix('branch_transactions')->middleware(['BranchInitializeSetup'])->group(function() {
        Route::prefix('notifications')->group(function() {
            Route::get('/', [NotificationController::class, 'notifications'])->name('notifications');
            Route::get('/show', [NotificationController::class, 'show'])->name('notif.show');
            Route::post('/acknowledge', [NotificationController::class, 'acknowledge'])->name('notif.acknowledge');
        });

        // Dashboard
        Route::prefix('dashboard')->group(function() {
            Route::get('/', [DashboardController::class, 'branchdDashboard'])->name('branch_transactions.dashboard');
            Route::post('buyingSalesBreakdown', [DashboardController::class, 'buyingSalesBreakdown'])->name('branch_transactions.dashbooard.b_sales_breakd_down');
            Route::post('/stocks', [DashboardController::class, 'stocks'])->name('branch_transactions.dashbooard.stocks');
            // Route::post('sendEmail', [DashboardController::class, 'sendEmail'])->name('dashbooard.send_email');
            // Route::get('userData', [DashboardController::class, 'userData'])->name('dashbooard.user_data');
        });
        // Buying Transaction
        Route::prefix('buying_transaction')->group(function() {
            Route::get('/', [BuyingTransactController::class, 'show'])->name('branch_transactions.buying_transaction');
            Route::get('/add', [BuyingTransactController::class, 'add'])->name('branch_transactions.buying_transaction.add');
            Route::post('/save', [BuyingTransactController::class, 'save'])->name('branch_transactions.buying_transaction.save');
            Route::get('edit/{id}', [BuyingTransactController::class, 'edit'])->name('branch_transactions.buying_transaction.edit');
            Route::post('/update', [BuyingTransactController::class, 'update'])->name('branch_transactions.buying_transaction.update');
            Route::post('/update-rate', [BuyingTransactController::class, 'updateRate'])->name('branch_transactions.buying_transaction.update_rate');
            Route::get('/delete', [BuyingTransactController::class, 'delete'])->name('branch_transactions.buying_transaction.delete');
            Route::get('/search', [BuyingTransactController::class, 'search'])->name('branch_transactions.buying_transaction.search');
            Route::post('/denominations', [BuyingTransactController::class, 'denominations'])->name('branch_transactions.buying_transaction.denominations');
            Route::post('/currencies', [BuyingTransactController::class, 'currencies'])->name('branch_transactions.buying_transaction.currencies');
            Route::post('/latestEntry', [BuyingTransactController::class, 'latestEntry'])->name('branch_transactions.buying_transaction.latest_rate');
            Route::post('/orNumbDuplicateBuying', [BuyingTransactController::class, 'orNumbDuplicateBuying'])->name('branch_transactions.buying_transaction.or_number_duplicate_b');
            Route::post('/mtcnDuplicateBuying', [BuyingTransactController::class, 'mtcnDuplicateBuying'])->name('branch_transactions.buying_transaction.mtcn_duplicate');
            Route::post('/printCountBuying', [BuyingTransactController::class, 'printCountBuying'])->name('branch_transactions.buying_transaction.print_count_buying');
            Route::get('/details/{id}', [BuyingTransactController::class, 'details'])->name('branch_transactions.buying_transaction.details');
            Route::get('/pendingSerials/{id}', [BuyingTransactController::class, 'pendingSerials'])->name('branch_transactions.buying_transaction.pending_serials');
            Route::post('/addSerials', [BuyingTransactController::class, 'addSerials'])->name('branch_transactions.buying_transaction.add_serials');
            Route::post('/scDetails', [BuyingTransactController::class, 'scDetails'])->name('branch_transactions.buying_transaction.sc_details');
        });
        // Selling Transaction
        Route::prefix('selling_transaction')->group(function() {
            Route::get('/', [SellingTransactController::class, 'show'])->name('branch_transactions.selling_transaction');
            Route::get('/add', [SellingTransactController::class, 'add'])->name('branch_transactions.selling_transaction.add');
            Route::post('/save', [SellingTransactController::class, 'save'])->name('branch_transactions.selling_transaction.save');
            Route::post('/availableCurrency', [SellingTransactController::class, 'availableCurrency'])->name('branch_transactions.selling_transaction.available_curr');
            Route::post('/orNumbDuplicateSelling', [SellingTransactController::class, 'orNumbDuplicateSelling'])->name('branch_transactions.selling_transaction.or_number_duplicate_s');
            Route::get('/edit/{id}', [SellingTransactController::class, 'edit'])->name('branch_transactions.selling_transaction.edit');
            Route::post('/update', [SellingTransactController::class, 'update'])->name('branch_transactions.selling_transaction.update');
            Route::get('/details/{id}', [SellingTransactController::class, 'details'])->name('branch_transactions.selling_transaction.details');
            Route::post('/serialDetails', [SellingTransactController::class, 'serialDetails'])->name('branch_transactions.selling_transaction.serial_detials');
            Route::get('/delete', [SellingTransactController::class, 'delete'])->name('branch_transactions.selling_transaction.delete');
            Route::post('/printCountSelling', [SellingTransactController::class, 'printCountSelling'])->name('branch_transactions.selling_transaction.print');
            Route::post('/scDetails', [SellingTransactController::class, 'scDetails'])->name('branch_transactions.selling_transaction.sc_details');
            // Search Serials - Selling transaction
            // Route::post('searchSerials', [SellingTransactController::class, 'searchSerials'])->name('search_serials');
            // Route::post('serialsStock', [SellingTransactController::class, 'serialsStock']);
        });
        // Pending serials 
        Route::prefix('pending_serials')->group(function() {
            Route::get('/', [SerialsController::class, 'pendingSerials'])->name('branch_transactions.pending_serials');
            Route::post('/addBillSerial', [SerialsController::class, 'addBillSerial'])->name('branch_transactions.pending_serials.add_bill');
            Route::post('/dupeSerial', [SerialsController::class, 'dupeSerial'])->name('branch_transactions.pending_serials.dupe_serials');
        });
        // Transfer forex
        Route::prefix('transfer_forex')->group(function() {
            Route::get('/', [TransferForexController::class, 'show'])->name('branch_transactions.transfer_forex');
            Route::post('/validation', [TransferForexController::class, 'validation'])->name('branch_transactions.transfer_forex.validation');
            Route::get('/add', [TransferForexController::class, 'add'])->name('branch_transactions.transfer_forex.add');
            Route::post('/save', [TransferForexController::class, 'save'])->name('branch_transactions.transfer_forex.save');
            Route::get('/detail/{id}', [TransferForexController::class, 'detail'])->name('branch_transactions.transfer_forex.detail');
            Route::post('/details', [TransferForexController::class, 'details'])->name('branch_transactions.transfer_forex.details');
            Route::post('/serials', [TransferForexController::class, 'serials'])->name('branch_transactions.transfer_forex.serials');
            Route::post('/bufferDetails', [TransferForexController::class, 'bufferDetails'])->name('branch_transactions.transfer_forex.buffer_details');
            Route::post('/acknowledge', [TransferForexController::class, 'acknowledge'])->name('branch_transactions.transfer_forex.acknowledge');
            Route::post('/delete', [TransferForexController::class, 'delete'])->name('branch_transactions.transfer_forex.delete');
            Route::post('/addTrackingNo', [TransferForexController::class, 'addTrackingNo'])->name('branch_transactions.transfer_forex.add_tracking_no');
            Route::post('/removeTrackingNo', [TransferForexController::class, 'removeTrackingNo'])->name('branch_transactions.transfer_forex.remove_tracking_no');
            // Route::post('trackingNumber', [TransferForexController::class, 'trackingNumber'])->name('tracking_number');
            // Route::post('addPendingSerials', [TransferForexController::class, 'addPendingSerials']);
        });
    });
    // Routes for Admin Modules =================================================================================================================================================
    Route::prefix('admin_transactions')->middleware(['AdminInitializeSetup'])->group(function() {
    // Route::prefix('admin_transactions')->group(function() {
        // Dashboard
        Route::prefix('dashboard')->group(function() {
            Route::get('/', [DashboardController::class, 'adminDashboard'])->name('admin_transactions.dashboard');
        });
        // Receive Trnasfer Forex
        Route::prefix('receive_transfer_forex')->group(function() {
            Route::get('/', [AdminReceiveTransfersController::class, 'show'])->name('admin_transactions.receive_transfer_forex');
            Route::get('/add', [AdminReceiveTransfersController::class, 'add'])->name('admin_transactions.receive_transfer_forex.add');
            Route::post('/dupe', [AdminReceiveTransfersController::class, 'dupeSerials'])->name('admin_transactions.receive_transfer_forex.dupe_check');
            Route::post('/save', [AdminReceiveTransfersController::class, 'save'])->name('admin_transactions.receive_transfer_forex.save');
            Route::get('/details/{id}', [AdminReceiveTransfersController::class, 'details'])->name('admin_transactions.receive_transfer_forex.details');
            Route::post('/unreceive', [AdminReceiveTransfersController::class, 'unreceive'])->name('admin_transactions.receive_transfer_forex.unreceive');
            Route::post('/unreceiveBills', [AdminReceiveTransfersController::class, 'unreceiveBills'])->name('admin_transactions.receive_transfer_forex.unreceive_bills');
            Route::post('/search', [AdminReceiveTransfersController::class, 'search'])->name('admin_transactions.receive_transfer_forex.search');
            Route::post('/incoming', [AdminReceiveTransfersController::class, 'incoming'])->name('admin_transactions.receive_transfer_forex.incoming');
            // Route::post('searchTransferForex', [AdminReceiveTransfersController::class, 'searchTransferForex'])->name('search_transfers');
            // Route::post('receivedBillsTagging', [AdminReceiveTransfersController::class, 'receivedBillsTagging'])->name('received_bills_tagging');
            // Route::post('untaggingReceivedBills', [AdminReceiveTransfersController::class, 'untaggingReceivedBills'])->name('untagging_received_bills');
        });
        // Currency Stocks
        Route::prefix('stocks')->group(function() {
            Route::get('/admin_stocks', [AdminCurrencyStockController::class, 'adminStocks'])->name('admin_transactions.stocks.admin_stocks');
            Route::post('/admin_stock_details', [AdminCurrencyStockController::class, 'adminStockDetails'])->name('admin_transactions.stocks.admin_stock_details');
            Route::post('/save', [AdminCurrencyStockController::class, 'save'])->name('admin_transactions.stocks.save');
            Route::get('/branch_stocks', [AdminCurrencyStockController::class, 'branchStocks'])->name('admin_transactions.stocks.branch_stocks');
            Route::post('/branch_stock_details', [AdminCurrencyStockController::class, 'branchStockDetails'])->name('admin_transactions.stocks.branch_stock_details');
            Route::post('/branches_currency', [AdminCurrencyStockController::class, 'branchesOfCurrency'])->name('admin_transactions.stocks.branches_of_currency');
            Route::post('/currency_stock_details', [AdminCurrencyStockController::class, 'currencyStockDetails'])->name('admin_transactions.stocks.curr_stock_details');
        });
        // Onhold Bills
        Route::prefix('reserved_stocks')->group(function() {
            Route::get('/', [AdminOnholdController::class, 'show'])->name('admin_transactions.reserved_stocks');
            // Route::post('/save', [AdminOnholdController::class, 'save'])->name('admin_transactions.reserved_stocks.save');
            Route::post('/details', [AdminOnholdController::class, 'details'])->name('admin_transactions.reserved_stocks.details');
            Route::post('/revert', [AdminOnholdController::class, 'revert'])->name('admin_transactions.reserved_stocks.revert');
        });
        // Bulk Selling
        Route::prefix('bulk_selling')->group(function() {
            Route::get('/', [AdminSellingTransactController::class, 'show'])->name('admin_transactions.bulk_selling');
            Route::get('/queue', [AdminSellingTransactController::class, 'queue'])->name('admin_transactions.bulk_selling.queue');
            Route::post('/currencies', [AdminSellingTransactController::class, 'currencies'])->name('admin_transactions.bulk_selling.currencies');
            Route::post('/getBills', [AdminSellingTransactController::class, 'getBills'])->name('admin_transactions.bulk_selling.get_bills');
            Route::post('/queueBills', [AdminSellingTransactController::class, 'queueBills'])->name('admin_transactions.bulk_selling.queue_bills');
            Route::post('/unselect', [AdminSellingTransactController::class, 'unselect'])->name('admin_transactions.bulk_selling.unselect');
            Route::post('/availableBills', [AdminSellingTransactController::class, 'availableBills'])->name('admin_transactions.bulk_selling.availabe_bills');
            Route::post('/addBuffRate', [AdminSellingTransactController::class, 'addBuffRate'])->name('admin_transactions.bulk_selling.add_buff_rate');
            Route::get('/sell', [AdminSellingTransactController::class, 'sell'])->name('admin_transactions.bulk_selling.sell');
            Route::post('/queued', [AdminSellingTransactController::class, 'queued'])->name('admin_transactions.bulk_selling.queued');
            Route::post('/save', [AdminSellingTransactController::class, 'save'])->name('admin_transactions.bulk_selling.save');
            Route::post('/cappedBills', [AdminSellingTransactController::class, 'cappedBills'])->name('admin_transactions.bulk_selling.capped_bills');
            Route::post('/unqueueCappedBills', [AdminSellingTransactController::class, 'unqueueCappedBills'])->name('admin_transactions.bulk_selling.unqueue_capped_bills');
            Route::get('/details/{id}', [AdminSellingTransactController::class, 'details'])->name('admin_transactions.bulk_selling.details');
            Route::post('/print', [AdminSellingTransactController::class, 'print'])->name('admin_transactions.bulk_selling.print');
            Route::post('/printQueued', [AdminSellingTransactController::class, 'printQueued'])->name('admin_transactions.bulk_selling.print_queued');
            // Route::post('consolidateTransferBills', [AdminSellingTransactController::class, 'consolidateTransferBills'])->name('consolidate_transfer_bills');
        });
        // Buying Transaction
        Route::prefix('admin_b_transaction')->group(function() {
            Route::get('/', [AdminBuyingTransactionController::class, 'show'])->name('admin_transactions.buying_transaction');
            Route::get('/add', [AdminBuyingTransactionController::class, 'add'])->name('admin_transactions.admin_b_transaction.add');
            Route::post('/denomination', [AdminBuyingTransactionController::class, 'denomination'])->name('admin_transactions.admin_b_transaction.denomination');
            Route::post('/currencies', [AdminBuyingTransactionController::class, 'currencies'])->name('admin_transactions.admin_b_transaction.currencies');
            Route::post('/orNumbDuplicateBuying', [AdminBuyingTransactionController::class, 'orNumbDuplicateBuying'])->name('admin_transactions.admin_b_transaction.or_number_duplicate_b');
            Route::post('/save', [AdminBuyingTransactionController::class, 'save'])->name('admin_transactions.admin_b_transaction.save');
            Route::post('/print', [AdminBuyingTransactionController::class, 'print'])->name('admin_transactions.admin_b_transaction.print');
            Route::get('/details/{id}', [AdminBuyingTransactionController::class, 'details'])->name('admin_transactions.admin_b_transaction.details');
            Route::post('/update', [AdminBuyingTransactionController::class, 'update'])->name('admin_transactions.admin_b_transaction.update');
            Route::post('/update_rate', [AdminBuyingTransactionController::class, 'updateRate'])->name('admin_transactions.admin_b_transaction.update_rate');
            Route::get('/serials/{id}', [AdminBuyingTransactionController::class, 'serials'])->name('admin_transactions.admin_b_transaction.serials');
            Route::post('/saveSerials', [AdminBuyingTransactionController::class, 'saveSerials'])->name('admin_transactions.admin_b_transaction.save_serials');
            Route::post('/void', [AdminBuyingTransactionController::class, 'void'])->name('admin_transactions.admin_b_transaction.void');
            // Route::get('pendingSerials', [AdminSerialsController::class, 'pendingSerials'])->name('pending_serials');
        });
        // Selling Transaction
        Route::prefix('admin_s_transaction')->group(function() {
            Route::get('/', [AdminRetailSellingController::class, 'show'])->name('admin_transactions.selling_transaction');
            Route::get('/add', [AdminRetailSellingController::class, 'add'])->name('admin_transactions.admin_s_transaction.add');
            Route::post('/serials', [AdminRetailSellingController::class, 'serials'])->name('admin_transactions.admin_s_transaction.serials');
            Route::post('/stocks', [AdminRetailSellingController::class, 'stocks'])->name('admin_transactions.admin_s_transaction.stocks');
            Route::post('/save', [AdminRetailSellingController::class, 'save'])->name('admin_transactions.admin_s_transaction.save');
            Route::post('/OrNoDuplicateRetailSelling', [AdminRetailSellingController::class, 'OrNoDuplicateRetailSelling'])->name('admin_transactions.admin_s_transaction.duplicate');
            Route::post('/print', [AdminRetailSellingController::class, 'print'])->name('admin_transactions.admin_s_transaction.print');
            Route::get('/details/{id}', [AdminRetailSellingController::class, 'details'])->name('admin_transactions.admin_s_transaction.details');
            Route::post('/update', [AdminRetailSellingController::class, 'update'])->name('admin_transactions.admin_s_transaction.update');
            Route::post('/void', [AdminRetailSellingController::class, 'void'])->name('admin_transactions.admin_s_transaction.void');
        });
        // Pending Serials
        Route::prefix('admin_pending_serials')->group(function() {
            Route::get('/', [AdminSerialsController::class, 'show'])->name('admin_transactions.pending_serials');
        });
        // Buffer ni Rolly
        Route::prefix('buffer')->group(function() {
            Route::get('/buffer', [BufferController::class, 'buffer'])->name('admin_transactions.buffer.buffer');
            Route::get('/stocks/{branch_id}', [BufferController::class, 'stocks'])->name('admin_transactions.buffer.stocks');
            Route::post('/cut_validation', [BufferController::class, 'cutValidation'])->name('admin_transactions.buffer.cut_validation');
            Route::post('/b_cut_validation', [BufferController::class, 'bufferCutBranch'])->name('admin_transactions.buffer.b_cut_validation');
            Route::post('/cut_processing', [BufferController::class, 'cutProcessing'])->name('admin_transactions.buffer.cut_processing');
            Route::post('/saveBuffer', [BufferController::class, 'saveBuffer'])->name('admin_transactions.buffer.save');
            Route::get('/transfers', [BufferController::class, 'transfers'])->name('admin_transactions.buffer.buffer_transfers');
            Route::get('/receive_buffer', [BufferController::class, 'receiveTransfers'])->name('admin_transactions.buffer.receive_buffer');
            Route::post('/search_buffer', [BufferController::class, 'searchBuffer'])->name('admin_transactions.buffer.search_buffer');
            Route::post('/receive', [BufferController::class, 'receive'])->name('admin_transactions.buffer.receive');
            Route::get('/details/{id}', [BufferController::class, 'details'])->name('admin_transactions.buffer.details');
            Route::post('/revert', [BufferController::class, 'revert'])->name('admin_transactions.buffer.revert');
            Route::post('/incoming_buff_details', [BufferController::class, 'incomingBuffDetails'])->name('admin_transactions.buffer.incoming_buff_details');
            Route::post('/revert_buffer', [BufferController::class, 'revertBuffer'])->name('admin_transactions.buffer.revert_buffer');

            Route::get('/wallet', [BufferController::class, 'wallet'])->name('admin_transactions.buffer.buffer_wallet');
            Route::get('/buffer_financing', [BufferController::class, 'financing'])->name('admin_transactions.buffer.buffer_financing');
            Route::get('/add_financing', [BufferController::class, 'addFinancing'])->name('admin_transactions.buffer.add_financing');
            Route::post('/save_financing', [BufferController::class, 'saveFinancing'])->name('admin_transactions.buffer.save_financing');
            Route::get('/break_d_finance/{BFID}', [BufferController::class, 'breakdownBuffer'])->name('admin_transactions.buffer.break_d_finance');
            Route::post('/denominations', [BufferController::class, 'denominations'])->name('admin_transactions.buffer.denominations');
            Route::post('/save_break_d', [BufferController::class, 'saveBreakdownBuff'])->name('admin_transactions.buffer.save_break_d');
            Route::get('/buffer_serials/{BFID}', [BufferController::class, 'bufferSerials'])->name('admin_transactions.buffer.buffer_serials');
        });
        // DPOFX
        Route::prefix('dpofx')->group(function() {
            Route::get('/wallet', [AdminDPOFXController::class, 'wallet'])->name('admin_transactions.dpofx.wallet');
            Route::get('/dpo_in', [AdminDPOFXController::class, 'showIn'])->name('admin_transactions.dpofx.dpo_in');
            Route::get('/dpo_add_in', [AdminDPOFXController::class, 'addIn'])->name('admin_transactions.dpofx.dpo_add_in');
            Route::post('/dpo_in_details', [AdminDPOFXController::class, 'inDetails'])->name('admin_transactions.dpofx.dpo_in_details');
            Route::post('/DPOFXS', [AdminDPOFXController::class, 'DPOFXS'])->name('admin_transactions.dpofx.DPOFXS');
            Route::post('/save_dpo_in', [AdminDPOFXController::class, 'saveIn'])->name('admin_transactions.dpofx.save_dpo_in');
            Route::get('/dpo_out', [AdminDPOFXController::class, 'showOut'])->name('admin_transactions.dpofx.dpo_out');
            Route::get('/dpo_add_out', [AdminDPOFXController::class, 'addOut'])->name('admin_transactions.dpofx.dpo_add_out');
            Route::post('/DPOFXINS', [AdminDPOFXController::class, 'DPOFXINS'])->name('admin_transactions.dpofx.DPOFXINS');
            Route::post('/save_dpo_out', [AdminDPOFXController::class, 'save'])->name('admin_transactions.dpofx.save_dpo_out');
            Route::get('/dpo_out_details/{id}', [AdminDPOFXController::class, 'outDetails'])->name('admin_transactions.dpofx.dpo_out_details');
            Route::post('/update', [AdminDPOFXController::class, 'update'])->name('admin_transactions.dpofx.update');
            Route::post('/print', [AdminDPOFXController::class, 'print'])->name('admin_transactions.dpofx.print');
        });
        // Bill Tagging
        Route::prefix('bill_tagging')->group(function() {
            Route::get('/', [AdminBillTaggingController::class, 'show'])->name('admin_transactions.bill_tagging');
            Route::post('/search', [AdminBillTaggingController::class, 'search'])->name('admin_transactions.bill_tagging.search');
            Route::post('/employees', [AdminBillTaggingController::class, 'employees'])->name('admin_transactions.bill_tagging.employees');
            Route::post('/save', [AdminBillTaggingController::class, 'save'])->name('admin_transactions.bill_tagging.save');
            Route::post('/untag', [AdminBillTaggingController::class, 'untag'])->name('admin_transactions.bill_tagging.untag');
            Route::post('/saveATDEmp', [AdminBillTaggingController::class, 'saveATDEmp'])->name('admin_transactions.bill_tagging.save_atd_emp');
            Route::post('/saveATDNo', [AdminBillTaggingController::class, 'saveATDNo'])->name('admin_transactions.bill_tagging.save_atd_no');
            Route::post('/selectATDNo', [AdminBillTaggingController::class, 'selectATDNo'])->name('admin_transactions.bill_tagging.select_atd_no');
            Route::post('/print', [AdminBillTaggingController::class, 'print'])->name('admin_transactions.bill_tagging.print');
            Route::post('/eps_ATD', [AdminBillTaggingController::class, 'epsATD'])->name('admin_transactions.bill_tagging.eps_atd');
        });
        // Trans. Cap.
        Route::prefix('trans_cap')->group(function() {
            Route::get('/', [AdminTransCapController::class, 'show'])->name('admin_transactions.trans_cap');
            Route::post('/details', [AdminTransCapController::class, 'details'])->name('admin_transactions.details');
            Route::post('/transfer', [AdminTransCapController::class, 'transfer'])->name('admin_transactions.transfer');
        });
    });

// Route::get('test', [LoginController::class, 'test'])->middleware('UserLevel');
// Route::group(['namespace' => 'Window'], function() {
    Route::prefix('maintenance')->group(function() {
        // Window based - Branch Maintenance
        Route::prefix('branch_maintenance')->group(function() {
            Route::get('/', [BranchMaintenanceController::class, 'show'])->name('maintenance.branch_maintenance');
            Route::post('add', [BranchMaintenanceController::class, 'add'])->name('maintenance.branch_maintenance.add');
            Route::post('edit', [BranchMaintenanceController::class, 'edit'])->name('maintenance.branch_maintenance.edit');
            Route::post('update', [BranchMaintenanceController::class, 'update'])->name('maintenance.branch_maintenance.update');
        });
        // Window based - Currency Maintenance
        Route::prefix('currency_maintenance')->group(function() {
            Route::get('/', [CurrencyMaintenanceController::class, 'show'])->name('maintenance.currency_maintenance');
            Route::post('/add', [CurrencyMaintenanceController::class, 'add'])->name('maintenance.currency_maintenance.add');
            Route::post('/edit', [CurrencyMaintenanceController::class, 'edit'])->name('maintenance.currency_maintenance.edit');
            Route::post('/update', [CurrencyMaintenanceController::class, 'update'])->name('maintenance.currency_maintenance.update');
            Route::post('/search', [CurrencyMaintenanceController::class, 'search'])->name('maintenance.currency_maintenance.search');
            Route::post('/delete', [CurrencyMaintenanceController::class, 'delete'])->name('maintenance.currency_maintenance.delete');
            Route::get('/editDenom/{currency_id}', [CurrencyMaintenanceController::class, 'editDenom'])->name('maintenance.currency_maintenance.edit_denom');
            Route::post('/updateDenominations', [CurrencyMaintenanceController::class, 'updateDenominations'])->name('maintenance.currency_maintenance.update_denom');
            Route::post('/updateOneDenom', [CurrencyMaintenanceController::class, 'updateOneDenom'])->name('maintenance.currency_maintenance.update_one_denom');
            Route::post('/deleteDenom', [CurrencyMaintenanceController::class, 'deleteDenom'])->name('maintenance.currency_maintenance.delete_denom');
            Route::post('/existing', [CurrencyMaintenanceController::class, 'existing'])->name('maintenance.currency_maintenance.existing');
            Route::post('/transType', [CurrencyMaintenanceController::class, 'transType'])->name('maintenance.currency_maintenance.trans_type');
        });
        // Window based - Rate Maintenance
        Route::prefix('rate_maintenance')->group(function() {
            Route::get('/rate', [RateMaintenanceController::class, 'show'])->name('maintenance.rate_maintenance.rate_maintenance');
            Route::post('/save', [RateMaintenanceController::class, 'save'])->name('maintenance.rate_maintenance.save');
            Route::post('/edit', [RateMaintenanceController::class, 'edit'])->name('maintenance.rate_maintenance.edit');
            Route::post('/update', [RateMaintenanceController::class, 'update'])->name('maintenance.rate_maintenance.update');
            Route::post('/countryAutoSelect', [RateMaintenanceController::class, 'countryAutoSelect'])->name('maintenance.rate_maintenance.select');
            Route::post('/history', [RateMaintenanceController::class, 'history'])->name('maintenance.rate_maintenance.history');
            Route::get('/dpofx_rate', [RateMaintenanceController::class, 'dpofxRate'])->name('maintenance.rate_maintenance.dpofx_rate');
            Route::post('/edit_dpo', [RateMaintenanceController::class, 'editDpo'])->name('maintenance.rate_maintenance.edit_dpofx_rate');
            Route::post('/update_dpofx_rate', [RateMaintenanceController::class, 'updateDpofxRate'])->name('maintenance.rate_maintenance.update_dpofx_rate');
            Route::post('/update_DPO_rate', [RateMaintenanceController::class, 'updateDPORate'])->name('maintenance.rate_maintenance.update_dpo_rate');
        });
        // Window based - Rate Configuration
        Route::prefix('rate_configuration')->group(function() {
            Route::get('/', [RateConfigurationController::class, 'show'])->name('maintenance.rate_configuration');
            Route::post('/denom' , [RateConfigurationController::class, 'denom'])->name('maintenance.rate_configuration.denom');
            Route::post('/update' , [RateConfigurationController::class, 'update'])->name('maintenance.rate_configuration.update');
            Route::post('/configHistory' , [RateConfigurationController::class, 'configHistory'])->name('maintenance.rate_configuration.config_history');
        });
        // Receipt Series Maintenance
        Route::prefix('form_series')->group(function() {
            Route::get('/', [FCFSeriesMaintenanceController::class, 'show'])->name('maintenance.form_series');
            Route::post('/add', [FCFSeriesMaintenanceController::class, 'add'])->name('maintenance.form_series.add');
            Route::post('/edit', [FCFSeriesMaintenanceController::class, 'edit'])->name('maintenance.form_series.edit');
            Route::post('/update', [FCFSeriesMaintenanceController::class, 'update'])->name('maintenance.form_series.update');
            Route::post('/delete', [FCFSeriesMaintenanceController::class, 'delete'])->name('maintenance.form_series.delete');
            Route::post('/existing', [FCFSeriesMaintenanceController::class, 'existing'])->name('maintenance.form_series.exisiting');
        });
        // Bill Tags Maintenance
        Route::prefix('bill_tags')->group(function() {
            Route::get('/', [TagsMaintenanceController::class, 'show'])->name('maintenance.bill_tags');
            Route::post('/add', [TagsMaintenanceController::class, 'add'])->name('maintenance.bill_tags.add');
            Route::post('/edit', [TagsMaintenanceController::class, 'edit'])->name('maintenance.bill_tags.edit');
            Route::post('/update', [TagsMaintenanceController::class, 'update'])->name('maintenance.bill_tags.update');
            Route::post('/delete', [TagsMaintenanceController::class, 'delete'])->name('maintenance.bill_tags.delete');
        });
        // Transaction Type Maintenance
        Route::prefix('transaction_types')->group(function() {
            Route::get('/', [TransactTypeMaintenanceController::class, 'show'])->name('maintenance.transaction_types');
            Route::post('/add', [TransactTypeMaintenanceController::class, 'add'])->name('maintenance.transaction_types.add');
            Route::post('/edit', [TransactTypeMaintenanceController::class, 'edit'])->name('maintenance.transaction_types.edit');
            Route::post('/update', [TransactTypeMaintenanceController::class, 'update'])->name('maintenance.transaction_types.update');
            Route::post('/delete', [TransactTypeMaintenanceController::class, 'delete'])->name('maintenance.transaction_types.delete');
        });
        // Bulk Limit Maintenance
        Route::prefix('bulk_limit')->group(function() {
            Route::get('/', [BulkLimitControllerMaintenance::class, 'show'])->name('maintenance.bulk_limit');
            Route::post('/add', [BulkLimitControllerMaintenance::class, 'add'])->name('maintenance.bulk_limit.add');
            Route::post('/edit', [BulkLimitControllerMaintenance::class, 'edit'])->name('maintenance.bulk_limit.edit');
            Route::post('/update', [BulkLimitControllerMaintenance::class, 'update'])->name('maintenance.bulk_limit.update');
            Route::post('/delete', [BulkLimitControllerMaintenance::class, 'delete'])->name('maintenance.bulk_limit.delete');
            Route::post('/exisiting', [BulkLimitControllerMaintenance::class, 'exisiting'])->name('maintenance.bulk_limit.exisiting');
        });
        // Configuration
        Route::prefix('configuration')->group(function() {
            Route::get('/', [ConfigurationController::class, 'show'])->name('maintenance.configuration');
            Route::post('/update', [ConfigurationController::class, 'update'])->name('maintenance.configuration.update');
        });
        // Stop Buying
        Route::prefix('denom_configuration')->group(function() {
            Route::get('/', [StopBuyingMaintenanceController::class, 'show'])->name('maintenance.denom_configuration');
            Route::post('/getDenomination', [StopBuyingMaintenanceController::class, 'getDenomination'])->name('maintenance.denom_configuration.get_denomination');
            Route::post('/updateStopBuyingStatus', [StopBuyingMaintenanceController::class, 'updateStopBuyingStatus'])->name('maintenance.denom_configuration.update');
            Route::post('/currentStop' , [StopBuyingMaintenanceController::class, 'currentStop'])->name('maintenance.denom_configuration.config_stop_history');
        });
        // Currency Manual
        Route::prefix('currency_manual')->group(function() {
            Route::get('/', [CurrencyManualMaintenanceController::class, 'show'])->name('maintenance.currency_manual');
            Route::post('/getDenominations', [CurrencyManualMaintenanceController::class, 'getDenominations'])->name('maintenance.currency_manual.get_denominations');
            Route::post('/addCurrencyManual', [CurrencyManualMaintenanceController::class, 'addCurrencyManual'])->name('maintenance.currency_manual.add');
            Route::post('/existing', [CurrencyManualMaintenanceController::class, 'existing'])->name('maintenance.currency_manual.existing');
            Route::get('/currencyManualDetail/{id}', [CurrencyManualMaintenanceController::class, 'currencyManualDetail'])->name('maintenance.currency_manual.view');
            Route::post('/editManualDetails', [CurrencyManualMaintenanceController::class, 'editManualDetails'])->name('maintenance.currency_manual.edit');
            Route::post('/updateCurrencyManual', [CurrencyManualMaintenanceController::class, 'updateCurrencyManual'])->name('maintenance.currency_manual.update');
            Route::post('/deleteManual', [CurrencyManualMaintenanceController::class, 'deleteManual'])->name('maintenance.currency_manual.delete');
        });
        // Company Limit
        Route::prefix('company_limit')->group(function() {
            Route::get('/', [CompanyLimitMaintenanceController::class, 'show'])->name('maintenance.company_limit');
            Route::get('/add', [CompanyLimitMaintenanceController::class, 'add'])->name('maintenance.company_limit.add_company_limit');
            Route::post('/save', [CompanyLimitMaintenanceController::class, 'save'])->name('maintenance.company_limit.save_company_limit');
            Route::get('/edit/{CLDID}', [CompanyLimitMaintenanceController::class, 'edit'])->name('maintenance.company_limit.edit_company_limit');
            Route::post('/update', [CompanyLimitMaintenanceController::class, 'update'])->name('maintenance.company_limit.update_company_limit');
        });
        // R-set Series
        Route::prefix('r_set_series')->group(function() {
            Route::get('/', [RsetSeriesMaintenanceController::class, 'show'])->name('maintenance.r_set_series');
            Route::post('/add', [RsetSeriesMaintenanceController::class, 'add'])->name('maintenance.r_set_series.add_r_set_series');
            Route::post('/edit', [RsetSeriesMaintenanceController::class, 'edit'])->name('maintenance.r_set_series.edit');
            Route::post('/update', [RsetSeriesMaintenanceController::class, 'update'])->name('maintenance.r_set_series.update');
            Route::post('/delete', [RsetSeriesMaintenanceController::class, 'delete'])->name('maintenance.r_set_series.delete');
            Route::post('/exisisting', [RsetSeriesMaintenanceController::class, 'exisisting'])->name('maintenance.r_set_series.exisisting');
        });
    });

    // Excluded Routes ===================================================================================================================================
    // Denomination Maintenance
    // Route::get('denominations', [DenominationMaintenanceController::class, 'denominations'])->name('denominations');
    // Route::get('addDenominations', [DenominationMaintenanceController::class, 'addDenominations'])->name('add_denominations');
    // Route::post('saveDenomination', [DenominationMaintenanceController::class, 'saveDenomination'])->name('save_denominations');
    // Route::get('editDenomination', [DenominationMaintenanceController::class, 'editDenomination'])->name('edit_denomination');

    // Window based - Expense Maintenance
    Route::get('expenseMaintenance', [ExpenseMaintenanceController::class, 'expenseMaintenance']);
    Route::post('addNewExpense', [ExpenseMaintenanceController::class, 'addNewExpense']);
    Route::post('editExpense', [ExpenseMaintenanceController::class, 'editExpense']);
    Route::post('updateExpense', [ExpenseMaintenanceController::class, 'updateExpense']);
    // Search word - Window based - Currency Maintenance
    Route::post('searchExpense', [BuyingTransactController::class, 'searchExpense']);
// });
});
