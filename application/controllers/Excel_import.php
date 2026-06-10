<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Excel_import extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('excel_import_model');
		$this->load->library('excel');
	}

	

	function import()
	{
	    $bdid= $this->input->post('bdid');
		if(isset($_FILES["excel"]["name"]))
		{
			$path = $_FILES["excel"]["tmp_name"];
			$object = PHPExcel_IOFactory::load($path);
			foreach($object->getWorksheetIterator() as $worksheet)
			{
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				for($row=2; $row<=$highestRow; $row++)
				{
					$compname = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
					$website = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$country = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$city = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$state = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$draft = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$address = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$ctype = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
					$budget = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
					$compconname = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
					$emailid = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
					$phoneno = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
					$designation = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
					$top_spender = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
					$upsell_client = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
					$focus_funnel = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
					$data[] = array(
						'compname' => $compname,
                        'website' => $website,
                        'country' => $country,
                        'city' => $city,
                        'state' => $state,
                        'draft' => $draft,
                        'address' => $address,
                        'ctype' => $ctype,
                        'budget' => $budget,
                        'compconname' => $compconname,
                        'emailid' => $emailid,
                        'phoneno' => $phoneno,
                        'designation' => $designation,
                        'top_spender' => $top_spender,
                        'upsell_client' => $upsell_client,
                        'focus_funnel' => $focus_funnel
					);
				}
			}
			
			$this->excel_import_model->insert($data,$bdid);
			redirect('Menu/NewLead/');
			
		}	
	}
}

?>