<?php

namespace indradevzapbuild\FNM;

use indradevzapbuild\FNM\Exception\FieldNotSetException;
use indradevzapbuild\FNM\Exception\IncorrectlyMappedConditionalException;
use indradevzapbuild\FNM\Exception\MethodNotSetException;

class Importer {
    /*
     * calculated data
     */

    protected $_return_data = [];
    /*
     * File path
     */
    protected $_file_path;
    /*
     * Index data for storing json
     */
    protected $_index_data = [];
    /*
     * all fields
     */
    protected $_fields = [];
    /*
     * directory path
     */
    protected $_directory_path;

    /**
     * ssl number of applicant
     */
    protected $_applicant_ssn;

    /**
     * temp array
     * *
     */
    protected $_temp_index = [];

    public function __construct($file_path) {
        // file path
        $this->_file_path = $file_path;
        //set directory path
        $this->_directory_path = dirname(__FILE__).'/bin';

        // Set empty return data
        $this->_return_data = [];
        //Set index data
        $this->_index_data = [];
        //set temp array
        $this->_temp_index = [];
        //call config
        $this->load_config();
    }

    /**
     * Load Config files
     */
    private function load_config() {
        $this->_fields = json_decode(file_get_contents($this->_directory_path . '/fnm-import.json'));
        foreach ($this->_fields as $field) {
            $this->_index_data[$field[0]->data_stream] = $field;
        }
    }

    /**
     * Start Importing
     * deal with file and break into lines
     *
     * * */
    private function start() {
        $file_data = file($this->_file_path);
        foreach ($file_data as $key => $line) {
            if (strlen($line) > 3) {
                $this->import_line($line);
            }
        }
    }

    /**
     * Import Line
     * Funtion 
     * deal with one line of file
     * * */
    private function import_line($line) {
        $row_name = substr($line, 0, 3);
        if (!empty($this->_index_data[$row_name])) {
            $this->import_data($line, $row_name, $this->_index_data[$row_name]);
        }
    }

