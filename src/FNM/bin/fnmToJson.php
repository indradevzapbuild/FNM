<?php

class fnmToJson
{
    protected $file_to_read = '/fnm_fields.csv';

    protected $avalaibleHeaders = [
        'field_id'          => 'Field ID',
        '1003_section'      => '1003 Section',
        'data_stream'       => 'Data Stream',
        'position'          => 'Position',
        'field_length'      => 'Field Lenth',
        'occurances'        => '# Occurances',
        'field_information' => 'Field Information',
        'values'            => 'Values/EDI Code(s)',
        'du'                => 'DeskTop Underwriter (DU)',
        'g'                 => 'Government (G)',
        'cr'                => 'Credit Request (CR)',
        'ec'                => 'EarlyCheck (EC)',
        'root_element'      => 'Root Element:  <LOAN_APPLICATION>',
    ];

    private $required_sections = [
        0 => 'EH', 1 => '1003', 2 => 'additional_case_data', 3 => 'product_data'
    ];

    private $headers = [];
    private $formatted_headers = [];
    private $csv;
    private $all_fields = [];
    private $destinationDir;

    public function __construct($destinationDir = null)
    {
        $this->destinationDir = is_null($destinationDir) ? __DIR__ . '/../config' : $destinationDir;
        $this->createConfigJson()->copyMapperFile();
        $this->csv = fopen(__DIR__ . $this->file_to_read, 'r');
        $this->readData()
            ->toJson();
    }

    private function createConfigJson()
    {
        $_fp = fopen(__DIR__ . '/../config.local.json', 'w+');
        fwrite($_fp, json_encode(['dir' => getcwd().'/'.$this->destinationDir]));
        fclose($_fp);

        return $this;
    }

    public function copyMapperFile()
    {
        if (!file_exists($this->destinationDir))
            mkdir($this->destinationDir);

        if (!file_exists($this->destinationDir . '/mapper.php'))
        {
            if (!copy(__DIR__ . '/mapper.example.php', $this->destinationDir . '/mapper.php'))
            {
                echo "Failed to copy mapper file";

                return false;
            }
        }

        return true;
    }

    protected function readData()
    {
        $this->getHeaders()
            ->formatHeaders()
            ->convertToCollection();

        return $this;
    }

    protected function getHeaders()
    {
        do
        {
            $this->headers = fgetcsv($this->csv);
            $headerCount = array_intersect($this->headers, $this->avalaibleHeaders);
        } while (count($headerCount) == 0);

        return $this;
    }

    protected function formatHeaders()
    {
        foreach ($this->headers as $key => $val)
        {
            $k = array_search($val, $this->avalaibleHeaders);
            if ($k !== false)
            {
                $this->formatted_headers[$key] = $k;
            }
        }

        return $this;
    }

    protected function convertToCollection()
    {
        $lineNumber = 0;
        while (!feof($this->csv))
        {
            $row = $this->rowToObject();
            $fieldIds = explode('-', $row['field_id']);
            if (isset($fieldIds[1]) && $row['field_id'] != '')
            {
                $this->all_fields[$lineNumber][] = $row;
            } else
            {
                $lineNumber++;
            }
        }

    }

    protected function convertAllRows()
    {
        $section = 0;
        $lastLine = '';
        while (!feof($this->csv))
        {
            $row = $this->rowToObject();
            $fieldIds = explode('-', $row['field_id']);
            if (isset($fieldIds[1]))
            {
                if ($lastLine == $section . '-' . $row['field_id'])
                {
                    $this->all_fields[$this->required_sections[$section]][$fieldIds[0]][$fieldIds[1]]['field_information'] .= "\n" . $row['field_information'];
                } elseif (!isset($this->all_fields[$this->required_sections[$section]][$fieldIds[0]][$fieldIds[1]]))
                {
                    $this->all_fields[$this->required_sections[$section]][$fieldIds[0]][$fieldIds[1]] = $row;
                } elseif (isset($this->required_sections[$section + 1]))
                {
                    $section++;
                    $this->all_fields[$this->required_sections[$section]][$fieldIds[0]][$fieldIds[1]] = $row;
                }
                $lastLine = $section . '-' . $row['field_id'];
            }

        }
        fclose($this->csv);

        return $this;
    }

    protected function toJson()
    {
        $_fp = fopen($this->destinationDir.'/fnm.json', 'w+');
        fwrite($_fp, json_encode($this->all_fields, JSON_PRETTY_PRINT));
        fclose($_fp);

        return true;
    }

    protected function rowToObject()
    {
        $row = fgetcsv($this->csv);
        $obj = [];
        foreach ($this->formatted_headers as $key => $val)
        {
            $obj[$val] = $row[$key];
        }

        return $obj;
    }

    public function getOriginalHeader($currHeader)
    {
        return $this->avalaibleHeaders[$currHeader];
    }
}