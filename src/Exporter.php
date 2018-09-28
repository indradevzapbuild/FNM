<?php

namespace indradevzapbuild\FNM;

use indradevzapbuild\FNM\Exception\FieldNotSetException;
use indradevzapbuild\FNM\Exception\IncorrectlyMappedConditionalException;
use indradevzapbuild\FNM\Exception\MethodNotSetException;

class Exporter {
    /*
     * Instance of the file during creation
     */

    protected $_file;

    /*
     * All fields available in FNM Format
     */
    protected $_fields = [];

    /*
     * The local mapping of our input fields to the FannieMae Field they refer to
     * Loaded from the ../config/mapper.php
     */
    protected $_mapper = [];

    /*
     * The input used to map to the fields
     */
    private $_input = [];

    /*
     * Storage for all the computed properties
     */
    private $_computed = [];

    /*
     * Storage for all our conditional mapped fields
     */
    private $_conditional = [];

    /*
     * Name of output file
     */
    public $file_name;
    /*
     * Add more data mapping
     */
    public $_add_more_data = [];

    public function __construct($input, $add_more_data = array(), $fileName = 'Export') {
        $this->_add_more_data = $add_more_data;
        $this->loadConfig();
        // Grab Input
        $this->_input = $input;

        // Set the file name
        $this->file_name = $fileName . '.fnm';

        // Start the buffer and lets goooooo!
        ob_start();
        $this->_file = fopen('php://output', 'w');
    }

    /**
     * Load Config files
     */
    private function loadConfig() {
        $config = new \stdClass;
        $config->dir = dirname(__FILE__).'/bin';
        // Load the field config
        $this->_fields = json_decode(file_get_contents($config->dir . '/fnm.json'));

        // Load our local config to map the vals
        $this->_mapper = require_once $config->dir . '/mapper.php';
    }

    /**
     * Loops through all fields to map them to our associated fields
     */
    private function createDoc() {
        foreach ($this->_fields as $field) {
            /* Custom code to make this as we needed */
            if (in_array($field[0]->field_id, ['04B-010', '06C-010', '06D-010', '06L-010', '06G-010'])) {//This added becuase of add more we are not clr how many row of data enter user
                $this->writeAddMore($field);
            } else {
                //lets go as previous going
                $this->write($field);
            }
            //$this->write($field);
        }
    }

    /**
     * Checks if the FNM field id is assigned a static value
     *
     * @param $part
     * @return bool
     */
    private function staticField($part) {
        return isset($this->_mapper['static'][$part->field_id]);
    }

    /**
     * Checks if the FNM field id is associated with a mapped value
     *
     * @param $part
     * @return bool
     */
    private function mappedField($part) {
        return isset($this->_mapper['mapped'][$part->field_id]) && substr($part->field_id, -3, 3) != '010';
    }

    /**
     * Returns the input by key or $default
     *
     * @param $key
     * @param null $default
     * @return null|mixed
     */
    protected function input($key, $default = null) {
        $key = str_replace('.', '_', $key);

        return isset($this->_input[$key]) ? $this->_input[$key] : $default;
    }

    /**
     * Returns the type of field from the mapper
     *
     * @param $part
     * @return string
     */
    private function getWriteType($part) {
        if ($this->staticField($part) !== false) {
            $writable = 'static';
        } elseif ($this->mappedField($part) !== false) {
            $writable = 'mapped';
        } else {
            $writable = 'default';
        }

        return $writable;
    }

