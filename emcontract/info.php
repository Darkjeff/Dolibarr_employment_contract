<?php
/* Copyright (C) 2013 Alexandre Spangaro  <alexandre.spangaro@gmail.com>
 * Copyright (C) 2013	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2013	Regis Houssin		    <regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file       htdocs/emcontract/info.php
 * 	\ingroup    employment_contract
 * 	\brief      Page to show a employment contract information
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/emcontract/core/lib/emcontract.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/emcontract/class/emcontract.class.php';

$langs->load("emcontract@emcontract");

// Security check
$id = (GETPOST('socid','int') ? GETPOST('socid','int') : GETPOST('id','int'));
if ($user->societe_id > 0) $id=$user->societe_id;
$result = restrictedArea($user,'societe',$id,'&societe');

/*
 * View
 */

llxHeader();

if ($id)
{
	$object = new Emcontract($db);
	$object->fetch($id);
	$object->info($id);
	
	$head = emcontract_prepare_head($object);
	
	dol_fiche_head($head,'info',$langs->trans("ContractTitle"),0,'emcontract@emcontract');

    print '<table width="100%"><tr><td>';
    dol_print_object_info($object);
    print '</td></tr></table>';
      
    print '</div>';
}

$db->close();

llxFooter();
?>
