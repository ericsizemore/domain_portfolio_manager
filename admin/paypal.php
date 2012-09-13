<?php

/**
* @author    Eric Sizemore <admin@secondversion.com>
* @package   Domain Portfolio Manager
* @link      http://domain-portfolio.secondversion.com/
* @version   1.0.1
* @copyright (C) 2010 - 2012 Eric Sizemore
* @license   http://domain-portfolio.secondversion.com/docs/license.html GNU Public License
*
*            This program is free software: you can redistribute it and/or modify
*            it under the terms of the GNU General Public License as published by
*            the Free Software Foundation, either version 3 of the License, or
*            (at your option) any later version.
*
*            This program is distributed in the hope that it will be useful,
*            but WITHOUT ANY WARRANTY; without even the implied warranty of
*            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*            GNU General Public License for more details.
*
*            You should have received a copy of the GNU General Public License
*            along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* April 23rd, 2010: I sold Domain Name Portfolio 1.6.0 to Stephen Cox.
* June 21st, 2010 : Stephen sold it to Stu Buckingham. 
*
* As of June 21st, 2010 - Stu Buckingham technically is the copyright holder to DNP up to
* 1.6.x and he rebranded to DNS Portfolio.
*
* After more than a year of different people coming to me, wanting me to continue working on 
* the script, I finally decided to pick it back up again. Since it is under the GNU GPL, I am 
* able to do so. My continuation of the script will be called Domain Portfolio Manager.
*/

/**
* This file was not part of the original DNP, this is exclusive to DPM.
*/

define('IN_DPM', true);
define('IN_ADMIN', true);
require_once('../includes/global.php');

// Logged in?
if (!$adm->verify_auth())
{
	redirect('index.php');
}

// ################################################################
$getlogsql = "
	SELECT domains.*, logs.*
	FROM " . TABLE_PREFIX . "paypal_log AS logs 
	LEFT JOIN " . TABLE_PREFIX . "domains AS domains ON(logs.item_number = domains.domainid)
	ORDER BY domains.domain ASC
";

// Will hold all log data to pass to the template file.
$logs = array();

// This is used both in pagination and to determine if there were any results.
$numlogs = $db->num_rows($db->query($getlogsql));

// Pagination
$pagination = paginate($numlogs, (isset($_GET['page']) ? intval($_GET['page']) : 0));

// Execute the query, and if there are any results, build the log table.
$getlogs = $db->query("
	$getlogsql
	LIMIT $pagination[limit], " . $config->get('maxperpage') . "
") or $db->raise_error();

if ($numlogs > 0)
{
	$row = 0;

	while ($log = $db->fetch_array($getlogs))
	{
		$log['dateline'] = dpm_date('M jS, Y', $log['dateline']);
		$log['class'] = ($row & 1);
		$logs[] = $log;

		$row++;
	}
}
$db->free_result($getlogs);

// ################################################################
// Output page
$pagetitle = 'Paypal Logs';

include("$template_admin/paypal.php");

?>