    /**
     * Creates an array of all write types for the row as 'field_id' => 'type_of_field'
     * Example: ['01A-000' => 'default', '01A-020' => 'mapped', '01A-030' => 'default', '01A-040' => 'static']
     *
     * @param $parts
     * @return array|bool
     */
    private function writableRow($parts) {   // Keep track of parts of the entire row
        $partsToWrite = [];
        // We start out by not writing the row until we see something that needs to be there
        $writing = false;
        // For multi map track the largest array
        // Start with value of one so as min repeat
        $repeatRow = [1];
        foreach ($parts as $part) {
            // We arent doing the Government Data or Community Lending data right now
            if ($part->field_id == '000-020' && in_array($part->values, ['20', '30']))
                return false;

            $partsToWrite[$part->field_id] = $this->getWriteType($part);
            // Store the count of any array so we know how many times to repeat this row
            if ($partsToWrite[$part->field_id] == 'mapped' && is_array($this->_mapper['mapped'][$part->field_id]))
                $repeatRow[] = count($this->_mapper['mapped'][$part->field_id]);

            // If there is a repeat in the first position it means we can repeat the row the amount of times the next method evaluates to or number given
            if (substr($part->field_id, -3, 3) == '010' && isset($this->_mapper['mapped'][$part->field_id]) && strpos($this->_mapper['mapped'][$part->field_id], 'repeat') !== false) {
                $repeatExp = explode('|', $this->_mapper['mapped'][$part->field_id]);

                $repeatRow[] = $repeatExp[1] == 'computed' ? $this->handleComputeAndStore($repeatExp[2]) : $repeatExp[1];
            }

            // If it is already true we dont need to check
            // All we need is 1 field to not be default to write a row so we have to check
            if (!$writing && ($partsToWrite[$part->field_id] != 'default' || max($repeatRow) > 1)) {
                $writing = true;
            }
        }
        // Needs to be last item in array so we can pop it off to get our repeat value
        $partsToWrite['repeat'] = max($repeatRow);

        return $writing ? $partsToWrite : $writing;
    }

    /**
     * Write the row
     * TODO: break into writeRow and WritePart to break up some logic
     *
     * @param $parts
     * @return $this
     */
    private function write($parts) {
        // Returns false if all writable types are 'default' so we skip the row
        $writableTypes = $this->writableRow($parts);
        if ($writableTypes !== false) {
            $counter = 0;
            $repeatRow = array_pop($writableTypes);
            do {
                // Start with an empty string which we will turn into a row
                $contents = '';
                // Default is to not write this row so we dont write blank rows for the rows that allow multi rows
                $writeRow = false;
                foreach ($parts as $part) {
                    // Preemptive error avoidance just to make sure we force an int
                    $part->field_length = (integer) $part->field_length;
                    // Call appropriate write type for this part of the row
                    switch ($writableTypes[$part->field_id]) {
                        case 'static':
                            $contents .= $this->writeStatic($part);
                            // If we have a static value we write
                            $writeRow = true;
                            break;
                        case 'mapped':
                            $r = $this->writeMapped($part, $counter);
                            // If a conditional is false we count and dont write
                            if ($r !== false) {
                                $contents .= $r;
                                if (!$writeRow)
                                    $writeRow = str_replace(' ', '', $r) != '';
                            }
                            break;
                        case 'default':
                        default:
                            $contents .= $this->writeDefault($part);
                            break;
                    }
                }
                // We write the file one row at a time
                if ($writeRow)
                    fputs($this->_file, $contents . "\r\n");

                $counter++;
            } while ($counter < $repeatRow);
        }

        return $this;
    }

    /**
     * Write add more data Custom code
     * coder:indradev
     * @param $parts
     * @return $this
     */
    private function writeAddMore($parts) {
        if (isset($this->_add_more_data[$parts[0]->field_id])) {
            foreach ($this->_add_more_data[$parts[0]->field_id] as $more_data) {//for every add more
                $contents = '';
                foreach ($parts as $part) {
                    if (substr($part->field_id, -3, 3) == '010') {
                        $str = str_pad($this->clean($part->data_stream), $part->field_length);
                    } else if (isset($more_data[$part->field_id])) {//set data
                        if (strlen($more_data[$part->field_id]) > $part->field_length) {
                            $str = substr($more_data[$part->field_id], 0, $part->field_length);
                        } else {
                            $str = str_pad($more_data[$part->field_id], $part->field_length);
                        }
                    } else {//pad
                        $str = str_repeat(' ', $part->field_length);
                    }
                    $contents .= $str;
                }
                fputs($this->_file, $contents . "\r\n");
            }
        } else {
            return false; //return from here no data come from our db
        }
    }

