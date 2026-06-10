<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MomController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Management_model');
        $this->load->model('Menu_model');
        $this->load->library('zip');
        $this->load->library('pdf');
    }

//   public function download_all_mom_pdf($mtypes, $target_uid, $sdate, $edate, $userwise = NULL) {
    
    
//         $user           = $this->session->userdata('user');
//         $data['user']   = $user;
//         $uid            = $user['user_id'];
//         $uyid           =  $user['type_id'];
    
    
//     $totalMeetingsUserByDatas = $this->Menu_model->TotalMeetingDetailsDatas($target_uid, $sdate, $edate, $mtypes, $target_uid, $userwise);
//     $pdfDir = FCPATH . 'uploads/pdf/';
//     if (!is_dir($pdfDir)) {
//         mkdir($pdfDir, 0777, true);
//     }
//     $pdfFiles = [];

//     if(sizeof($totalMeetingsUserByDatas) > 0){

//     foreach ($totalMeetingsUserByDatas as $row) {
//         $momtask_mom = '';
//         $meeting_user_id = $row->user_id;
//         $meeting_task_id = $row->task_id;
//         $momtaskDatas = $this->Menu_model->GetTBLMomTaskByTaskId($meeting_task_id);

//         if (!empty($momtaskDatas)) {
//             $momtask_id = $momtaskDatas[0]->id;
//             $momtask_mom = $momtaskDatas[0]->mom;
//             $momdatas = $this->Menu_model->GetMomDataByTaskId($momtask_id);

//             if (!empty($momdatas)) {
//                 $mom_id = $momdatas[0]->id;
//                 $momdata = $this->Menu_model->getRequestMOMBYID($mom_id);
                
//                 $mom_userName =  $udetail = $this->Menu_model->get_userbyid($momdata[0]->user_id)[0]->name;;
//                 $init_cmpid   =  $momdata[0]->init_cmpid;
//                 $mom_cdate    =  $momdata[0]->cdate;

//                 $cominfo            = $this->Menu_model->get_cmpbyinidINFO($init_cmpid);
//                 $compname           = $cominfo[0]->company_name;
//                 $company_id         = $cominfo[0]->cid;

//                 $html = $this->load->view('mom_pdf_template', [
//                     'momdata' => $momdata,
//                     't_id' => $mom_id,
//                     'ce_id' => $row->cid,'uid'=>$uid,'user'=>$user,'cdate'=>date("Y-m-d"),
//                 ], TRUE);

//                $filename = 'MOM_' . 
//                     preg_replace('/\s+/', '_', $mom_userName) . 
//                     '_Company_Name_' . 
//                     preg_replace('/[\s\.]+/', '_', $compname) . 
//                     '_CID_' . 
//                     $company_id . 
//                     '_MOM_Created_Date_' . 
//                     str_replace([' ', ':'], '_', $mom_cdate) . 
//                     '.pdf';

//                 $filepath = $pdfDir . $filename;
//                 $this->pdf->createPDF($html, $filepath, TRUE);
//                 $pdfFiles[] = $filepath;

//             }
//         }
//     }
// }

//     // Create ZIP file
//     if (!empty($pdfFiles)) {
//         $zipname = 'MOM_All_' . date('Y-m-d_H-i-s') . '.zip';
//         $zip_path = $pdfDir . $zipname;
//         $this->load->library('zip');
//         foreach ($pdfFiles as $file) {
//             $this->zip->read_file($file);
//         }
//         $this->zip->archive($zip_path);
//         $this->zip->download($zipname);
//     } else {
//         echo "No MOM data found to generate PDFs.";
//     }

//     redirect('Menu/MeetingsDatas/'.$mtypes.'/'.$target_uid.'/'.$sdate.'/'.$edate);
// }




