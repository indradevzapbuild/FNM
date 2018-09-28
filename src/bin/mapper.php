<?php

return [
    'static' => [
        // Envelope Header
        // Envelope Control Number
        'EH-050' => '0',
        // Transaction Header
        // Transaction ID (Static EDI Value)
        'TH-020' => 'T100099-002',
        // Transaction Header Control Number
        'TH-030' => '1',
        // Transaction Processing Info
        // Version
        'TPI-020' => ' 1.00',
        // Identifier Code Type
        'TPI-030' => '01',
        // Always going to be new submit
        'TPI-050' => 'N',
        // File Identification
        // File Version
        '000-030' => '3.20',
        // Working Copy
        '000-040' => 'W',
        // Record ID: Top of form
        //Additional Loan Asset Considerations
        '00A-020' => 'N',
        '00A-030' => 'N',
        //Fixed Rate
        //'01A-090' => '05',
        'TT-020' => '1', // Matches: TH-030
        'ET-020' => '0', // Matches: EH-050
        // Number of units being financed (Always One)
        //'02A-070' => '1',
        // Type of legal description always F1 => Other
        '02A-080' => 'F1',
        // Default to primary residence
        '02B-050' => '1',
        // Application always taken over telephone
        //'10B-020' => 'T',
        //'10B-050' => '8004518810',
        //'10B-060' => 'Interest Smart Home Loans',
        //'10B-070' => '23172 PLAZA POINTE DR',
        //'10B-090' => 'SUITE 185',
        //'10B-100' => 'CA',
        //'10B-110' => '92653',
        // Appraisal cost
        '99B-040' => '0',
        'PID-010' => 'PID',
        'PCH-020' => '360',
        //static data added here custom
        '03A-020' => 'BW', //applicant data //indicator
        '03A-020-1' => 'QZ', //co-applicant data indicator
        '03C-030' => 'ZG', // Present Address
        '03C-030-1' => 'F4', // former address
        '03C-030-2' => 'BH', // mailing address
        '05I-030' => '20', //base monthly income code
        '05I-030-1' => '09', //overtime income code
        '05I-030-2' => '08', //bonus income code
        '05I-030-3' => '10', //commision income code
        '05I-030-4' => '17', //divident income code
        '05I-030-5' => '33', //net income code
        //Combined Monthly Expense
        '05H-030' => '1', //indicator for present or proposed(present)
        '05H-030-1' => '2', //indicator for present or proposed(proposed)
        //Housing Payment Type Code
        '05H-040' => '25', //25 = Rent
        '05H-040-1' => '26', //26 = First Mortgage P&I
        '05H-040-2' => '22', //22 = Other Financing P&I
        '05H-040-3' => '01', //01 = Hazard Insurance
        '05H-040-4' => '14', //14 = Real Estate Taxes
        '05H-040-5' => '02', //02 = Mortgage Insurance
        '05H-040-6' => '06', //06 = Homeowner Association Dues
        '05H-040-7' => '23', //23 = Other
        //for Required if Alimony, Child Support/ Maintenance and/or Job Related Expense(s) exist.
        '06F-030' => 'DR', //Alimony
        '06F-030-2' => 'DZ', //Seprate Maintance payment ,DV have been replace by other called EE
        '06F-030-3' => 'DZ', //Job Related expense
        //declrations
        /* EDI Data Element 1321: */
        '08B-030' => '91',
        '08B-030-1' => '92',
        '08B-030-2' => '93',
        '08B-030-3' => '94',
        '08B-030-4' => '95',
        '08B-030-5' => '96',
        '08B-030-6' => '97',
        '08B-030-7' => '98',
        '08B-030-8' => '99',
        //end here declrations
        '02E-030' => '0', //down payment(There can be added aditional field in future)
    ],
    // This is our model/db relation to the FNM format piped with a default value if not blank
    // If it is prefixed with 'computed|' then it will run the method we pipe to
    'mapped' => [
        '03C-120' => 'country',
        //end here
        //for row 01A
        '01A-020' => 'mortgage_applied_for',
        '01A-060' => 'loan_amount',
        '01A-040' => 'agency_case_number',
        '01A-050' => 'case_number',
        '01A-070' => 'interest_rate',
        '01A-080' => 'no_of_months',
        '01A-090' => 'amortization_type',
        //for row 02A
        '02A-020' => 'property_street_address',
        '02A-030' => 'property_city',
        '02A-040' => 'property_state',
        '02A-050' => 'property_zip_code',
        '02A-070' => 'no_of_units',
        '02A-100' => 'year_built',
        '02A-090' => 'legal_description_of_subject_property',
        //for row 02B
        '02B-030' => 'purpose_of_loan',
        '02B-050' => 'property_will_be',
        '02B-060' => 'manner_in_which_title_will_be_held',
        '02B-070' => 'estate_will_be_held_in',
        //for row 02E
        '02E-020' => 'down_payment_type_code',
        //for row 02c
        '02C-020' => 'titleholder_name',
        //for row 03A applicant data 
        '03A-030' => 'applicant_ssn',
        '03A-040' => 'applicant_first_name',
        '03A-050' => 'applicant_middle_name',
        '03A-060' => 'applicant_last_name',
        '03A-070' => 'applicant_generation',
        '03A-080' => 'applicant_home_phone', //max length will be 11 so remove extra
        '03A-100' => 'applicant_schooling_years',
        '03A-110' => 'applicant_marital_status',
        '03A-120' => 'applicant_dependent_count',
        '03A-150' => 'applicant_birth_date', //date will format(yyyymmdd)
        '03A-160' => 'email',
        //for row 03B
        '03B-020' => 'applicant_ssn',
        '03B-030' => 'applicant_dependent_age',
        //for row 03C address,//current address
        '03C-020' => 'applicant_ssn',
        '03C-040' => 'residence_street_address',
        '03C-050' => 'residence_city',
        '03C-060' => 'residence_state',
        '03C-070' => 'residence_zip_code',
        '03C-090' => 'residency_basis_type',
        '03C-100' => 'residency_duration_years',
        '03C-110' => 'residency_duration_months', //1 to 11 are valid only
        //for row 04A employment details
        '04A-020' => 'applicant_ssn',
        '04A-030' => 'employer_name',
        '04A-040' => 'employer_street_address',
        '04A-050' => 'employer_city',
        '04A-060' => 'employer_state',
        '04A-070' => 'employer_zip_code',
        '04A-090' => 'self_employed_indicator',
        '04A-100' => 'current_employment_years_duration',
        '04A-110' => 'current_employment_months_duration',
        '04A-120' => 'current_employment_time_line_work_years',
        '04A-130' => 'employment_position_type',
        '04A-140' => 'employer_telephone',
        //address mainling and there new row added in json
        '03C-020-2' => 'applicant_ssn',
        '03C-040-2' => 'mailing_street_address',
        '03C-050-2' => 'mailing_city',
        '03C-060-2' => 'mailing_state',
        '03C-070-2' => 'mailing_zip_code',
        '03C-120-2' => 'mailing_country',
        '03C-090-2' => 'mailing_basis_type',
        '03C-100-2' => 'mailing_duration_years',
        '03C-110-2' => 'mailing_duration_months',
        //address for former
        '03C-020-1' => 'applicant_ssn',
        '03C-040-1' => 'former_residence_street_address',
        '03C-050-1' => 'former_residence_city',
        '03C-060-1' => 'former_residence_state',
        '03C-070-1' => 'former_residence_zip_code',
        '03C-090-1' => 'former_residence_basis_type',
        '03C-100-1' => 'former_residence_duration_years',
        '03C-110-1' => 'former_residence_duration_months',
        //income sources
        //monthly
        '05I-020' => 'applicant_ssn',
        '05I-040' => 'base_monthly_income',
        //overtime
        '05I-020-1' => 'applicant_ssn',
        '05I-040-1' => 'overtime_income',
        //bonus
        '05I-020-2' => 'applicant_ssn',
        '05I-040-2' => 'bonuses_income',
        //commision
        '05I-020-3' => 'applicant_ssn',
        '05I-040-3' => 'commissions_income',
        //divident
        '05I-020-4' => 'applicant_ssn',
        '05I-040-4' => 'dividends_income',
        //net
        '05I-020-5' => 'applicant_ssn',
        '05I-040-5' => 'net_rental_income',
        //other income
        '05I-020-6' => 'applicant_ssn',
        '05I-030-6' => 'other_income_code',
        '05I-040-6' => 'other_income_amount',
        '05I-020-7' => 'income_applicant_ssn',
        '05I-030-7' => 'other_income_code_second',
        '05I-040-7' => 'other_income_amount_second',
        //Combined Monthly Expense//////////////
        //present rent
        '05H-020' => 'applicant_ssn',
        '05H-050' => 'present_rent',
        //proposed rent
        '05H-020-1' => 'applicant_ssn',
        '05H-050-1' => 'proposed_rent',
        //present first mortagage
        '05H-020-2' => 'applicant_ssn',
        '05H-050-2' => 'present_first_mortgage',
        //proposed first mortgage
        '05H-020-3' => 'applicant_ssn',
        '05H-050-3' => 'proposed_first_mortgage',
        //present other financing
        '05H-020-4' => 'applicant_ssn',
        '05H-050-4' => 'present_other_financing',
        //proposed other financing
        '05H-020-5' => 'applicant_ssn',
        '05H-050-5' => 'proposed_other_financing',
        //present hazarad insurance
        '05H-020-6' => 'applicant_ssn',
        '05H-050-6' => 'present_hazard_insurance',
        //proposed hazard insurance
        '05H-020-7' => 'applicant_ssn',
        '05H-050-7' => 'proposed_hazard_insurance',
        //present real estate taxes
        '05H-020-8' => 'applicant_ssn',
        '05H-050-8' => 'present_real_estate_taxes',
        //proposed read estate taxes
        '05H-020-9' => 'applicant_ssn',
        '05H-050-9' => 'proposed_real_estate_taxes',
        //present mortgage insurance
        '05H-020-10' => 'applicant_ssn',
        '05H-050-10' => 'present_mortgage_insurance',
        //proposed mortgage insurance
        '05H-020-11' => 'applicant_ssn',
        '05H-050-11' => 'proposed_mortgage_insurance',
        //present homeowner association dues
        '05H-020-12' => 'applicant_ssn',
        '05H-050-12' => 'present_homeowner_association_dues',
        //proposed home owner association dues
        '05H-020-13' => 'applicant_ssn',
        '05H-050-13' => 'proposed_homeowner_association_dues',
        //present other dues
        '05H-020-14' => 'applicant_ssn',
        '05H-050-14' => 'other_present_dues',
        //proposed other dues
        '05H-020-15' => 'applicant_ssn',
        '05H-050-15' => 'other_proposed_dues',
        /////////////////////end Combined Monthly Expense////////////////////////////////
        //////////////////co-applicant//////////////////////////////////////////////////
        //for row 03A applicant data 
        //'03A-030-1'=>'co_applicant_ssn',
        '03A-040-1' => 'co_applicant_first_name',
        '03A-050-1' => 'co_applicant_middle_name',
        '03A-060-1' => 'co_applicant_last_name',
        '03A-070-1' => 'co_applicant_generation',
        '03A-080-1' => 'co_applicant_home_phone', //max length will be 11 so remove extra
        '03A-100-1' => 'co_applicant_schooling_years',
        '03A-110-1' => 'co_applicant_marital_status',
        '03A-120-1' => 'co_applicant_dependent_count',
        '03A-150-1' => 'co_applicant_birth_date', //date will format(yyyymmdd)
        //'03A-160-1'=>'co_email',//date will format(yyyymmdd)
        //for row 03B
        //'03B-020-1'=>'co_applicant_ssn',
        '03B-030-1' => 'co_applicant_dependent_age',
        //for row 03C address,//current address
        //'03C-020-co'=>'co_applicant_ssn',
        '03C-040-co' => 'co_applicant_residence_street_address',
        '03C-050-co' => 'co_applicant_residence_city',
        '03C-060-co' => 'co_applicant_residence_state',
        '03C-070-co' => 'co_applicant_residence_zip_code',
        '03C-090-co' => 'co_applicant_residency_basis_type',
        '03C-100-co' => 'co_applicant_residency_duration_years',
        '03C-110-co' => 'co_applicant_residency_duration_months', //1 to 11 are valid only
        //for row 04A employment details
        //'04A-020-co'=>'co_applicant_ssn',
        '04A-030-co' => 'co_applicant_employer_name',
        '04A-040-co' => 'co_applicant_employer_street_address',
        '04A-050-co' => 'co_applicant_employer_city',
        '04A-060-co' => 'co_applicant_employer_state',
        '04A-070-co' => 'co_applicant_employer_zip_code',
        '04A-090-co' => 'co_applicant_self_employed_indicator',
        '04A-100-co' => 'co_applicant_current_employment_years_duration',
        '04A-110-co' => 'co_applicant_current_employment_months_duration',
        '04A-120-co' => 'co_applicant_current_employment_time_line_work_years',
        '04A-130-co' => 'co_applicant_employment_position_type',
        '04A-140-co' => 'co_applicant_employer_telephone',
        //address mainling and there new row added in json
        //'03C-020-2-co'=>'co_applicant_ssn',
        '03C-040-2-co' => 'co_applicant_mailing_street_address',
        '03C-050-2-co' => 'co_applicant_mailing_city',
        '03C-060-2-co' => 'co_applicant_mailing_state',
        '03C-070-2-co' => 'co_applicant_mailing_zip_code',
        '03C-120-2-co' => 'co_applicant_mailing_country',
        '03C-090-2-co' => 'co_applicant_mailing_basis_type',
        '03C-100-2-co' => 'co_applicant_mailing_duration_years',
        '03C-110-2-co' => 'co_applicant_mailing_duration_months',
        //address for former
        // '03C-020-1-co'=>'co_applicant_ssn',
        '03C-040-1-co' => 'co_borrower_former_residence_street_address',
        '03C-050-1-co' => 'co_borrower_former_residence_city',
        '03C-060-1-co' => 'co_borrower_former_residence_state',
        '03C-070-1-co' => 'co_borrower_former_residence_zip_code',
        '03C-090-1-co' => 'co_borrower_former_residence_basis_type',
        '03C-100-1-co' => 'co_borrower_former_residence_duration_years',
        '03C-110-1-co' => 'co_borrower_former_residence_duration_months',
        //income sources
        //monthly
        //'05I-020-co'=>'co_applicant_ssn',
        '05I-040-co' => 'co_applicant_base_monthly_income',
        //overtime
        //'05I-020-1-co'=>'co_applicant_ssn',
        '05I-040-1-co' => 'co_applicant_overtime_income',
        //bonus
        //'05I-020-2-co'=>'co_applicant_ssn',
        '05I-040-2-co' => 'co_applicant_bonuses_income',
        //commision
        //'05I-020-3-co'=>'co_applicant_ssn',
        '05I-040-3-co' => 'co_applicant_commissions_income',
        //divident
        //'05I-020-4-co'=>'co_applicant_ssn',
        '05I-040-4-co' => 'co_applicant_dividends_income',
        //net
        //'05I-020-5-co'=>'co_applicant_ssn',
        '05I-040-5-co' => 'co_applicant_net_rental_income',
        //other income
        //'05I-020-6-co'=>'co_applicant_ssn',
        '05I-030-6-co' => 'co_applicant_other_income_code',
        '05I-040-6-co' => 'co_applicant_other_income_amount',
        //'05I-020-7-co'=>'income_co_applicant_ssn',
        '05I-030-7-co' => 'co_applicant_other_income_code_second',
        '05I-040-7-co' => 'co_applicant_other_income_amount_second',
        //Combined Monthly Expense//////////////
        //present rent
        //'05H-020-co'=>'co_applicant_ssn',
        '05H-050-co' => 'co_applicant_present_rent',
        //proposed rent
        //'05H-020-1-co'=>'co_applicant_ssn',
        '05H-050-1-co' => 'co_applicant_proposed_rent',
        //present first mortagage
        //'05H-020-2-co'=>'co_applicant_ssn',
        '05H-050-2-co' => 'co_applicant_present_first_mortgage',
        //proposed first mortgage
        //'05H-020-3-co'=>'co_applicant_ssn',
        '05H-050-3-co' => 'co_applicant_proposed_first_mortgage',
        //present other financing
        //'05H-020-4-co'=>'co_applicant_ssn',
        '05H-050-4-co' => 'co_applicant_present_other_financing',
        //proposed other financing
        //'05H-020-5-co'=>'co_applicant_ssn',
        '05H-050-5-co' => 'co_applicant_proposed_other_financing',
        //present hazarad insurance
        //'05H-020-6-co'=>'co_applicant_ssn',
        '05H-050-6-co' => 'co_applicant_present_hazard_insurance',
        //proposed hazard insurance
        //'05H-020-7-co'=>'co_applicant_ssn',
        '05H-050-7-co' => 'co_applicant_proposed_hazard_insurance',
        //present real estate taxes
        //'05H-020-8-co'=>'co_applicant_ssn',
        '05H-050-8-co' => 'co_applicant_present_real_estate_taxes',
        //proposed read estate taxes
        //'05H-020-9-co'=>'co_applicant_ssn',
        '05H-050-9-co' => 'co_applicant_proposed_real_estate_taxes',
        //present mortgage insurance
        //'05H-020-10-co'=>'co_applicant_ssn',
        '05H-050-10-co' => 'co_applicant_present_mortgage_insurance',
        //proposed mortgage insurance
        //'05H-020-11-co'=>'co_applicant_ssn',
        '05H-050-11-co' => 'co_applicant_proposed_mortgage_insurance',
        //present homeowner association dues
        //'05H-020-12-co'=>'co_applicant_ssn',
        '05H-050-12-co' => 'co_applicant_present_homeowner_association_dues',
        //proposed home owner association dues
        //'05H-020-13-co'=>'co_applicant_ssn',
        '05H-050-13-co' => 'co_applicant_proposed_homeowner_association_dues',
        //present other dues
        //'05H-020-14-co'=>'co_applicant_ssn',
        '05H-050-14-co' => 'co_applicant_other_present_dues',
        //proposed other dues
        //'05H-020-15-co'=>'co_applicant_ssn',
        '05H-050-15-co' => 'co_applicant_other_proposed_dues',
        /////////////////////end Combined Monthly Expense////////////////////////////////
        //assets data
        //Cash deposit on purchase
        '06A-020' => 'applicant_ssn',
        '06A-040' => 'asset_market_value',
        //life insurance and case value
        '06B-020' => 'applicant_ssn',
        '06B-040' => 'life_insurance_cash_value',
        '06B-050' => 'life_insurance_face_value',
        //for Required if Alimony, Child Support/ Maintenance and/or Job Related Expense(s) exist.
        '06F-020' => 'applicant_ssn',
        '06F-040' => 'alimony_payment',
        '06F-050' => 'alimony_payment_month_left',
        '06F-060' => 'alimony_payment_text',
        '06F-020-3' => 'applicant_ssn',
        '06F-040-3' => 'separate_maintenance_payment',
        '06F-050-3' => 'separate_maintenance_payment_month_left',
        '06F-060-3' => 'separate_maintenance_payment_text',
        '06F-020-2' => 'applicant_ssn',
        '06F-040-2' => 'job_related_expense',
        '06F-050-2' => 'job_related_expense_month_left',
        '06F-060-2' => 'job_related_expense_text',
        //transaction details
        '07A-020' => 'transaction_purchase_price',
        '07A-030' => 'transaction_after_imprvt_repair',
        '07A-040' => 'transaction_land',
        '07A-050' => 'transaction_refinance',
        '07A-060' => 'estimated_prepaid_items',
        '07A-070' => 'estimated_closing_cost',
        '07A-080' => 'pmi_mip_funding_fee',
        '07A-090' => 'transaction_discount',
        '07A-100' => 'subordinate_financing',
        '07A-110' => 'applicant_closing_cost_paid_by_seller',
        '07A-120' => 'pmi_mip_funding_fee_financed',
        '07B-020' => 'other_credit_type_code',
        '07B-030' => 'amount_of_other_credit',
        //assets data end here
        //declrations start
        //borrower
        '08A-020' => 'applicant_ssn',
        '08A-030' => 'dec_outstanding_judgement',
        '08A-040' => 'dec_bankrupt',
        '08A-050' => 'dec_property_foreclosed',
        '08A-060' => 'dec_lawsuit',
        '08A-070' => 'dec_obligated_loan',
        '08A-080' => 'dec_delinquent',
        '08A-090' => 'dec_obligated_alimony',
        '08A-100' => 'dec_down_payment_borrowed',
        '08A-110' => 'dec_co_maker',
        '08A-120' => 'dec_residence_type',
        '08A-130' => 'dec_property_primary_residence',
        '08A-140' => 'dec_ownership_interest',
        '08A-150' => 'dec_type_of_property',
        '08A-160' => 'dec_hold_title_to_the_home',
        //co-borrower
        //'08A-020-co'=>'applicant_ssn',
        '08A-030-co' => 'co_applicant_dec_outstanding_judgement',
        '08A-040-co' => 'co_applicant_dec_bankrupt',
        '08A-050-co' => 'co_applicant_dec_property_foreclosed',
        '08A-060-co' => 'co_applicant_dec_lawsuit',
        '08A-070-co' => 'co_applicant_dec_obligated_loan',
        '08A-080-co' => 'co_applicant_dec_delinquent',
        '08A-090-co' => 'co_applicant_dec_obligated_alimony',
        '08A-100-co' => 'co_applicant_dec_down_payment_borrowed',
        '08A-110-co' => 'co_applicant_dec_co_maker',
        '08A-120-co' => 'co_applicant_dec_residence_type',
        '08A-130-co' => 'co_applicant_dec_property_primary_residence',
        '08A-140-co' => 'co_applicant_dec_ownership_interest',
        '08A-150-co' => 'co_applicant_dec_type_of_property',
        '08A-160-co' => 'co_applicant_dec_hold_title_to_the_home',
        //for borrower
        '08B-020' => 'applicant_ssn',
        '08B-040' => 'dec_outstanding_judgement_desc',
        //for co-borrower
        //'08B-020-co'=>'co_applicant_ssn',
        '08B-040-co' => 'co_applicant_dec_outstanding_judgement_desc',
        //for borrower
        '08B-020-1' => 'applicant_ssn',
        '08B-040-1' => 'dec_bankrupt_desc',
        //for co-borrower
        //'08B-020-1-co'=>'co_applicant_ssn',
        '08B-040-1-co' => 'co_applicant_dec_bankrupt_desc',
        //for borrower
        '08B-020-2' => 'applicant_ssn',
        '08B-040-2' => 'dec_property_foreclosed_desc',
        //for co-borrower
        //'08B-020-2-co'=>'co_applicant_ssn',
        '08B-040-2-co' => 'co_applicant_dec_property_foreclosed_desc',
        //for borrower
        '08B-020-3' => 'applicant_ssn',
        '08B-040-3' => 'dec_lawsuit_desc',
        //for co-borrower
        //'08B-020-3-co'=>'co_applicant_ssn',
        '08B-040-3-co' => 'co_applicant_dec_lawsuit_desc',
        //for borrower
        '08B-020-4' => 'applicant_ssn',
        '08B-040-4' => 'dec_obligated_loan_desc',
        //for co-borrower
        //'08B-020-4-co'=>'co_applicant_ssn',
        '08B-040-4-co' => 'co_applicant_dec_obligated_loan_desc',
        //for borrower
        '08B-020-5' => 'applicant_ssn',
        '08B-040-5' => 'dec_delinquent_desc',
        //for co-borrower
        //'08B-020-5-co'=>'co_applicant_ssn',
        '08B-040-5-co' => 'co_applicant_dec_delinquent_desc',
        //for borrower
        '08B-020-6' => 'applicant_ssn',
        '08B-040-6' => 'dec_obligated_alimony_desc',
        //for co-borrower
        //'08B-020-6-co'=>'co_applicant_ssn',
        '08B-040-6-co' => 'co_applicant_dec_obligated_alimony_desc',
        //for borrower
        '08B-020-7' => 'applicant_ssn',
        '08B-040-7' => 'dec_down_payment_borrowed_desc',
        //for co-borrower
        //'08B-020-7-co'=>'co_applicant_ssn',
        '08B-040-7-co' => 'co_applicant_dec_down_payment_borrowed_desc',
        //for borrower
        '08B-020-8' => 'applicant_ssn',
        '08B-040-8' => 'dec_co_maker_desc',
        //co-borrower
        //'08B-020-8-co'=>'co_applicant_ssn',
        '08B-040-8-co' => 'co_applicant_dec_co_maker_desc',
        //Information for Government Monitoring Purposes
        //BORROWER
        '10A-020' => 'applicant_ssn',
        '10A-030' => 'dec_do_not_wish_furnish_information',
        '10A-040' => 'dec_enthnicity',
        '10A-060' => 'dec_sex',
        //co-borrower
        //'10A-020-co'=>'co_applicant_ssn',
        '10A-030-co' => 'co_applicant_dec_do_not_wish_furnish_information',
        '10A-040-co' => 'co_applicant_dec_enthnicity',
        '10A-060-co' => 'co_applicant_dec_sex',
        //borrower
        '10R-020' => 'applicant_ssn',
        '10R-030-1' => 'dec_race_1',
        '10R-030-2' => 'dec_race_2',
        '10R-030-3' => 'dec_race_3',
        '10R-030-4' => 'dec_race_4',
        '10R-030-5' => 'dec_race_5',
        //co-borrower
        //'10R-020-co'=>'co_applicant_ssn',
        '10R-030-1-co' => 'co_applicant_dec_race_1',
        '10R-030-2-co' => 'co_applicant_dec_race_2',
        '10R-030-3-co' => 'co_applicant_dec_race_3',
        '10R-030-4-co' => 'co_applicant_dec_race_4',
        '10R-030-5-co' => 'co_applicant_dec_race_5',
    //declrations end here
    ],
    // Fields whose value/requirement is dependent on another field
    // In form of parent => child
    'conditional' => [
    ],
];