    /**
     * Write the static content from our mapper
     *
     * @param $part
     * @return string
     */
    private function writeStatic($part) {
        return str_pad($this->_mapper['static'][$part->field_id], $part->field_length);
    }

    /**
     * Write a field we have mapped to an input
     *
     * @param $part
     * @param $count
     * @return bool|mixed|null|string
     * @throws FieldNotSetException
     * @throws IncorrectlyMappedConditionalException
     */
    private function writeMapped($part, $count) {
        $row = $this->computeField($part, $count);

        // The row will be false if a conditional is not met.
        if ($row !== false) {
            if (strlen($row) > $part->field_length) {
                $row = substr($row, 0, $part->field_length);
            } else {
                $row = str_pad($row, $part->field_length);
            }
        }

        return $row;
    }

    /**
     * Test conditional statement
     *
     * @param $conditionAndArgs
     * @return mixed
     * @throws MethodNotSetException
     */
    private function handleConditionalAndStore($conditionAndArgs) {
        // If we have already computed this return it
        if (isset($this->_conditional[$conditionAndArgs]))
            return $this->_conditional[$conditionAndArgs];

        // Pull prameters out of string
        $params = explode("::", $conditionAndArgs);

        // Pull out the conditional statement
        $conditional = array_shift($params);

        // Check to make sure the method exists
        if (!method_exists($this, $conditional))
            throw new MethodNotSetException('Conditional Method ' . $conditional . ' doesnt\'t exist!');

        $this->_conditional[$conditionAndArgs] = call_user_func_array([$this, $conditional], array_map([$this, 'inputReturnSelf'], $params));

        return $this->_conditional[$conditionAndArgs];
    }

    /**
     * This is a helper for the array_map so we can return the value instead of null if it doesnt exist
     *
     * @param $key
     * @return mixed|null
     */
    private function inputReturnSelf($key) {
        return $this->input($key, $key);
    }

    /**
     * Compute the value of a field
     *
     * @param $methodAndArgs
     * @return mixed
     * @throws MethodNotSetException
     */
    private function handleComputeAndStore($methodAndArgs, $counter = 0) {
        // If we have already computed this return it
        if (isset($this->_computed[$methodAndArgs . $counter]))
            return $this->_computed[$methodAndArgs . $counter];

        // Pull prameters out of string
        $params = explode("::", $methodAndArgs);

        // Pull out the
        $method = array_shift($params);
        // Switch the counter input for the value of the counter

        $params = array_map(function($param) use ($counter) {
            return $param == 'counter' ? $counter : $param;
        }, $params);

        // Check to make sure the method exists
        if (!method_exists($this, $method))
            throw new MethodNotSetException('Computed Method ' . $method . ' doesnt\'t exist!');

        $this->_computed[$methodAndArgs . $counter] = call_user_func_array([$this, $method], array_map([$this, 'inputReturnSelf'], $params));

        return $this->_computed[$methodAndArgs . $counter];
    }

