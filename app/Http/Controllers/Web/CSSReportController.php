<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use App\Models\User;
use DB;
use Illuminate\Support\Carbon;
use Hash;
use Session;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;

class CSSReportController extends Controller {
    public function reportErrorShow(Request $request) {
        $css_concerns = '';
        $menu_id = $request->get('menu_id');

        $query = DB::connection('cssystem')->table('tblsconcern')
            ->where('ConcernID', 50);

        switch ($menu_id) {
            case ENV('B_BUYING_TRANS'):
                $css_concerns = $query->select('SConcernID','SConcern')
                    // ->whereIn('SConcernID', [277, 487, 486, 265, 485, 276, 266, 267, 268])
                    ->whereIn('SConcernID', [267, 268, 276, 277, 474])
                    ->get();

                break;
            case ENV('B_SELLING_TRANS'):
                $css_concerns = $query->select('SConcernID','SConcern')
                    ->whereIn('SConcernID', [267, 268, 276, 277, 474])
                    ->get();

                break;
            case ENV('B_TRANSFER_FX'):
                $css_concerns = $query->select('SConcernID','SConcern')
                    ->where('SConcernID', 269)
                    ->get();

                break;

            default:
                dd("hehe");
        }

        $response = [
            'css_concerns' => $css_concerns
        ];

        return response()->json($response);
    }

    public function sendReportError(Request $request) {
        $query = '';
        $details = '';
        $concern_attachment = '';
        $menu_id = $request->get('menu_id');

        exec('arp -a', $output, $return_var);
        $currentIpAddress = getServerIpAddress();

        $ip_adds = [];

        if ($return_var === 0) {
            foreach ($output as $line) {
                if (preg_match('/\b(\d{1,3}\.){3}\d{1,3}\b/', $line, $matches)) {
                    $ip_adds[] = $matches[0];
                }
            }
        }

        // $exact_ip_add = array_intersect($ip_adds, [$currentIpAddress]);

        // dd($exact_ip_add[0]);
        //     return response()->json(['ip_addresses' => array_unique($ip_adds)]);
        // } else {
        //     return response()->json(['error' => 'Could not retrieve network IPs'], 500);
        // }

        switch ($menu_id) {
            case ENV('B_BUYING_TRANS'):
                $details =  DB::connection('forex')->table('tblforextransactiondetails as fd')
                    ->where('fd.FTDID', $request->get('ID'));

                break;
            case ENV('B_SELLING_TRANS'):
                $details =  DB::connection('forex')->table('tblsoldcurrdetails as sc')
                    ->where('sc.SCID', $request->get('ID'));

                break;
            case ENV('B_TRANSFER_FX'):
                $details =  DB::connection('forex')->table('tbltransferforex as tfx')
                    ->where('tfx.TransferForexID', $request->get('ID'));

                break;

            default:
                dd("hehe");
        }

        $priotity_id = DB::connection('cssystem')->table('tblsconcern')->where('SConcernID', $request->sconcernid)->value('PriorityID');
        $next_ticket_no = DB::connection('cssystem')->table('tblticket')->max('TicketNo') + 1;

        $path = '';

        if ($request->hasFile('concern_attachement')) {
            $concern_attachment = $request->file('concern_attachement');

            $validator = Validator::make($request->all(), [
                'concern_attachment' => 'nullable|mimes:jpeg,png,jpg,pdf,doc,docx,xlsx',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $concern_attachment_name = $concern_attachment->getClientOriginalName();

                // if (isset($ip_adds[12])) {
                    // $path = '\\\\' . $ip_adds[12] . '\\htdocs\\cssystem\\uploads\\' . $concern_attachment_name;
                    $path = '\\\\' .  config('app.css_attachment_path') . '\\htdocs\\cssystem\\uploads\\' . $concern_attachment_name;

                    $targetDir = dirname($path);

                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }

                    $concern_attachment->move($targetDir, $concern_attachment_name);
                // } else {
                //     return redirect()->back()->withErrors(['error' => 'IP address not found.'])->withInput();
                // }
            }
        } else {
            $concern_attachment == null;
        }

        $sent = DB::connection('cssystem')->table('tblticket')
            ->insert([
                'TicketNo' => $next_ticket_no,
                'BranchID' => $details->first()->BranchID,
                'RequestDate' => now(),
                'RDEntryDate' => now(),
                'UserID' => $request->get('matched_user_id'),
                'RequestBy' => $request->get('matched_user_id'),
                'DeptID' => 6,
                'SDeptID' => 35,
                'ConcernID' => 50,
                'SConcernID' => $request->sconcernid,
                'Remarks' => $request->Remarks. ' ' .'(FROM FOREX V2)',
                'Attachment' => $request->file('concern_attachement') ? $request->file('concern_attachement')->getClientOriginalName() : null,
                'PriorityID' => $priotity_id,
                // 'CSR' => $data,
                // 'DateAssigned'  => $data,
                // 'DSEntryDate'  => $data,
                'ETID' => 4,
                // 'DateAccomplished' => $data,
                // 'AccomplishedBy' => $data,
                // 'DACEntryDate' => $data,
                'StatusID' => 1,
            ]);

        if ($sent) {
            switch ($menu_id) {
                case ENV('B_BUYING_TRANS'):
                    $details->update([
                        'HasTicket' => 1,
                    ]);

                    break;
                case ENV('B_SELLING_TRANS'):
                    $details->update([
                        'HasTicket' => 1,
                    ]);

                    break;
                case ENV('B_TRANSFER_FX'):
                    $details->update([
                        'HasTicket' => 1,
                    ]);

                    break;

                default:
                    dd("hehe");
            }
        }
    }
}
