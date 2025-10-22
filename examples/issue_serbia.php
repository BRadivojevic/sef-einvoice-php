<?php
require __DIR__.'/../vendor/autoload.php';
use App\Serbia\IssueInvoice;
IssueInvoice::send(['id'=>'INV-1001','date'=>date('Y-m-d'),'buyer'=>'Kupac DOO','total'=>1234.56]);