    /**
     * Returns the value of a mapped field
     *
     * @param $part
     * @param null $mapperIndex
     * @return bool|mixed|null
     * @throws FieldNotSetException
     * @throws IncorrectlyMappedConditionalException
     * @throws MethodNotSetException
     */
    private function computeField($part, $mapperIndex = null) {
        // If field is an array we compute the current index
        $mappedField = is_array($this->_mapper['mapped'][$part->field_id]) ? $this->_mapper['mapped'][$part->field_id][$mapperIndex] : $this->_mapper['mapped'][$part->field_id];
        $concats = explode('|', str_replace('.', '_', $mappedField));

        if ($concats[0] == 'computed') {
            // If its a computed field then we will compute
            $str = $this->handleComputeAndStore($concats[1], $mapperIndex);
        } elseif ($concats[0] == 'onlyif') {
            // If the conditional is true we return the field.  If not we return false
            if ($this->handleConditionalAndStore($concats[1])) {
                // If this is set then we have to compute if not we set the value
                if (isset($concats[3])) {
                    switch ($concats[2]) {
                        case 'computed':
                            $str = $this->handleComputeAndStore($concats[3], $mapperIndex);
                            break;
                        case 'set':
                            $str = $concats[3];
                            break;
                        default:
                            // Sets the value to default piped value if no value is set
                            $this->input($concats[2], $concats[3]);
                    }
                } elseif (isset($concats[2])) {
                    // If this is set on a conditional it is an input field or the default value
                    $str = $this->input($concats[2]);
                } else {
                    throw new IncorrectlyMappedConditionalException('No Field/Value Set For When Field ID ' . $part->field_id . ' Returns As True');
                }
            } else {
                $str = false;
            }
        } elseif ($concats[0] == 'set') {
            if (!isset($concats[1]))
                throw new FieldNotSetException($part->field_id . ' Field Not Set In "set" directive in mapper');
            // If it says set then we set
            $str = $concats[1];
        } else {
            // If the input is set then return it!
            $str = $this->input($concats[0], isset($concats[1]) ? $concats[1] : '');
        }

        // No need to check dependents if we failed the conditional
        if ($str !== false) {
            // Add any dependencies of this value
            $this->addDependentFields($part->field_id, $str);
        }

        return $str;
    }

    /**
     * Add required fields based on this fields value
     *
     * @param $fieldId
     * @param $value
     */
    protected function addDependentFields($fieldId, $value) {
        if (isset($this->_mapper['conditional'][$fieldId . '|' . $value])) {
            foreach ($this->_mapper['conditional'][$fieldId . '|' . $value] as $id => $input) {
                $this->_mapper['mapped'][$id] = $input;
            }
        }
    }

    /**
     * Writes a default part of the string
     * @param $part
     * @return string
     */
    protected function writeDefault($part) {
        if (substr($part->field_id, -3, 3) == '010') {
            $str = str_pad($this->clean($part->data_stream), $part->field_length);
        } elseif ($part->field_id == '000-020') {
            // For this field we have stored the values in the Value parameter of the field object
            //$str = str_pad($this->clean($part->values), $part->field_length);
            //there were coming issue due to of some bad data issue so we need to make formate it first(above one line commeted) //start here
            $newstr = explode('=', $part->values);
            if ($this->clean($part->values) == '1=1003') {
                $str = $newstr[0] . '  '; //remove after =
            } else {
                $str = $newstr[0];
            }
            //end here
        } else {
            $str = str_repeat(' ', $part->field_length);
        }

        return $str;
    }

    protected function clean($string) {
        return preg_replace('/\s|\'/', '', $string);
    }

    /**
     * Writes the file
     *
     * @return string
     */
    public function get() {
        $this->createDoc();
        // Stop Buffer and get dat file
        $contents = ob_get_clean();
        // Listen to your mother and clean up after yourself.  Close that output stream.
        fclose($this->_file);

        return $contents;
    }

    /**
     * Displays the file (mostly helpful for debugging)
     */
    public function display() {
        // Stop Buffer and get dat file
        $contents = $this->get();

        echo "<!doctype html>\n<html><head><meta><title>FNM</title></head><body><pre>$contents</pre></body></html>";
    }

    /**
     * Downloads the doc
     */
    public function download() {
        $contents = $this->get();

        header('Content-type: application/octet-stream; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"$this->file_name\"");
        echo $contents;
        exit;
    }

}