public function download_all_mom_pdf($mtypes, $target_uid, $sdate, $edate, $userwise = NULL)
{
    $user = $this->session->userdata('user');
    $uid  = $user['user_id'];
    $uyid = $user['type_id'];

    $totalMeetingsUserByDatas = $this->Menu_model->TotalMeetingDetailsDatas(
        $target_uid,
        $sdate,
        $edate,
        $mtypes,
        $target_uid,
        $userwise
    );

    $pdfDir = FCPATH . 'uploads/pdf/';
    if (!is_dir($pdfDir)) {
        mkdir($pdfDir, 0777, true);
    }

    $pdfFiles = [];

    if (!empty($totalMeetingsUserByDatas)) {
        foreach ($totalMeetingsUserByDatas as $row) {
            $momtaskDatas = $this->Menu_model->GetTBLMomTaskByTaskId($row->task_id);

            if (!empty($momtaskDatas)) {
                $momdatas = $this->Menu_model->GetMomDataByTaskId($momtaskDatas[0]->id);

                if (!empty($momdatas)) {
                    $momdata = $this->Menu_model->getRequestMOMBYID($momdatas[0]->id);
                    $mom_userName = $this->Menu_model->get_userbyid($momdata[0]->user_id)[0]->name;
                    $init_cmpid = $momdata[0]->init_cmpid;
                    $mom_cdate = $momdata[0]->cdate;

                    $cominfo = $this->Menu_model->get_cmpbyinidINFO($init_cmpid);
                    $compname = $cominfo[0]->company_name;
                    $company_id = $cominfo[0]->cid;

                    $html = $this->load->view('mom_pdf_template', [
                        'momdata' => $momdata,
                        't_id'    => $momdatas[0]->id,
                        'ce_id'   => $row->cid,
                        'uid'     => $uid,
                        'user'    => $user,
                        'cdate'   => date("Y-m-d"),
                    ], TRUE);

                    $filename = 'MOM_' .
                        preg_replace('/\s+/', '_', $mom_userName) .
                        '_CompanyName_' .
                        preg_replace('/[\s\.]+/', '_', $compname) .
                        '_CID_' . $company_id .
                        '_MOM_Created_Date_' .
                        str_replace([' ', ':'], '_', $mom_cdate) .
                        '.pdf';

                    $filepath = $pdfDir . $filename;
                    $this->pdf->createPDF($html, $filepath, TRUE);
                    $pdfFiles[] = $filepath;
                }
            }
        }
    }

    if (!empty($pdfFiles)) {
        $zipname = 'MOM_All_' . date('Y-m-d_H-i-s') . '.zip';
        $zip_path = $pdfDir . $zipname;

        $this->load->library('zip');
        foreach ($pdfFiles as $file) {
            $this->zip->read_file($file);
        }

        // Save ZIP on server
        $this->zip->archive($zip_path);

        // Prepare data for view (for JS download + redirect)
        $data['zip_path'] = base_url('uploads/pdf/' . $zipname);


        $data['redirect_url'] = site_url('Reports/MeetingsDatas/' . $mtypes . '/' . $target_uid . '/' . $sdate . '/' . $edate);

        // Load the auto-download + redirect view
        $this->load->view('auto_download_redirect', $data);
    } else {
        echo "No MOM data found to generate PDFs.";
    }
}




