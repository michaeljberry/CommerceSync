<?php

namespace CSV;

class CSV
{
    protected $fileName;
    protected $fileFolder;
    protected $filePath;
    protected $csvArray;
    protected $filePointer;

    public function __construct($fileFolder, $csvArray, $csvHeader = null)
    {
        $this->setFileName();

        $this->setFileFolder($fileFolder);

        $this->setFilePath();

        $this->setCSVArray($csvArray);

        $this->setCSVHeader($csvHeader);

        $this->createCSVFromArray();
    }

    protected function setFileName()
    {
        $this->fileName = date('Y-m-d-H-i') . ".csv";
    }

    protected function setFileFolder($fileFolder)
    {
        $this->fileFolder = $fileFolder;
    }

    protected function setFilePath()
    {
        $this->filePath = $this->getFileFolder() . DIRECTORY_SEPARATOR . $this->getFileName();
    }

    protected function setCSVArray($csvArray)
    {
        $this->csvArray = $csvArray;
    }

    protected function setCSVHeader($csvHeader)
    {
        if($csvHeader)

            $this->prependToCSVArray($csvHeader);
    }

    protected function createCSVFromArray()
    {
        $this->openCSV();

        $this->putCSVContents();

        $this->closeCSV();
    }

    protected function openCSV()
    {
        $this->filePointer = fopen($this->getFilePath(), 'w');
    }

    protected function putCSVContents()
    {
        foreach ($this->getCSVArray() as $fields) {

            $this->fputcsv_eol($fields);

        }
    }

    protected function closeCSV()
    {
        fclose($this->getFilePointer());
    }

    protected function fputcsv_eol($array, $eol = "\r\n")
    {
        fputcsv($this->getFilePointer(), $array);

        if ("\n" != $eol && 0 === fseek($this->getFilePointer(), -1, SEEK_CUR)) {

            fwrite($this->getFilePointer(), $eol);

        }
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getFileFolder()
    {
        return $this->fileFolder;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getCSVArray()
    {
        return $this->csvArray;
    }

    public function getFilePointer()
    {
        return $this->filePointer;
    }

    public function prependToCSVArray($array)
    {
        array_unshift($this->csvArray, $array);
    }
}