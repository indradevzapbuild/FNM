<?php
require_once 'ijaracdc-fnm.php';
error_reporting(E_ALL);
ini_set('display_errors', 2);
$get_json=json_decode(file_get_contents("sample11.json"));
// echo "<pre>";
// print_r($get_json);die;
$data =array
(
    "country" => "United States",//this system work for United States
    //"ssn" => "123456877",//for sample this will change once we have real ssn
);
//there need ssn for both borrower and co-borrower
$applicant_ssn="123456877";
$co_applicant_ssn="123456877";
 $add_more_data=array();
       foreach($get_json as $key=>$value){
			if((is_array($value))||(is_object($value)))
			{
                   //further notification of array data
				  if($key=='user')//fetch data from user table
				  {
                       $data['email']=$value->email;
				  } else if($key=='former_employers'){//for former employers row of 3001(04B-010)
				  	        $temp_array=array();
                           foreach($value as $key1=>$value1)
                           {
                           	      $temp_array['04B-020']=$applicant_ssn;
                           	    foreach ($value1 as $key2 => $value2) {
                           	        if($key2=='former_emp_name'){
                                      $temp_array['04B-030']=$value2;
                           	        } else if($key2=='former_emp_address'){
                                      $temp_array['04B-040']=$value2;
                           	        }else if($key2=='former_emp_state'){
                                      $temp_array['04B-060']=$value2;
                           	        }else if($key2=='former_emp_city'){
                                      $temp_array['04B-050']=$value2;
                           	        }else if($key2=='former_emp_zip_code'){
                                      $temp_array['04B-070']=$value2;
                           	        }else if($key2=='former_emp_business_phone'){
                                        $temp_array['04B-150']=$value2;
                           	        }else if($key2=='former_emp_position'){
                                        $temp_array['04B-140']=$value2;
                           	        }else if($key2=='former_emp_self_emp'){
                                         $temp_array['04B-090']=$value2;
                           	        }else if($key2=='former_emp_date_from'){
                           	        	//its date so format it
                                         $temp_array['04B-110']=str_replace('-', '', date("Y-m-d", strtotime($value2)));
                           	        }else if($key2=='former_emp_date_to'){
                           	        	//its date so formate it
                                         $temp_array['04B-120']=str_replace('-', '', date("Y-m-d", strtotime($value2)));
                           	        }else if($key2=='former_emp_monthly_income'){
                                          $temp_array['04B-130']=$value2;
                           	        } else{
                                      //
                           	        }
                           	    }
                           	    $add_more_data['04B-010'][]=$temp_array;
                           }
				    } else if($key=='co_former_employers'){//for co-borrower former employers row of 3001(04B-010)
				  	        $temp_array=array();
                           foreach($value as $key1=>$value1)
                           {
                           	      //$temp_array['04B-020']=$co_applicant_ssn;
                           	    foreach ($value1 as $key2 => $value2) {
                           	        if($key2=='co_former_emp_name'){
                                      $temp_array['04B-030']=$value2;
                           	        } else if($key2=='co_former_emp_address'){
                                      $temp_array['04B-040']=$value2;
                           	        }else if($key2=='co_former_emp_state'){
                                      $temp_array['04B-060']=$value2;
                           	        }else if($key2=='co_former_emp_city'){
                                      $temp_array['04B-050']=$value2;
                           	        }else if($key2=='co_former_emp_zip_code'){
                                      $temp_array['04B-070']=$value2;
                           	        }else if($key2=='co_former_emp_business_phone'){
                                        $temp_array['04B-150']=$value2;
                           	        }else if($key2=='co_former_emp_position'){
                                        $temp_array['04B-140']=$value2;
                           	        }else if($key2=='co_former_emp_self_emp'){
                                         $temp_array['04B-090']=$value2;
                           	        }else if($key2=='co_former_emp_date_from'){
                           	        	//its date so format it
                                         $temp_array['04B-110']=str_replace('-', '', date("Y-m-d", strtotime($value2)));
                           	        }else if($key2=='co_former_emp_date_to'){
                           	        	//its date so formate it
                                         $temp_array['04B-120']=str_replace('-', '', date("Y-m-d", strtotime($value2)));
                           	        }else if($key2=='co_former_emp_monthly_income'){
                                          $temp_array['04B-130']=$value2;
                           	        } else{
                                      //
                           	        }
                           	    }
                           	    $add_more_data['04B-010'][]=$temp_array;
                           }
				    } else if($key=='checking_and_saving_account'){//check account and saving row of 3001(06C-010)
				  	        $temp_array=array();
                           foreach($value as $key1=>$value1)
                           {
                           	      $temp_array['06C-020']=$applicant_ssn;
                           	    foreach ($value1 as $key2 => $value2) {
                           	        if($key2=='bank_acct_type'){
                                      $temp_array['06C-030']=$value2;
                           	        } else if($key2=='bank_name'){
                                      $temp_array['06C-040']=$value2;
                           	        }else if($key2=='bank_street_address'){
                                      $temp_array['06C-050']=$value2;
                           	        }else if($key2=='bank_city'){
                                      $temp_array['06C-060']=$value2;
                           	        }else if($key2=='bank_state'){
                                      $temp_array['06C-070']=$value2;
                           	        }else if($key2=='bank_zip_code'){
                                        $temp_array['06C-080']=$value2;
                           	        }else if($key2=='bank_acct_no'){
                                        $temp_array['06C-100']=$value2;
                           	        }else if($key2=='cash_market_value'){
                                         $temp_array['06C-110']=$value2;
                           	        }else{
                                      //
                           	        }
                           	    }
                           	    $add_more_data['06C-010'][]=$temp_array;
                           }
				    } else if($key=='stocks_and_bonds'){//stock and bonds row(06C-010)
				  	        $temp_array=array();
                           foreach($value as $key1=>$value1)
                           {
                           	      $temp_array['06C-020']=$applicant_ssn;
                           	    foreach ($value1 as $key2 => $value2) {
                           	        if($key2=='stock_asset_type'){
                                      $temp_array['06C-030']=$value2;
                           	        } else if($key2=='institution_name'){
                                      $temp_array['06C-040']=$value2;
                           	        }else if($key2=='stock_market_value'){
                                         $temp_array['06C-110']=$value2;
                           	        }else{
                                      //
                           	        }
                           	    }
                           	    $add_more_data['06C-010'][]=$temp_array;
                           }
				    }else if($key=='automobiles_owned'){//auto mobiles row(06D-010)
				  	        $temp_array=array();
                           foreach($value as $key1=>$value1)
                           {
                           	      $temp_array['06D-020']=$applicant_ssn;
                           	    foreach ($value1 as $key2 => $value2) {
                           	        if($key2=='automobile_year'){
                                      $temp_array['06D-040']=$value2;
                           	        } else if($key2=='automobile_make'){
                                      $temp_array['06D-030']=$value2;
                           	        }else if($key2=='automobile_market_value'){
                                         $temp_array['06D-050']=$value2;
                           	        }else{
                                      //
                           	        }
                           	    }
                           	    $add_more_data['06D-010'][]=$temp_array;
                           }
				    }else if($key=='other_assets'){//other assets row(06C-010)
				  	        $temp_array=array();
                           foreach($value as $key1=>$value1)
                           {
                           	      $temp_array['06C-020']=$applicant_ssn;
                           	    foreach ($value1 as $key2 => $value2) {
                           	        if($key2=='other_asset_type'){
                                      $temp_array['06C-030']=$value2;
                           	        } else if($key2=='other_institution_name'){
                                      $temp_array['06C-040']=$value2;
                           	        }else if($key2=='other_asset_value'){
                                         $temp_array['06C-110']=$value2;
                           	        }else{
                                      //
                           	        }
                           	    }
                           	    $add_more_data['06C-010'][]=$temp_array;
                           }
				    }else if($key=='outstanding_assets'){//other assets row(06L-010)
				  	        $temp_array=array();
                           foreach($value as $key1=>$value1)
                           {
                           	      $temp_array['06L-020']=$applicant_ssn;
                           	    foreach ($value1 as $key2 => $value2) {
                           	        if($key2=='creditor_name'){
                                      $temp_array['06L-040']=$value2;
                           	        } else if($key2=='liability_type'){
                                      $temp_array['06L-030']=$value2;
                           	        }else if($key2=='creditor_street_address'){
                                         $temp_array['06L-050']=$value2;
                           	        }else if($key2=='creditor_city'){
                                         $temp_array['06L-060']=$value2;
                           	        }else if($key2=='creditor_state'){
                                         $temp_array['06L-070']=$value2;
                           	        }else if($key2=='creditor_zip_code'){
                                         $temp_array['06L-080']=$value2;
                           	        }else if($key2=='creditor_monthly_payment_amount'){
                                         $temp_array['06L-110']=$value2;
                           	        }else if($key2=='creditor_unpaid_balance'){
                                         $temp_array['06L-130']=$value2;
                           	        }else if($key2=='creditor_account_no'){
                                         $temp_array['06L-100']=$value2;
                           	        }else{
                                      //
                           	        }
                           	    }
                           	    $add_more_data['06L-010'][]=$temp_array;
                           }
				    }else if($key=='real_estate_owned'){//real estate owned row(06G-010)
				  	        $temp_array=array();
                           foreach($value as $key1=>$value1)
                           {
                           	      $temp_array['06G-020']=$applicant_ssn;
                           	    foreach ($value1 as $key2 => $value2) {
                           	        if($key2=='property_disposition'){
                                      $temp_array['06G-080']=$value2;
                           	        } else if($key2=='asset_property_street_address'){
                                      $temp_array['06G-030']=$value2;
                           	        }else if($key2=='asset_property_city'){
                                         $temp_array['06G-040']=$value2;
                           	        }else if($key2=='asset_property_state'){
                                         $temp_array['06G-050']=$value2;
                           	        }else if($key2=='asset_property_zip_code'){
                                         $temp_array['06G-060']=$value2;
                           	        }else if($key2=='asset_property_present_market_value'){
                                         $temp_array['06G-100']=$value2;
                           	        }else if($key2=='asset_property_amount_of_mortgage_lien'){
                                         $temp_array['06G-110']=$value2;
                           	        }else if($key2=='asset_property_gross_rental_income'){
                                         $temp_array['06G-120']=$value2;
                           	        }else if($key2=='asset_property_mortgage_payment'){
                                         $temp_array['06G-130']=$value2;
                           	        }else if($key2=='asset_property_insurance_maintenance_taxes'){
                                         $temp_array['06G-140']=$value2;
                           	        }else if($key2=='asset_property_net_rental_income'){
                                         $temp_array['06G-150']=$value2;
                           	        }else{
                                      //
                           	        }
                           	    }
                           	    $add_more_data['06G-010'][]=$temp_array;
                           }
				    }
				  else{
				  	//
				  }
			}
			else{
						//make db tata to fnm form formate
						//for - replace by ""
						if(in_array($key, ['applicant_ssn','applicant_home_phone','co_applicant_ssn','co_applicant_home_phone']))
						{
						   $value=str_replace('-', '', $value);	
						}
						//date formate yyyymmdd
						if(in_array($key, ['applicant_birth_date','co_applicant_birth_date']))
						{
						   $value= str_replace('-', '', date("Y-m-d", strtotime($value)));
						}
						   //end here
						if($value!=''){
                            $data[$key]=$value;
						}
			}
       }
       $data['applicant_ssn']=$applicant_ssn;
       //more data for mapping
       //real estate owned
     $add_more_data['06G-010'][]=['06G-020'=>$applicant_ssn,'06G-100'=>$get_json->real_estate_owned_market_value];
        //Vested interest in retirement fund
       $add_more_data['06C-010'][]=['06C-020'=>$applicant_ssn,'06C-030'=>'08','06C-110'=>$get_json->retirement_fund_cash_value];
       //Net worth of business(es) owned
        $add_more_data['06C-010'][]=['06C-020'=>$applicant_ssn,'06C-030'=>'F8','06C-110'=>$get_json->net_worth_business_owned];
        //more cases so that we can send appropriate data to fnm file as needed
        //for us citizen borrower
        if($get_json->dec_us_citizen=="Y"){
           $data['dec_residence_type']="01";//for use citizen
        } else if($get_json->dec_permanent_resident=="Y"){
           $data['dec_residence_type']="03";//Permanent Resident-Alien
        } else{
             $data['dec_residence_type']="05";//No Permanent Resident-Alien
        }
                //for us citizen co borrower
        if($get_json->co_applicant_dec_us_citizen=="Y"){
           $data['co_applicant_dec_residence_type']="01";//for use citizen
        } else if($get_json->co_applicant_dec_permanent_resident=="Y"){
           $data['co_applicant_dec_residence_type']="03";//Permanent Resident-Alien
        } else{
             $data['co_applicant_dec_residence_type']="05";//No Permanent Resident-Alien
        }
       //end here
       // echo "<pre>";
       //  print_r($add_more_data);
       // print_r($data);die;
$filename_base = "filename_".mt_rand()."_".time();
$fnm = getFnm($data,$add_more_data);
$filenameFnm = $filename_base . '.fnm';
$fnmFile = "fnm_files/".$filenameFnm;
file_put_contents($fnmFile, $fnm);
chmod($fnmFile, 0666);

echo 'File Created';

?>
