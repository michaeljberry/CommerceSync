<?php

namespace CSV;

class CSV
{

    protected $fileName;
    protected $directory;
    protected $filePath;
    protected $csvArray;
    protected $filePointer;

    public function __construct($fileName, $directory, $csvArray)
    {

        $this->setFileName($fileName);

        $this->setDirectory($directory);

        $this->setFilePath();

        $this->setCSVArray($csvArray);

        $this->createCSVFromArray();

    }

    protected function setFileName($fileName)
    {

        $this->fileName = "$fileName.csv";

    }

    protected function setDirectory($directory)
    {

        $this->directory = $directory;

    }

    protected function setFilePath()
    {

        $this->filePath = $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getFileName();

    }

    protected function setCSVArray($csvArray)
    {

        $this->csvArray = $csvArray;

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

    public function getDirectory()
    {

        return $this->directory;

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

}