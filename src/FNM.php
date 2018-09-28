<?php

namespace indradevzapbuild\FNM;
/**
 * FNM.php is part of FNM library
 *
 *FNM.php is for handle FNM import and export
 *
 * @package    indradevzapbuild\FNM
 * @copyright  Copyright (c) 2014 WallaceIT. (https://wallaceit.com.au)
 * @author     Indradev Prasad <indradevprasad@zapbuild.com>
 */
use indradevzapbuild\FNM\Exception\FieldNotSetException;
use indradevzapbuild\FNM\Exception\IncorrectlyMappedConditionalException;
use indradevzapbuild\FNM\Exception\MethodNotSetException;
use indradevzapbuild\FNM\Exporter;
use indradevzapbuild\FNM\Importer;

/**
 * 
 */
class FNM {

    function __construct() {
        # code...
    }
       /**
       * @param $file path | ['fnm_file']['tmp_name']
       * @return $importedData
       *
       */
    function import($file) {
        $importObj = new Importer($file);
        return $importObj->get();
    }
      /**
       * @param $file path | ['fnm_file']['tmp_name']
       * @return $fnm file
       *
       */
    function export($get_json) {
        $data = array
            (
            "country" => "United States", //this system work for United States
                //"ssn" => "123456877",//for sample this will change once we have real ssn
        );
        if ($get_json->currently_own_real_estate != 'Y') {
            unset($get_json->real_estate_owned);
        }
        if ($get_json->former_emp_data != 'Y') {
            unset($get_json->former_employers);
        }
        if ($get_json->co_applicant_former_emp_data != 'Y') {
            unset($get_json->co_former_employers);
        }
//there need ssn for both borrower and co-borrower
        //pre -convert data for format
        $get_json->applicant_home_phone = preg_replace('/\D+/i', '', $get_json->applicant_home_phone); //remove all un wannted charactor(that we used to format)
        $get_json->employer_telephone = preg_replace('/\D+/i', '', $get_json->employer_telephone); //remove all un wannted charactor(that we used to format)
        $get_json->co_applicant_home_phone = preg_replace('/\D+/i', '', $get_json->co_applicant_home_phone); //remove all un wannted charactor(that we used to format)
        $get_json->co_applicant_employer_telephone = preg_replace('/\D+/i', '', $get_json->co_applicant_employer_telephone); //remove all un wannted charactor(that we used to format)
        $get_json->former_emp_business_phone = preg_replace('/\D+/i', '', $get_json->former_emp_business_phone); //remove all un wannted charactor(that we used to format)
        $get_json->co_former_emp_business_phone = preg_replace('/\D+/i', '', $get_json->co_former_emp_business_phone); //remove all un wannted charactor(that we used to format)
        //for mailing address
        if ($get_json->same_mailing_address == 1) {//same selected
            $get_json->mailing_street_address = $get_json->residence_street_address;
            $get_json->mailing_city = $get_json->residence_city;
            $get_json->mailing_state = $get_json->residence_state;
            $get_json->mailing_zip_code = $get_json->residence_zip_code;
            $get_json->mailing_basis_type = $get_json->residency_basis_type;
            $get_json->mailing_duration_years = $get_json->residency_duration_years;
            $get_json->mailing_duration_months = $get_json->residency_duration_months;
        }
        if ($get_json->co_applicant_same_mailing_address == 1) {//same selected
            $get_json->co_applicant_mailing_street_address = $get_json->co_applicant_residence_street_address;
            $get_json->co_applicant_mailing_city = $get_json->co_applicant_residence_city;
            $get_json->co_applicant_mailing_state = $get_json->co_applicant_residence_state;
            $get_json->co_applicant_mailing_zip_code = $get_json->co_applicant_residence_zip_code;
            $get_json->co_applicant_mailing_basis_type = $get_json->co_applicant_residency_basis_type;
            $get_json->co_applicant_mailing_duration_years = $get_json->co_applicant_residency_duration_years;
            $get_json->co_applicant_mailing_duration_months = $get_json->co_applicant_residency_duration_months;
        }
        //end here
        $applicant_ssn = preg_replace('/\D+/i', '', $get_json->applicant_ssn);
        $co_applicant_ssn = preg_replace('/\D+/i', '', $get_json->co_applicant_ssn);
        $add_more_data = array();
        $add_more_data['06C-010'][] = ['06C-020' => $applicant_ssn, '06C-030' => 'F1', '06C-110' => $get_json->asset_market_value]; //pass case market value from here bez we are using 06C row
        foreach ($get_json as $key => $value) {
            if ((is_array($value)) || (is_object($value))) {
                //further notification of array data
                if ($key == 'user') {//fetch data from user table
                    $data['email'] = $value->email;
                } else if ($key == 'former_employers') {//for former employers row of 3001(04B-010)
                    foreach ($value as $key1 => $value1) {
                        $value1 = ((is_array($value1)) || (is_object($value1)) == true) ? $value1 : []; //convert if no arrray so we can handle exception
                        $temp_array = array();
                        $temp_array['04B-020'] = $applicant_ssn;
                        foreach ($value1 as $key2 => $value2) {
                            if ($key2 == 'former_emp_name') {
                                $temp_array['04B-030'] = $value2;
                            } else if ($key2 == 'former_emp_address') {
                                $temp_array['04B-040'] = $value2;
                            } else if ($key2 == 'former_emp_state') {
                                $temp_array['04B-060'] = $value2;
                            } else if ($key2 == 'former_emp_city') {
                                $temp_array['04B-050'] = $value2;
                            } else if ($key2 == 'former_emp_zip_code') {
                                $temp_array['04B-070'] = $value2;
                            } else if ($key2 == 'former_emp_business_phone') {
                                $temp_array['04B-150'] = preg_replace('/\D+/i', '', $value2);
                            } else if ($key2 == 'former_emp_position') {
                                $temp_array['04B-140'] = $value2;
                            } else if ($key2 == 'former_emp_self_emp') {
                                $temp_array['04B-090'] = $value2;
                            } else if ($key2 == 'former_emp_date_from') {
                                //its date so format it
                                $temp_array['04B-110'] = str_replace('-', '', date("Y-m-d", strtotime($value2)));
                            } else if ($key2 == 'former_emp_date_to') {
                                //its date so formate it
                                $temp_array['04B-120'] = str_replace('-', '', date("Y-m-d", strtotime($value2)));
                            } else if ($key2 == 'former_emp_monthly_income') {
                                $temp_array['04B-130'] = $value2;
                            } else {
                                //
                            }
                        }
                        $add_more_data['04B-010'][] = $temp_array;
                    }
                } else if ($key == 'co_former_employers') {//for co-borrower former employers row of 3001(04B-010)
                    foreach ($value as $key1 => $value1) {
                        $value1 = ((is_array($value1)) || (is_object($value1)) == true) ? $value1 : []; //convert if no arrray so we can handle exception
                        $temp_array = array();
                        //$temp_array['04B-020']=$co_applicant_ssn;
                        foreach ($value1 as $key2 => $value2) {
                            if ($key2 == 'co_former_emp_name') {
                                $temp_array['04B-030'] = $value2;
                            } else if ($key2 == 'co_former_emp_address') {
                                $temp_array['04B-040'] = $value2;
                            } else if ($key2 == 'co_former_emp_state') {
                                $temp_array['04B-060'] = $value2;
                            } else if ($key2 == 'co_former_emp_city') {
                                $temp_array['04B-050'] = $value2;
                            } else if ($key2 == 'co_former_emp_zip_code') {
                                $temp_array['04B-070'] = $value2;
                            } else if ($key2 == 'co_former_emp_business_phone') {
                                $temp_array['04B-150'] = preg_replace('/\D+/i', '', $value2);
                            } else if ($key2 == 'co_former_emp_position') {
                                $temp_array['04B-140'] = $value2;
                            } else if ($key2 == 'co_former_emp_self_emp') {
                                $temp_array['04B-090'] = $value2;
                            } else if ($key2 == 'co_former_emp_date_from') {
                                //its date so format it
                                $temp_array['04B-110'] = str_replace('-', '', date("Y-m-d", strtotime($value2)));
                            } else if ($key2 == 'co_former_emp_date_to') {
                                //its date so formate it
                                $temp_array['04B-120'] = str_replace('-', '', date("Y-m-d", strtotime($value2)));
                            } else if ($key2 == 'co_former_emp_monthly_income') {
                                $temp_array['04B-130'] = $value2;
                            } else {
                                //
                            }
                        }
                        $add_more_data['04B-010'][] = $temp_array;
                    }
                } else if ($key == 'checking_and_saving_account') {//check account and saving row of 3001(06C-010)
                    foreach ($value as $key1 => $value1) {
                        $value1 = ((is_array($value1)) || (is_object($value1)) == true) ? $value1 : [];
                        $temp_array = array();
                        $temp_array['06C-020'] = $applicant_ssn;
                        foreach ($value1 as $key2 => $value2) {
                            if ($key2 == 'bank_acct_type') {
                                $temp_array['06C-030'] = $value2;
                            } else if ($key2 == 'bank_name') {
                                $temp_array['06C-040'] = $value2;
                            } else if ($key2 == 'bank_street_address') {
                                $temp_array['06C-050'] = $value2;
                            } else if ($key2 == 'bank_city') {
                                $temp_array['06C-060'] = $value2;
                            } else if ($key2 == 'bank_state') {
                                $temp_array['06C-070'] = $value2;
                            } else if ($key2 == 'bank_zip_code') {
                                $temp_array['06C-080'] = $value2;
                            } else if ($key2 == 'bank_acct_no') {
                                $temp_array['06C-100'] = $value2;
                            } else if ($key2 == 'cash_market_value') {
                                $temp_array['06C-110'] = $value2;
                            } else {
                                //
                            }
                        }
                        $add_more_data['06C-010'][] = $temp_array;
                    }
                } else if ($key == 'stocks_and_bonds') {//stock and bonds row(06C-010)
                    foreach ($value as $key1 => $value1) {
                        $value1 = ((is_array($value1)) || (is_object($value1)) == true) ? $value1 : []; //convert if no arrray so we can handle exception
                        $temp_array = array();
                        $temp_array['06C-020'] = $applicant_ssn;
                        foreach ($value1 as $key2 => $value2) {
                            if ($key2 == 'stock_asset_type') {
                                $temp_array['06C-030'] = $value2;
                            } else if ($key2 == 'institution_name') {
                                $temp_array['06C-040'] = $value2;
                            } else if ($key2 == 'stock_market_value') {
                                $temp_array['06C-110'] = $value2;
                            } else {
                                //
                            }
                        }
                        $add_more_data['06C-010'][] = $temp_array;
                    }
                } else if ($key == 'automobiles_owned') {//auto mobiles row(06D-010)
                    foreach ($value as $key1 => $value1) {
                        $value1 = ((is_array($value1)) || (is_object($value1)) == true) ? $value1 : []; //convert if no arrray so we can handle exception
                        $temp_array = array();
                        $temp_array['06D-020'] = $applicant_ssn;
                        foreach ($value1 as $key2 => $value2) {
                            if ($key2 == 'automobile_year') {
                                $temp_array['06D-040'] = $value2;
                            } else if ($key2 == 'automobile_make') {
                                $temp_array['06D-030'] = $value2;
                            } else if ($key2 == 'automobile_market_value') {
                                $temp_array['06D-050'] = $value2;
                            } else {
                                //
                            }
                        }
                        $add_more_data['06D-010'][] = $temp_array;
                    }
                } else if ($key == 'other_assets') {//other assets row(06C-010)
                    foreach ($value as $key1 => $value1) {
                        $value1 = ((is_array($value1)) || (is_object($value1)) == true) ? $value1 : []; //convert if no arrray so we can handle exception
                        $temp_array = array();
                        $temp_array['06C-020'] = $applicant_ssn;
                        foreach ($value1 as $key2 => $value2) {
                            if ($key2 == 'other_asset_type') {
                                $temp_array['06C-030'] = $value2;
                            } else if ($key2 == 'other_institution_name') {
                                $temp_array['06C-040'] = $value2;
                            } else if ($key2 == 'other_asset_value') {
                                $temp_array['06C-110'] = $value2;
                            } else {
                                //
                            }
                        }
                        $add_more_data['06C-010'][] = $temp_array;
                    }
                } else if ($key == 'outstanding_assets') {//other assets row(06L-010)
                    foreach ($value as $key1 => $value1) {
                        $value1 = ((is_array($value1)) || (is_object($value1)) == true) ? $value1 : []; //convert if no arrray so we can handle exception
                        $temp_array = array();
                        $temp_array['06L-020'] = $applicant_ssn;
                        foreach ($value1 as $key2 => $value2) {
                            if ($key2 == 'creditor_name') {
                                $temp_array['06L-040'] = $value2;
                            } else if ($key2 == 'liability_type') {
                                $temp_array['06L-030'] = $value2;
                            } else if ($key2 == 'creditor_street_address') {
                                $temp_array['06L-050'] = $value2;
                            } else if ($key2 == 'creditor_city') {
                                $temp_array['06L-060'] = $value2;
                            } else if ($key2 == 'creditor_state') {
                                $temp_array['06L-070'] = $value2;
                            } else if ($key2 == 'creditor_zip_code') {
                                $temp_array['06L-080'] = $value2;
                            } else if ($key2 == 'creditor_monthly_payment_amount') {
                                $temp_array['06L-110'] = $value2;
                            } else if ($key2 == 'creditor_unpaid_balance') {
                                $temp_array['06L-130'] = $value2;
                            } else if ($key2 == 'creditor_account_no') {
                                $temp_array['06L-100'] = $value2;
                            } else {
                                //
                            }
                        }
                        $add_more_data['06L-010'][] = $temp_array;
                    }
                } else if ($key == 'real_estate_owned') {//real estate owned row(06G-010)
                    foreach ($value as $key1 => $value1) {
                        $value1 = ((is_array($value1)) || (is_object($value1)) == true) ? $value1 : []; //convert if no arrray so we can handle exception
                        $temp_array = array();
                        $temp_array['06G-020'] = $applicant_ssn;
                        foreach ($value1 as $key2 => $value2) {
                            if ($key2 == 'property_disposition') {
                                $temp_array['06G-080'] = $value2;
                            } else if ($key2 == 'asset_property_street_address') {
                                $temp_array['06G-030'] = $value2;
                            } else if ($key2 == 'asset_property_city') {
                                $temp_array['06G-040'] = $value2;
                            } else if ($key2 == 'asset_property_state') {
                                $temp_array['06G-050'] = $value2;
                            } else if ($key2 == 'asset_property_zip_code') {
                                $temp_array['06G-060'] = $value2;
                            } else if ($key2 == 'asset_property_present_market_value') {
                                $temp_array['06G-100'] = $value2;
                            } else if ($key2 == 'asset_property_amount_of_mortgage_lien') {
                                $temp_array['06G-110'] = $value2;
                            } else if ($key2 == 'asset_property_gross_rental_income') {
                                $temp_array['06G-120'] = $value2;
                            } else if ($key2 == 'asset_property_mortgage_payment') {
                                $temp_array['06G-130'] = $value2;
                            } else if ($key2 == 'asset_property_insurance_maintenance_taxes') {
                                $temp_array['06G-140'] = $value2;
                            } else if ($key2 == 'asset_property_net_rental_income') {
                                $temp_array['06G-150'] = $value2;
                            } else {
                                //
                            }
                        }
                        $add_more_data['06G-010'][] = $temp_array;
                    }
                } else {
                    //
                }
            } else {
                //make db tata to fnm form formate
                //for - replace by ""
                if (in_array($key, ['applicant_ssn', 'applicant_home_phone', 'co_applicant_ssn', 'co_applicant_home_phone'])) {
                    $value = str_replace('-', '', $value);
                }
                //date formate yyyymmdd
                if (in_array($key, ['applicant_birth_date', 'co_applicant_birth_date'])) {
                    $value = str_replace('-', '', date("Y-m-d", strtotime($value)));
                }
                //end here
                if ($value != '') {
                    $data[$key] = $value;
                }
            }
        }
        $data['applicant_ssn'] = $applicant_ssn;
        //more data for mapping
        //real estate owned
        // $add_more_data['06G-010'][] = ['06G-020' => $applicant_ssn, '06G-100' => $get_json->real_estate_owned_market_value];
        //Vested interest in retirement fund
        $add_more_data['06C-010'][] = ['06C-020' => $applicant_ssn, '06C-030' => '08', '06C-110' => $get_json->retirement_fund_cash_value];
        //Net worth of business(es) owned
        $add_more_data['06C-010'][] = ['06C-020' => $applicant_ssn, '06C-030' => 'F8', '06C-110' => $get_json->net_worth_business_owned];
        //more cases so that we can send appropriate data to fnm file as needed
        //for us citizen borrower
        if ($get_json->dec_us_citizen == "Y") {
            $data['dec_residence_type'] = "01"; //for use citizen
        } else if ($get_json->dec_permanent_resident == "Y") {
            $data['dec_residence_type'] = "03"; //Permanent Resident-Alien
        } else {
            if ($get_json->dec_us_citizen == 'N' || $get_json->dec_permanent_resident == 'N') {
                $data['dec_residence_type'] = "05"; //No Permanent Resident-Alien   
            }
        }
        //for us citizen co borrower
        if ($get_json->co_applicant_dec_us_citizen == "Y") {
            $data['co_applicant_dec_residence_type'] = "01"; //for use citizen
        } else if ($get_json->co_applicant_dec_permanent_resident == "Y") {
            $data['co_applicant_dec_residence_type'] = "03"; //Permanent Resident-Alien
        } else {
            if ($get_json->co_applicant_dec_us_citizen == 'N' || $get_json->co_applicant_dec_permanent_resident == 'N') {
                $data['co_applicant_dec_residence_type'] = "05"; //No Permanent Resident-Alien
            }
        }
        //unset co-borrower data if there no co-borrower
        if ($get_json->is_there_co_borrower == 'N') {
            unset($data['co_applicant_dec_residence_type']);
        }
        //end here
        //send data of more income source
        if ($get_json->other_income_amount_second_check == '') {
            unset($data['other_income_code_second']);
            unset($data['other_income_amount_second']);
            unset($data['co_applicant_other_income_code_second']);
            unset($data['co_applicant_other_income_amount_second']);
        } else if ($get_json->other_income_amount_second_check == "B") {
            $data['income_applicant_ssn'] = $applicant_ssn;
            unset($data['co_applicant_other_income_code_second']);
            unset($data['co_applicant_other_income_amount_second']);
        } else {
            //$data['income_co_applicant_ssn'] = $co_applicant_ssn;
            unset($data['other_income_code_second']);
            unset($data['other_income_amount_second']);
        }
        //end here income source send data more
        $filename_base = "fnm_" . mt_rand() . "" . time();
        //pass to exported
        $exportObj = new Exporter($data, $add_more_data);
        $fnm = $exportObj->get();
        $filenameFnm = $filename_base . '.fnm';
        //download instead create file
        header('Content-type: application/octet-stream; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"$filenameFnm\"");
        echo $fnm;
        exit;
    }

    function test($param) {
        new Importer();
        return $param;
    }

}

?>