    /**
     * Import data
     * Funtion
     * create return data array main task resolve here
     * * */
    private function import_data($line, $row_name, $row_detail) {
        $imported_array = [];
        foreach ($row_detail as $row) {
            if (is_numeric($row->position) && is_numeric($row->field_length)) {
                $imported_array[] = substr($line, $row->position - 1, $row->field_length);
            }
        }
        //magic start boom
        switch ($row_name) {
            case '01A':
                $this->_return_data['mortgage_applied_for'] = (empty($imported_array[1])) ? '' : $imported_array[1];
                $this->_return_data['loan_amount'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                $this->_return_data['agency_case_number'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                $this->_return_data['case_number'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                $this->_return_data['interest_rate'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                $this->_return_data['no_of_months'] = (empty($imported_array[7])) ? '' : $imported_array[7];
                $this->_return_data['amortization_type'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                break;
            case '02A':
                $this->_return_data['property_street_address'] = (empty($imported_array[1])) ? '' : $imported_array[1];
                $this->_return_data['property_city'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                $this->_return_data['property_state'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                $this->_return_data['property_zip_code'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                $this->_return_data['no_of_units'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                $this->_return_data['year_built'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                $this->_return_data['legal_description_of_subject_property'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                break;
            case '02B':
                $this->_return_data['purpose_of_loan'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                $this->_return_data['property_will_be'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                $this->_return_data['manner_in_which_title_will_be_held'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                $this->_return_data['estate_will_be_held_in'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                break;
            case '02E':
                $this->_return_data['down_payment_type_code'] = (empty($imported_array[1])) ? '' : $imported_array[1];
                break;
            case '02C':
                $this->_return_data['titleholder_name'] = (empty($imported_array[1])) ? '' : $imported_array[1];
                break;
            case '03A':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == 'BW') {//applicant
                    $this->_applicant_ssn = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $this->_return_data['applicant_ssn'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $this->_return_data['applicant_first_name'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $this->_return_data['applicant_middle_name'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $this->_return_data['applicant_last_name'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    $this->_return_data['applicant_generation'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                    $this->_return_data['applicant_home_phone'] = (empty($imported_array[7])) ? '' : $imported_array[7];
                    $this->_return_data['applicant_schooling_years'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                    $this->_return_data['applicant_marital_status'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    $this->_return_data['applicant_dependent_count'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                    $this->_return_data['applicant_birth_date'] = (empty($imported_array[14])) ? '' : $imported_array[14];
                    $this->_return_data['email'] = (empty($imported_array[15])) ? '' : $imported_array[15];
                } else {
                    $this->_return_data['co_applicant_ssn'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $this->_return_data['co_applicant_first_name'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $this->_return_data['co_applicant_middle_name'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $this->_return_data['co_applicant_last_name'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    $this->_return_data['co_applicant_generation'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                    $this->_return_data['co_applicant_home_phone'] = (empty($imported_array[7])) ? '' : $imported_array[7];
                    $this->_return_data['co_applicant_schooling_years'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                    $this->_return_data['co_applicant_marital_status'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    $this->_return_data['co_applicant_dependent_count'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                    $this->_return_data['co_applicant_birth_date'] = (empty($imported_array[14])) ? '' : $imported_array[14];
                    $this->_return_data['co_applicant_email'] = (empty($imported_array[15])) ? '' : $imported_array[15];
                    $this->_return_data['is_there_co_borrower'] = 'Y';
                }
                break;
            case '03B':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $this->_return_data['applicant_dependent_age'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                } else {
                    $this->_return_data['co_applicant_dependent_age'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                }
                break;
            case '03C':
                $address_type = (empty($imported_array[2])) ? '' : $imported_array[2];
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($address_type == 'ZG') {//present address
                    if ($whose_data == $this->_applicant_ssn) {//applicant
                        $this->_return_data['residence_street_address'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $this->_return_data['residence_city'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                        $this->_return_data['residence_state'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                        $this->_return_data['residence_zip_code'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                        $this->_return_data['residency_basis_type'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                        $this->_return_data['residency_duration_years'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                        $this->_return_data['residency_duration_months'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                        $this->mail_addess_helper($imported_array, 'current_address'); //later will use
                    } else {
                        $this->_return_data['co_applicant_residence_street_address'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $this->_return_data['co_applicant_residence_city'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                        $this->_return_data['co_applicant_residence_state'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                        $this->_return_data['co_applicant_residence_zip_code'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                        $this->_return_data['co_applicant_residency_basis_type'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                        $this->_return_data['co_applicant_residency_duration_years'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                        $this->_return_data['co_applicant_residency_duration_months'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                        $this->mail_addess_helper($imported_array, 'co_current_address'); //later will use
                    }
                } elseif ($address_type == 'F4') {//former address
                    if ($whose_data == $this->_applicant_ssn) {//applicant
                        $this->_return_data['former_residence_street_address'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $this->_return_data['former_residence_city'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                        $this->_return_data['former_residence_state'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                        $this->_return_data['former_residence_zip_code'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                        $this->_return_data['former_residence_basis_type'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                        $this->_return_data['former_residence_duration_years'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                        $this->_return_data['former_residence_duration_months'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                        $this->_return_data['current_location_less_than_2'] = 'Y';
                    } else {
                        $this->_return_data['co_borrower_former_residence_street_address'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $this->_return_data['co_borrower_former_residence_city'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                        $this->_return_data['co_borrower_former_residence_state'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                        $this->_return_data['co_borrower_former_residence_zip_code'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                        $this->_return_data['co_borrower_former_residence_basis_type'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                        $this->_return_data['co_borrower_former_residence_duration_years'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                        $this->_return_data['co_borrower_former_residence_duration_months'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                        $this->_return_data['co_applicant_current_location_less_than_2'] = 'Y';
                    }
                } elseif ($address_type == 'BH') {//mailing address
                    if ($whose_data == $this->_applicant_ssn) {//applicant
                        $this->_return_data['mailing_street_address'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $this->_return_data['mailing_city'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                        $this->_return_data['mailing_state'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                        $this->_return_data['mailing_zip_code'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                        $this->_return_data['mailing_basis_type'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                        $this->_return_data['mailing_duration_years'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                        $this->_return_data['mailing_duration_months'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                        $this->_return_data['mailing_country'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                        $this->mail_addess_helper($imported_array, 'mailing_address'); //later will use
                    } else {
                        $this->_return_data['co_applicant_mailing_street_address'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $this->_return_data['co_applicant_mailing_city'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                        $this->_return_data['co_applicant_mailing_state'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                        $this->_return_data['co_applicant_mailing_zip_code'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                        $this->_return_data['co_applicant_mailing_basis_type'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                        $this->_return_data['co_applicant_mailing_duration_years'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                        $this->_return_data['co_applicant_mailing_duration_months'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                        $this->_return_data['co_applicant_mailing_country'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                        $this->mail_addess_helper($imported_array, 'co_mailing_address'); //later will use
                    }
                }
                break;
            case '04A':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $this->_return_data['employer_name'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $this->_return_data['employer_street_address'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $this->_return_data['employer_city'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $this->_return_data['employer_state'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    $this->_return_data['employer_zip_code'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                    $this->_return_data['self_employed_indicator'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                    $this->_return_data['current_employment_years_duration'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                    $this->_return_data['current_employment_months_duration'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    $this->_return_data['current_employment_time_line_work_years'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                    $this->_return_data['employment_position_type'] = (empty($imported_array[12])) ? '' : $imported_array[12];
                    $this->_return_data['employer_telephone'] = (empty($imported_array[13])) ? '' : $imported_array[13];
                } else {
                    $this->_return_data['co_applicant_employer_name'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $this->_return_data['co_applicant_employer_street_address'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $this->_return_data['co_applicant_employer_city'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $this->_return_data['co_applicant_employer_state'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    $this->_return_data['co_applicant_employer_zip_code'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                    $this->_return_data['co_applicant_self_employed_indicator'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                    $this->_return_data['co_applicant_current_employment_years_duration'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                    $this->_return_data['co_applicant_current_employment_months_duration'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    $this->_return_data['co_applicant_current_employment_time_line_work_years'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                    $this->_return_data['co_applicant_employment_position_type'] = (empty($imported_array[12])) ? '' : $imported_array[12];
                    $this->_return_data['co_applicant_employer_telephone'] = (empty($imported_array[13])) ? '' : $imported_array[13];
                }
                break;
            case '04B':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $former_data_array = []; //initialize
                    $former_data_array['former_emp_name'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $former_data_array['former_emp_address'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $former_data_array['former_emp_state'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    $former_data_array['former_emp_city'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $former_data_array['former_emp_zip_code'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                    $former_data_array['former_emp_business_phone'] = (empty($imported_array[14])) ? '' : $imported_array[14];
                    $former_data_array['former_emp_position'] = (empty($imported_array[13])) ? '' : $imported_array[13];
                    $former_data_array['former_emp_self_emp'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                    $former_data_array['former_emp_date_from'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    $former_data_array['former_emp_date_to'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                    $former_data_array['former_emp_monthly_income'] = (empty($imported_array[12])) ? '' : $imported_array[12];
                    $this->_return_data['former_employers'][] = $former_data_array;
                } else {
                    $former_data_array = []; //initialize
                    $former_data_array['co_former_emp_name'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $former_data_array['co_former_emp_address'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $former_data_array['co_former_emp_state'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    $former_data_array['co_former_emp_city'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $former_data_array['co_former_emp_zip_code'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                    $former_data_array['co_former_emp_business_phone'] = (empty($imported_array[14])) ? '' : $imported_array[14];
                    $former_data_array['co_former_emp_position'] = (empty($imported_array[13])) ? '' : $imported_array[13];
                    $former_data_array['co_former_emp_self_emp'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                    $former_data_array['co_former_emp_date_from'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    $former_data_array['co_former_emp_date_to'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                    $former_data_array['co_former_emp_monthly_income'] = (empty($imported_array[12])) ? '' : $imported_array[12];
                    $this->_return_data['co_former_employers'][] = $former_data_array;
                }
                break;
            case '05I':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                $income_type = (empty($imported_array[2])) ? '' : $imported_array[2];
                if ($income_type == '20') {//base monthly
                    if ($whose_data == $this->_applicant_ssn) {//applicant
                        $this->_return_data['base_monthly_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    } else {
                        $this->_return_data['co_applicant_base_monthly_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    }
                } elseif ($income_type == '09') {//over time monthly
                    if ($whose_data == $this->_applicant_ssn) {//applicant
                        $this->_return_data['overtime_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    } else {
                        $this->_return_data['co_applicant_overtime_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    }
                } elseif ($income_type == '08') {//bonus
                    if ($whose_data == $this->_applicant_ssn) {//applicant
                        $this->_return_data['bonuses_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    } else {
                        $this->_return_data['co_applicant_bonuses_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    }
                } elseif ($income_type == '10') {//commision
                    if ($whose_data == $this->_applicant_ssn) {//applicant
                        $this->_return_data['commissions_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    } else {
                        $this->_return_data['co_applicant_commissions_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    }
                } elseif ($income_type == '17') {//divident
                    if ($whose_data == $this->_applicant_ssn) {//applicant
                        $this->_return_data['dividends_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    } else {
                        $this->_return_data['co_applicant_dividends_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    }
                } elseif ($income_type == '33') {//net 
                    if ($whose_data == $this->_applicant_ssn) {//applicant
                        $this->_return_data['net_rental_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    } else {
                        $this->_return_data['co_applicant_net_rental_income'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    }
                } else {//other income
                    if ($whose_data == $this->_applicant_ssn) {//applicant
                        $other_income_data = [];
                        $other_income_data['other_income_code'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                        $other_income_data['other_income_amount'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $this->_return_data['applicant_other_income'][] = $other_income_data;
                    } else {
                        $other_income_data = [];
                        $other_income_data['other_income_code'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                        $other_income_data['other_income_amount'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $this->_return_data['co_applicant_other_income'][] = $other_income_data;
                    }
                }
                break;
            case '05H':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                $present_or_proposed = (empty($imported_array[2])) ? '' : $imported_array[2];
                $type = (empty($imported_array[3])) ? '' : $imported_array[3];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $index = 0;
                    switch ($type) {
                        case '25'://25 = Rent
                            $index = ($present_or_proposed == '1') ? 'present_rent' : 'proposed_rent';
                            break;
                        case '26':////26 = First Mortgage P&I
                            $index = ($present_or_proposed == '1') ? 'present_first_mortgage' : 'proposed_first_mortgage';
                            break;
                        case '22'://22 = Other Financing P&I
                            $index = ($present_or_proposed == '1') ? 'present_other_financing' : 'proposed_other_financing';
                            break;
                        case '01'://01 = Hazard Insurance
                            $index = ($present_or_proposed == '1') ? 'present_hazard_insurance' : 'proposed_hazard_insurance';
                            break;
                        case '14'://14 = Real Estate Taxes
                            $index = ($present_or_proposed == '1') ? 'present_real_estate_taxes' : 'proposed_real_estate_taxes';
                            break;
                        case '02'://02 = Mortgage Insurance
                            $index = ($present_or_proposed == '1') ? 'present_mortgage_insurance' : 'proposed_mortgage_insurance';
                            break;
                        case '06'://06 = Homeowner Association Dues
                            $index = ($present_or_proposed == '1') ? 'present_homeowner_association_dues' : 'proposed_homeowner_association_dues';
                            break;
                        case '23'://23 = Other
                            $index = ($present_or_proposed == '1') ? 'other_present_dues' : 'other_proposed_dues';
                            break;
                        default:
                            # code...
                            break;
                    }
                    $this->_return_data[$index] = (empty($imported_array[4])) ? '' : $imported_array[4];
                } else {//no needed for co-applicant we dont have if in future need then here will be implemented
                }
                break;
            case '06C':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                $type = (empty($imported_array[2])) ? '' : $imported_array[2];
                $type = str_replace(' ', '', $type);
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    if ($type == '08') {//for Vested Interest in Retirement Fund
                        $this->_return_data['retirement_fund_cash_value'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    } elseif ($type == 'F8') {//for Net Worth of Business(es) Owned
                        $this->_return_data['net_worth_business_owned'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    } elseif (in_array($type, ['03', 'SG'])) {//for Checking and Saving Account
                        $account_array = [];
                        $account_array['bank_acct_type'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                        $account_array['bank_name'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $account_array['bank_street_address'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                        $account_array['bank_city'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                        $account_array['bank_state'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                        $account_array['bank_zip_code'] = (empty($imported_array[7])) ? '' : $imported_array[7];
                        $account_array['bank_acct_no'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                        $account_array['cash_market_value'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                        $this->_return_data['checking_and_saving_account'][] = $account_array;
                    } elseif (in_array($type, ['F4', '05', '06'])) {//for stock and bond
                        $stock_bond_array = [];
                        $stock_bond_array['stock_asset_type'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                        $stock_bond_array['institution_name'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $stock_bond_array['stock_market_value'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                        $this->_return_data['stocks_and_bonds'][] = $stock_bond_array;
                    } elseif (in_array($type, ['01', 'F3', 'F5', 'F1', 'F7', '11', 'F2', 'M1'])) {//for other assets
                        if ($type == 'F1' && empty($this->_return_data['asset_market_value'])) {
                            $this->_return_data['asset_market_value'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                        } else {
                            $other_assets = [];
                            $other_assets['stock_asset_type'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                            $other_assets['institution_name'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                            $other_assets['stock_market_value'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                            $this->_return_data['other_assets'][] = $other_assets;
                        }
                    }
                } else {//no needed for co-applicant we dont have if in future need then here will be implemented
                }
                break;
            case '06G':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $real_estates = [];
                    $real_estates['property_disposition'] = (empty($imported_array[7])) ? '' : $imported_array[7];
                    $real_estates['asset_property_street_address'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $real_estates['asset_property_city'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $real_estates['asset_property_state'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $real_estates['asset_property_zip_code'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    $real_estates['asset_property_present_market_value'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                    $real_estates['asset_property_amount_of_mortgage_lien'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    $real_estates['asset_property_gross_rental_income'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                    $real_estates['asset_property_mortgage_payment'] = (empty($imported_array[12])) ? '' : $imported_array[12];
                    $real_estates['asset_property_insurance_maintenance_taxes'] = (empty($imported_array[13])) ? '' : $imported_array[13];
                    $real_estates['asset_property_net_rental_income'] = (empty($imported_array[14])) ? '' : $imported_array[14];
                    $this->_return_data['real_estate_owned'][] = $real_estates;
                } else {//no needed for co-applicant we dont have if in future need then here will be implemented
                }
                break;
            case '06B':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $this->_return_data['life_insurance_cash_value'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $this->_return_data['life_insurance_face_value'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                } else {//no needed for co-applicant we dont have if in future need then here will be implemented
                }
                break;
            case '06D':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $automobiles = [];
                    $automobiles['automobile_year'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $automobiles['automobile_make'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $automobiles['automobile_market_value'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $this->_return_data['automobiles_owned'][] = $automobiles;
                } else {//no needed for co-applicant we dont have if in future need then here will be implemented
                }
                break;
            case '06L':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $outstanding = [];
                    $outstanding['creditor_name'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $outstanding['liability_type'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $outstanding['creditor_street_address'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $outstanding['creditor_city'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    $outstanding['creditor_state'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                    $outstanding['creditor_zip_code'] = (empty($imported_array[7])) ? '' : $imported_array[7];
                    $outstanding['creditor_monthly_payment_amount'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    $outstanding['creditor_unpaid_balance'] = (empty($imported_array[12])) ? '' : $imported_array[12];
                    $outstanding['creditor_account_no'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                    $this->_return_data['outstanding_assets'][] = $outstanding;
                } else {//no needed for co-applicant we dont have if in future need then here will be implemented
                }
                break;
            case '06F':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $type = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $type = str_replace(' ', '', $type);
                    if ($type == 'DR' || $type == 'DT') {//Alimony Payment/Child Support Payment
                        $this->_return_data['alimony_payment'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                        $this->_return_data['alimony_payment_month_left'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                        $this->_return_data['alimony_payment_text'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    } elseif ($type == 'DZ') {//Job Related Expense (child care, union dues, etc.)
                        if (isset($this->_return_data['job_related_expense'])) {//we need as point software
                            $this->_return_data['separate_maintenance_payment'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                            $this->_return_data['separate_maintenance_payment_month_left'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                            $this->_return_data['separate_maintenance_payment_text'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                        } else {
                            $this->_return_data['job_related_expense'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                            $this->_return_data['job_related_expense_month_left'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                            $this->_return_data['job_related_expense_text'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                        }
                    }
                } else {//no needed for co-applicant we dont have if in future need then here will be implemented
                }
                break;
            case '07A':
                $this->_return_data['transaction_purchase_price'] = (empty($imported_array[1])) ? '' : $imported_array[1];
                $this->_return_data['transaction_after_imprvt_repair'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                $this->_return_data['transaction_land'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                $this->_return_data['transaction_refinance'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                $this->_return_data['estimated_prepaid_items'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                $this->_return_data['estimated_closing_cost'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                $this->_return_data['pmi_mip_funding_fee'] = (empty($imported_array[7])) ? '' : $imported_array[7];
                $this->_return_data['transaction_discount'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                $this->_return_data['subordinate_financing'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                $this->_return_data['applicant_closing_cost_paid_by_seller'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                $this->_return_data['pmi_mip_funding_fee_financed'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                break;
            case '07B':
                $this->_return_data['other_credit_type_code'] = (empty($imported_array[1])) ? '' : $imported_array[1];
                $this->_return_data['amount_of_other_credit'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                break;
            case '08A':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $this->_return_data['dec_outstanding_judgement'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $this->_return_data['dec_bankrupt'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $this->_return_data['dec_property_foreclosed'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $this->_return_data['dec_lawsuit'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    $this->_return_data['dec_obligated_loan'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                    $this->_return_data['dec_delinquent'] = (empty($imported_array[7])) ? '' : $imported_array[7];
                    $this->_return_data['dec_obligated_alimony'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                    $this->_return_data['dec_down_payment_borrowed'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                    $this->_return_data['dec_co_maker'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    $this->_return_data['dec_residence_type'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                    $this->_return_data['dec_property_primary_residence'] = (empty($imported_array[12])) ? '' : $imported_array[12];
                    $this->_return_data['dec_ownership_interest'] = (empty($imported_array[13])) ? '' : $imported_array[13];
                    $this->_return_data['dec_type_of_property'] = (empty($imported_array[14])) ? '' : $imported_array[14];
                    $this->_return_data['dec_hold_title_to_the_home'] = (empty($imported_array[15])) ? '' : $imported_array[15];
                } else {
                    $this->_return_data['co_applicant_dec_outstanding_judgement'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $this->_return_data['co_applicant_dec_bankrupt'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $this->_return_data['co_applicant_dec_property_foreclosed'] = (empty($imported_array[4])) ? '' : $imported_array[4];
                    $this->_return_data['co_applicant_dec_lawsuit'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                    $this->_return_data['co_applicant_dec_obligated_loan'] = (empty($imported_array[6])) ? '' : $imported_array[6];
                    $this->_return_data['co_applicant_dec_delinquent'] = (empty($imported_array[7])) ? '' : $imported_array[7];
                    $this->_return_data['co_applicant_dec_obligated_alimony'] = (empty($imported_array[8])) ? '' : $imported_array[8];
                    $this->_return_data['co_applicant_dec_down_payment_borrowed'] = (empty($imported_array[9])) ? '' : $imported_array[9];
                    $this->_return_data['co_applicant_dec_co_maker'] = (empty($imported_array[10])) ? '' : $imported_array[10];
                    $this->_return_data['co_applicant_dec_residence_type'] = (empty($imported_array[11])) ? '' : $imported_array[11];
                    $this->_return_data['co_applicant_dec_property_primary_residence'] = (empty($imported_array[12])) ? '' : $imported_array[12];
                    $this->_return_data['co_applicant_dec_ownership_interest'] = (empty($imported_array[13])) ? '' : $imported_array[13];
                    $this->_return_data['co_applicant_dec_type_of_property'] = (empty($imported_array[14])) ? '' : $imported_array[14];
                    $this->_return_data['co_applicant_dec_hold_title_to_the_home'] = (empty($imported_array[15])) ? '' : $imported_array[15];
                }
                break;
            case '08B':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                $type = (empty($imported_array[2])) ? '' : $imported_array[2];
                $index = 0;
                switch ($type) {
                    case '91':
                        $index = ($whose_data == $this->_applicant_ssn) ? 'dec_outstanding_judgement_desc' : 'co_applicant_dec_outstanding_judgement_desc';
                        break;
                    case '92':
                        $index = ($whose_data == $this->_applicant_ssn) ? 'dec_bankrupt_desc' : 'co_applicant_dec_bankrupt_desc';
                        break;
                    case '93':
                        $index = ($whose_data == $this->_applicant_ssn) ? 'dec_property_foreclosed_desc' : 'co_applicant_dec_property_foreclosed_desc';
                        break;
                    case '94':
                        $index = ($whose_data == $this->_applicant_ssn) ? 'dec_lawsuit_desc' : 'co_applicant_dec_lawsuit_desc';
                        break;
                    case '95':
                        $index = ($whose_data == $this->_applicant_ssn) ? 'dec_obligated_loan_desc' : 'co_applicant_dec_obligated_loan_desc';
                        break;
                    case '96':
                        $index = ($whose_data == $this->_applicant_ssn) ? 'dec_delinquent_desc' : 'co_applicant_dec_delinquent_desc';
                        break;
                    case '97':
                        $index = ($whose_data == $this->_applicant_ssn) ? 'dec_obligated_alimony_desc' : 'co_applicant_dec_obligated_alimony_desc';
                        break;
                    case '98':
                        $index = ($whose_data == $this->_applicant_ssn) ? 'dec_down_payment_borrowed_desc' : 'co_applicant_dec_down_payment_borrowed_desc';
                        break;
                    case '99':
                        $index = ($whose_data == $this->_applicant_ssn) ? 'dec_co_maker_desc' : 'co_applicant_dec_co_maker_desc';
                        break;
                    default:
                        # code...
                        break;
                }
                $this->_return_data[$index] = (empty($imported_array[3])) ? '' : $imported_array[3];
                break;
            case '10A':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $this->_return_data['dec_do_not_wish_furnish_information'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $this->_return_data['dec_enthnicity'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $this->_return_data['dec_sex'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                } else {
                    $this->_return_data['co_applicant_dec_do_not_wish_furnish_information'] = (empty($imported_array[2])) ? '' : $imported_array[2];
                    $this->_return_data['co_applicant_dec_enthnicity'] = (empty($imported_array[3])) ? '' : $imported_array[3];
                    $this->_return_data['co_applicant_dec_sex'] = (empty($imported_array[5])) ? '' : $imported_array[5];
                }
                break;
            case '10R':
                $whose_data = (empty($imported_array[1])) ? '' : $imported_array[1];
                if ($whose_data == $this->_applicant_ssn) {//applicant
                    $this->_return_data['dec_races'][] = (empty($imported_array[2])) ? '' : $imported_array[2];
                } else {
                    $this->_return_data['co_applicant_dec_races'][] = (empty($imported_array[2])) ? '' : $imported_array[2];
                }
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * For same mailing address helper
     * function
     * * */
    private function mail_addess_helper($imported_array, $address_index) {
        $string = '';
        foreach ($imported_array as $val) {
            $string.=trim($val);
        }
        $this->_temp_index[$address_index] = $string;
    }

    /**
     * data formate as we needed in database
     * function
     * @return variable
     * * */
    private function formate_data($name, $data) {
        $data = str_replace(' ', '', $data); //remove space that paded during generate FNM file
        $return = '';
        switch ($name) {
            case 'phone':
                for ($i = 0; $i < 11; $i++) {
                    if (isset($data[$i])) {
                        if ($i == 0) {
                            $return.='(' . $data[$i];
                        } elseif ($i == 2) {
                            $return.=$data[$i] . ')-';
                        } elseif ($i == 6) {
                            $return.='-' . $data[$i];
                        } else {
                            $return.='' . $data[$i];
                        }
                    }
                }
                break;
            case 'ssn':
                for ($i = 0; $i < 9; $i++) {
                    if (isset($data[$i])) {
                        if ($i == 3) {
                            $return.='-' . $data[$i];
                        } elseif ($i == 5) {
                            $return.='-' . $data[$i];
                        } else {
                            $return.=$data[$i];
                        }
                    }
                }
                break;
            case 'date':
                for ($i = 0; $i < 8; $i++) {
                    if (isset($data[$i])) {
                        if ($i == 4) {
                            $return.='-' . $data[$i];
                        } elseif ($i == 6) {
                            $return.='-' . $data[$i];
                        } else {
                            $return.=$data[$i];
                        }
                    }
                }
                break;

            default:
                # code...
                break;
        }
        return $return;
    }

    /**
     * beautify data
     * function
     * @update return data
     * * */
    private function beautify_data() {
        foreach ($this->_return_data as $key => $data) {
            if ($key == 'applicant_other_income') {
                foreach ($data as $value) {
                    if (!isset($this->_return_data['other_income_code'])) {
                        $this->_return_data['other_income_code'] = (empty($value['other_income_code'])) ? '' : $value['other_income_code'];
                        $this->_return_data['other_income_amount'] = (empty($value['other_income_amount'])) ? '' : $value['other_income_amount'];
                    } else {
                        $this->_return_data['other_income_code_second'] = (empty($value['other_income_code'])) ? '' : $value['other_income_code'];
                        $this->_return_data['other_income_amount_second'] = (empty($value['other_income_amount'])) ? '' : $value['other_income_amount'];
                    }
                }
                if (count($data) > 1) {
                    $this->_return_data['other_income_amount_second_check'] = 'B';
                }
            } elseif ($key == 'co_applicant_other_income') {
                foreach ($data as $value) {
                    if (!isset($this->_return_data['co_applicant_other_income_code'])) {
                        $this->_return_data['co_applicant_other_income_code'] = (empty($value['other_income_code'])) ? '' : $value['other_income_code'];
                        $this->_return_data['co_applicant_other_income_amount'] = (empty($value['other_income_amount'])) ? '' : $value['other_income_amount'];
                    } else {
                        $this->_return_data['co_applicant_other_income_code_second'] = (empty($value['other_income_code'])) ? '' : $value['other_income_code'];
                        $this->_return_data['co_applicant_other_income_amount_second'] = (empty($value['other_income_amount'])) ? '' : $value['other_income_amount'];
                    }
                }
                if (count($data) > 1) {
                    $this->_return_data['other_income_amount_second_check'] = 'C';
                }
            } elseif ($key == 'dec_races') {
                foreach ($data as $k => $value) {
                    $index = $k + 1;
                    $this->_return_data['dec_race_' . $index] = $value;
                }
            } elseif ($key == 'co_applicant_dec_races') {
                foreach ($data as $k => $value) {
                    $index = $k + 1;
                    $this->_return_data['co_applicant_dec_race_' . $index] = $value;
                }
            } elseif (in_array($key, ['applicant_ssn', 'co_applicant_ssn'])) {
                $this->_return_data[$key] = $this->formate_data('ssn', $data);
            } elseif (in_array($key, ['applicant_home_phone', 'employer_telephone', 'co_applicant_home_phone', 'co_applicant_employer_telephone', 'former_emp_business_phone', 'co_former_emp_business_phone'])) {
                $this->_return_data[$key] = $this->formate_data('phone', $data);
            } elseif ($key == 'former_employers') {
                foreach ($data as $k => $value) {
                    $value['former_emp_business_phone'] = $this->formate_data('phone', $value['former_emp_business_phone']);
                    $value['former_emp_date_from'] = $this->formate_data('date', $value['former_emp_date_from']);
                    $value['former_emp_date_to'] = $this->formate_data('date', $value['former_emp_date_to']);
                    $this->_return_data[$key][$k] = $value;
                }
            } elseif ($key == 'co_former_employers') {
                foreach ($data as $k => $value) {
                    $value['co_former_emp_business_phone'] = $this->formate_data('phone', $value['co_former_emp_business_phone']);
                    $value['co_former_emp_date_from'] = $this->formate_data('date', $value['co_former_emp_date_from']);
                    $value['co_former_emp_date_to'] = $this->formate_data('date', $value['co_former_emp_date_to']);
                    $this->_return_data[$key][$k] = $value;
                }
            } elseif (in_array($key, ['applicant_birth_date', 'co_applicant_birth_date'])) {
                $this->_return_data[$key] = $this->formate_data('date', $data);
            }
        }
        //same mailing address 
        if (!empty($this->_temp_index['current_address']) && !empty($this->_temp_index['mailing_address'])) {
            if ($this->_temp_index['current_address'] == $this->_temp_index['mailing_address']) {
                $this->_return_data['same_mailing_address'] = '1';
            } else {
                $this->_return_data['same_mailing_address'] = '0';
            }
        } else {
            $this->_return_data['same_mailing_address'] = '1';
        }
        if (!empty($this->_temp_index['co_current_address']) && !empty($this->_temp_index['co_mailing_address'])) {
            if ($this->_temp_index['co_current_address'] == $this->_temp_index['co_mailing_address']) {
                $this->_return_data['co_applicant_same_mailing_address'] = '1';
            } else {
                $this->_return_data['co_applicant_same_mailing_address'] = '0';
            }
        } else {
            $this->_return_data['co_applicant_same_mailing_address'] = '1';
        }
        //have real estate
        if (!empty($this->_return_data['real_estate_owned'])) {
            $this->_return_data['currently_own_real_estate'] = 'Y';
        }
        //former employees
        if (!empty($this->_return_data['former_employers'])) {
            $this->_return_data['former_emp_data'] = 'Y';
        }
        if (!empty($this->_return_data['co_former_employers'])) {
            $this->_return_data['co_applicant_former_emp_data'] = 'Y';
        }
        //residence type
        if (!empty($this->_return_data['dec_residence_type'])) {
            if (trim($this->_return_data['dec_residence_type']) == '01') {
                $this->_return_data['dec_us_citizen'] = 'Y';
            } elseif (trim($this->_return_data['dec_residence_type']) == '03') {
                $this->_return_data['dec_permanent_resident'] = 'Y';
            }
        }
        if (!empty($this->_return_data['co_applicant_dec_residence_type'])) {
            if (trim($this->_return_data['co_applicant_dec_residence_type']) == '01') {
                $this->_return_data['co_applicant_dec_us_citizen'] = 'Y';
            } elseif (trim($this->_return_data['co_applicant_dec_residence_type']) == '03') {
                $this->_return_data['co_applicant_dec_permanent_resident'] = 'Y';
            }
        }
        // more beautify delete temp index
        unset($this->_return_data['applicant_other_income']);
        unset($this->_return_data['co_applicant_other_income']);
        unset($this->_return_data['dec_races']);
        unset($this->_return_data['co_applicant_dec_races']);
    }

    /**
     * Import file
     * function initiator
     * @return Array
     */
    public function get() {
        $this->start();
        $this->beautify_data();
        return json_decode(json_encode($this->_return_data));
    }

}
