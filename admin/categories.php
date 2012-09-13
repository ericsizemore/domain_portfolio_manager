<?php

/**
* @author    Eric Sizemore <admin@secondversion.com>
* @package   Domain Portfolio Manager
* @link      http://domain-portfolio.secondversion.com/
* @version   1.0.1
* @copyright (C) 2010 - 2011 Eric Sizemore
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
*
* @file      ./admin/categories.php
*/

/**
* April 23rd, 2010: I sold Domain Name Portfolio 1.6.0 to Stephen Cox.
* June 21st, 2010 : Stephen sold it to Stu Buckingham. 
*
* As of June 21st, 2010 - Stu Buckingham technically is the copyright holder to DNP up to
* 1.6.x and he rebranded to DNS Portfolio.
*
* The script was originally created by me, Eric Sizemore, and has always been under
* the GNU GPL. So, with that in mind, I decided to continue development as the rights
* to 1.7.0 that was in development was not sold. Even with my rights to the code sold
* for previous versions, with the GPL, I still have the right to fork or continue development.
*
* After more than a year of different people coming to me, wanting me to continue working on 
* Domain Portfolio Manager, I finally decided to pick it back up again.
*
* So, this code now is based on the unreleased 1.7.0 I had, and has been re-versioned to start
* at 1.0.0. It's new name under me is: Domain Portfolio Manager.
*
* Enjoy. :)
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
$getcatsql = "
	SELECT catid, title, description
	FROM " . TABLE_PREFIX . "categories
	ORDER BY title ASC
";

// Will hold category data to be used in the template file
$cats = array();

// Used in pagination, and to determine if we even have any categories
$numcats = $db->num_rows($db->query($getcatsql));

// Pagination
$pagination = paginate($numcats, (isset($_GET['page']) ? intval($_GET['page']) : 0));

// Execute the query, and if there are any results, build the category table.
$getcats = $db->query("
	$getcatsql
	LIMIT $pagination[limit], " . $config->get('maxperpage') . "
") or $db->raise_error();

if ($numcats > 0)
{
	$row = 0;

	while ($cat = $db->fetch_array($getcats))
	{
		$cat['numdomains'] = $db->query("
			SELECT COUNT(domainid) AS count
			FROM " . TABLE_PREFIX . "dom2cat
			WHERE catid = $cat[catid]
		", true);
		$cat['numdomains'] = $cat['numdomains']['count'];
		$cat['class'] = ($row & 1);
		$cats[] = $cat;

		$row++;
	}
	$db->free_result($getcats);
}

// ################################################################
// Output page
$pagetitle = 'Categories';

include("$template_admin/categories.php");

?>