<?php
/*******************************************************************************
 *******************************************************************************
 **                                                                           **
 **                                                                           **
 **  Copyright 2015-2017 JK Technosoft                  					  **
 **  http://www.jktech.com                                           		  **
 **                                                                           **
 **  ProActio is free software; you can redistribute it and/or modify it      **
 **  under the terms of the GNU General Public License (GPL) as published     **
 **  by the Free Software Foundation; either version 2 of the License, or     **
 **  at your option) any later version.                                       **
 **                                                                           **
 **  ProActio is distributed in the hope that it will be useful, but WITHOUT  **
 **  ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or    **
 **  FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License     **
 **  for more details.                                                        **
 **                                                                           **
 **  See TNC.TXT for more information regarding the Terms and Conditions    **
 **  of use and alternative licensing options for this software.              **
 **                                                                           **
 **  A copy of the GPL is in GPL.TXT which was provided with this package.    **
 **                                                                           **
 **  See http://www.fsf.org for more information about the GPL.               **
 **                                                                           **
 **                                                                           **
 *******************************************************************************
 *******************************************************************************
 *
 * {program name}
 *
 * Known Bugs & Issues:
 *
 *
 * Author:
 *
 *	JK Technosoft
 *	http://www.jktech.com
 *	August 11, 2015
 *
 *
 * History:
 *
 */

session_start();
require('pdffunc.php');
@$current_user=$_SESSION['username'];
$dt=$_POST['date'];
$pdf= new PDF_TOC('L','mm','A4'); 
$pdf->startPageNums();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',32);
$pdf->Ln(70);
$pdf->Cell(0,1,'DATABASE BI SIZE REPORT',0,1,'C');

include('sqlconnectver2.php');
if($query = mysql_query("SELECT dbuid,dbname,environ FROM configureddb INNER JOIN dbalogin where  dbalogin.username ='$current_user'")){
$pdf->AddPage();
 $pdf->Ln(3);
		$pdf->SetFont('Arial','',20);
$pdf->setFillColor(63,139,202); 
$pdf->SetTextColor(253,253,253);
	  $pdf->Cell(0,10,'List of Databases',1,1,'C',1);
	  $pdf->TOC_Entry('List of Databases', 0);
	  $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('Times','',14);	
while($query_row=mysql_fetch_assoc($query)){
$dbuid=$query_row['dbuid'];
$databaseName =$query_row['dbname'];
$environ =$query_row['environ'];
	$pdf->Cell(0,10,'Database:'.$databaseName.'   ['.$environ.']',0,1,'L'); 
	}
	$areastatquery=mysql_query("SELECT  * FROM bireport where date(date) = '$dt'");
if(mysql_num_rows($areastatquery))
{
	if($query = mysql_query("SELECT dbuid,dbname FROM configureddb INNER JOIN dbalogin where  dbalogin.username ='$current_user' ")){
	$pdf->setFillColor(63,139,202); 
$pdf->SetTextColor(253,253,253);
    $pdf->SetFont('Times','',20);	
	$pdf->Cell(0,10,'Database BI Size Report',1,1,'C',1);
	$pdf->TOC_Entry('Database BI Size Report', 0);
		  $pdf->SetTextColor(0,0,0);
	while($query_row=mysql_fetch_assoc($query)){
	$dbuid=$query_row['dbuid'];
	$databaseName =$query_row['dbname'];	
    $pdf->SetFont('Arial','B',15);
    $pdf->Ln(10);
	$pdf->ln(1);
	$biquery=mysql_query("SELECT   time(B.date) as time,date(B.date) as date,B.bisize as bisize, (B.bisize - A.bisize) AS growth,B.clustersize as clustersize , B.commits as commits FROM bireport A CROSS JOIN bireport B  WHERE B.date IN (SELECT MIN(C.date) FROM bireport C WHERE C.date > A.date)  and date(B.date) = '$dt' ORDER BY B.date ASC ");
if(mysql_num_rows($biquery)){
	$pdf->TOC_Entry($databaseName, 1);
$pdf->SetFont('Arial','B',14);
$pdf->setFillColor(63,139,202); 
$pdf->SetTextColor(253,253,253);
$pdf->Cell(0,7,'Database: '.$databaseName,1,1,'C',1); 
$pdf->SetTextColor(0,0,0);
				$pdf->AddCol('time',26,'Time','C');
				$pdf->AddCol('date',27,'Date','C');
				$pdf->AddCol('bisize',64,'BI Size (in KB)');
				$pdf->AddCol('growth',50,'Growth (in KB)');
				$pdf->AddCol('clustersize',55,'Cluster Size (in KB)');
				$pdf->AddCol('commits',55,'Commits');
				$prop=array('HeaderColor'=>array(66,139,202),
            'color1'=>array(255,255,255),
            'color2'=>array(255,255,255));
		
$pdf->Table("SELECT   time(B.date) as time,date(B.date) as date,B.bisize as bisize, (B.bisize - A.bisize) AS growth,B.clustersize as clustersize , B.commits as commits FROM bireport A CROSS JOIN bireport B  WHERE B.date IN (SELECT MIN(C.date) FROM bireport C WHERE C.date > A.date AND C.dbid ='$dbuid') AND A.dbid ='$dbuid' AND B.dbid ='$dbuid' and date(B.date) = '$dt' ORDER BY B.date ASC ",$prop);
}
else {
$pdf->SetFont('Times','',26);
	 $pdf->Ln(10);
	$pdf->Cell(0,10,'No Matching Records Found For Database '.$databaseName,0,1,'C');
}
}
}
}
else
{
$pdf->SetFont('Times','',26);
	 $pdf->Ln(30);
	$pdf->Cell(0,10,'No Matching Records Found For Any Database ',0,1,'C');
	$pdf->SetFont('Times','',20);
}
	$pdf->stopPageNums();
	$pdf->insertTOC(2);
	$pdf->AutoPrint(true);
$pdf->Output();
mysql_close($connect);
}
?>
 