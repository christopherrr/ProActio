<?php
/*******************************************************************************
 *******************************************************************************
 **                                                                           **
 **                                                                           **
 **  Copyright 2015-2017 J K Technosoft                  					  **
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
 * Author:Ezaz Ul Shafi War
 *
 *	J K Technosoft
 *	http://www.jktech.com
 *	August 11, 2015
 *
 *
 * History:
 *
 */

header('Content-type: application/json');
@$option=$_GET['dbuid'];
require_once ("../sqlconnect.php");
if(isset($_SESSION['sql'])){
@$query = mysqli_query($connect,"SELECT * FROM configureddb where dbuid='$option'");
while($query_row=mysqli_fetch_assoc($query)){
$username=$query_row['dbuser'];
$password =$query_row['dbpass'];
$hostname =$query_row['server'];
$portnumber =$query_row['port'];
$databaseName =$query_row['dbname'];
}
$conn_string = "Driver={Progress OpenEdge 11.3 driver}; 
    HostName=" . @$hostname . "; 
    PortNumber=". @$portnumber ."; 
    DatabaseName=" . @$databaseName . "; 
    DefaultIsolationLevel=READ COMMITTED;";
	
if(@$conn = odbc_connect($conn_string, $username, $password, SQL_CUR_USE_ODBC)){
$query =$query ="select \"_DbStatus-CreateDate\" , \"_DbStatus-Starttime\" , \"_DbStatus-State\" , \"_DbStatus-DbBlkSize\" , \"_DbStatus-TotalBlks\" , \"_DbStatus-EmptyBlks\" from PUB.\"_DbStatus\"";
$result = odbc_exec($conn, $query);
    while ($row = odbc_fetch_array($result)) {
	if($row['_DbStatus-state']==1)
	$row['_DbStatus-state']="Open";
	$row['_DbStatus-TotalBlks']=round($row['_DbStatus-TotalBlks']*$row['_DbStatus-DbBlkSize']/1024/1024,2).' MB';
	$row['_DbStatus-EmptyBlks']=round($row['_DbStatus-EmptyBlks']*$row['_DbStatus-DbBlkSize']/1024/1024,2).' MB';
        $row['_DbStatus-DbBlkSize']=$row['_DbStatus-DbBlkSize']. ' Bytes';
		$json[]=$row;
    }
odbc_close($conn);
mysqli_close($connect);
echo json_encode($json);
}
else
echo "[]";
}
else
echo "[]";
?>