public function download_all_mom_pdfFile($mtypes, $target_uid, $sdate, $edate, $userwise = NULL)
{
    $user = $this->session->userdata('user');
    $uid  = $user['user_id'];

    // Get all meetings data
    $totalMeetingsUserByDatas = $this->Menu_model->TotalMeetingDetailsDatas(
        $target_uid,
        $sdate,
        $edate,
        $mtypes,
        $target_uid,
        $userwise
    );

    // Temporary PDF directory for this user
    $pdfDir = FCPATH . 'uploads/pdf/' . $uid . '/';
    if (!is_dir($pdfDir)) {
        mkdir($pdfDir, 0777, true);
    }

    $pdfFiles = [];

    if (!empty($totalMeetingsUserByDatas)) {
        foreach ($totalMeetingsUserByDatas as $row) {
            $momtaskDatas = $this->Menu_model->GetTBLMomTaskByTaskId($row->task_id);
            if (!empty($momtaskDatas)) {
                $momdatas = $this->Menu_model->GetMomDataByTaskId($momtaskDatas[0]->id);
                if (!empty($momdatas)) {
                    $momdata = $this->Menu_model->getRequestMOMBYID($momdatas[0]->id);
                    $mom_userName = $this->Menu_model->get_userbyid($momdata[0]->user_id)[0]->name;
                    $init_cmpid  = $momdata[0]->init_cmpid;
                    $mom_cdate   = $momdata[0]->cdate;

                    $cominfo = $this->Menu_model->get_cmpbyinidINFO($init_cmpid);
                    $compname = $cominfo[0]->company_name;
                    $company_id = $cominfo[0]->cid;

                    // Load HTML content for PDF
                    $html = $this->load->view('mom_pdf_template', [
                        'momdata' => $momdata,
                        't_id'    => $momdatas[0]->id,
                        'ce_id'   => $row->cid,
                        'uid'     => $uid,
                        'user'    => $user,
                        'cdate'   => date("Y-m-d"),
                    ], TRUE);

                    // Generate safe filename
                    $filename = 'MOM_' .
                        preg_replace('/\s+/', '_', $mom_userName) .
                        '_CompanyName_' .
                        preg_replace('/[\s\.]+/', '_', $compname) .
                        '_CID_' . $company_id .
                        '_MOM_Created_Date_' .
                        str_replace([' ', ':'], '_', $mom_cdate) .
                        '.pdf';

                    $filepath = $pdfDir . $filename;

                    // Generate PDF and save
                    $this->pdf->createPDF($html, $filepath, TRUE);

                    $pdfFiles[] = $filepath;
                }
            }
        }
    }

   


    if (!empty($pdfFiles)) {
    $zipname = 'MOM_All_' . date('Y-m-d_H-i-s') . '.zip';
    $zip_path = $pdfDir . $zipname;

    $this->load->library('zip');
    foreach ($pdfFiles as $file) {
        $this->zip->read_file($file);
    }

    // Create ZIP on server
    $this->zip->archive($zip_path);

    // Send ZIP file to browser
    if (file_exists($zip_path)) {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($zipname) . '"');
        header('Content-Length: ' . filesize($zip_path));
        readfile($zip_path);

        // After sending the ZIP, delete all files and folder
        $files = array_diff(scandir($pdfDir), ['.', '..']);
        foreach ($files as $file) {
            $path = $pdfDir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $subFiles = array_diff(scandir($path), ['.', '..']);
                foreach ($subFiles as $subFile) {
                    @unlink($path . DIRECTORY_SEPARATOR . $subFile);
                }
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($pdfDir);

        // exit; // stop further execution
    } else {
        echo "ZIP file not created!";
    }
} else {
    echo "No MOM data found to generate PDFs.";
}





}

/**
 * Recursively delete a folder and its files
 */

function deleteFolder($dir) {
    if (!is_dir($dir)) return false;

    $files = array_diff(scandir($dir), ['.', '..']);

    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            deleteFolder($path); // recursive deletion
        } else {
            @unlink($path); // delete file
        }
    }

    return @rmdir($dir); // delete folder itself
}


public function download_single_mom_pdf($momdatasID,$rowcid)
{
    // Get current user
    $user = $this->session->userdata('user');
    $uid  = $user['user_id'];

    // Fetch all meetings for the given user/date/type




                $momdata = $this->Menu_model->getRequestMOMBYID($momdatasID);
                $mom_userName = $this->Menu_model->get_userbyid($momdata[0]->user_id)[0]->name;
                $init_cmpid = $momdata[0]->init_cmpid;
                $mom_cdate = $momdata[0]->cdate;

                $cominfo = $this->Menu_model->get_cmpbyinidINFO($init_cmpid);
                $compname = $cominfo[0]->company_name;
                $company_id = $cominfo[0]->cid;

                // Load HTML view for PDF
                $html = $this->load->view('mom_pdf_template', [
                    'momdata' => $momdata,
                    't_id'    => $momdatasID,
                    'ce_id'   => $rowcid,
                    'uid'     => $uid,
                    'user'    => $user,
                    'cdate'   => date("Y-m-d"),
                ], TRUE);

                // Create safe filename
                $filename = 'MOM_' .
                    preg_replace('/\s+/', '_', $mom_userName) .
                    '_CompanyName_' .
                    preg_replace('/[\s\.]+/', '_', $compname) .
                    '_CID_' . $company_id .
                    '_MOM_Created_Date_' .
                    str_replace([' ', ':'], '_', $mom_cdate) .
                    '.pdf';

                // Output PDF directly to browser for download
                $this->pdf->createPDF($html, $filename, FALSE); // FALSE = output to browser
          
}



































}
