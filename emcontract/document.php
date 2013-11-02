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
 *       \file       htdocs/emcontract/document.php
 *       \ingroup    employment_contract
 *       \brief      Page of linked files onto employment contract
 */

$res=@include("../main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/emcontract/class/emcontract.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/emcontract/core/lib/emcontract.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

$langs->load("other");
$langs->load("emcontract@emcontract");
$langs->load("companies");

$id = GETPOST('id','int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action','alpha');
$confirm = GETPOST('confirm','alpha');

// Security check
$id = (GETPOST('socid','int') ? GETPOST('socid','int') : GETPOST('id','int'));
if ($user->societe_id > 0) $id=$user->societe_id;
$result = restrictedArea($user,'societe',$id,'&societe');

// Get parameters
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha');
$page = GETPOST('page','int');
if ($page == -1) { $page = 0; }
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortorder) $sortorder="ASC";
if (! $sortfield) $sortfield="name";


$object = new Emcontract($db);
$object->fetch($id, $ref);

$upload_dir = $conf->emcontract->dir_output.'/'.dol_sanitizeFileName($object->ref);
$modulepart='emcontract';


/*
 * Actions
 */

if (GETPOST('sendit','alpha') && ! empty($conf->global->MAIN_UPLOAD_DOC))
{
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	dol_add_file_process($upload_dir,0,1,'userfile');
}

// Delete
else if ($action == 'confirm_deletefile' && $confirm == 'yes')
{
	if ($object->id > 0)
	{
		$langs->load("other");
		$object->fetch_thirdparty();

		$file = $upload_dir . '/' . GETPOST('urlfile');	// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
		$ret=dol_delete_file($file,0,0,0,$object);
		if ($ret) setEventMessage($langs->trans("FileWasRemoved", GETPOST('urlfile')));
		else setEventMessage($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), 'errors');
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
		exit;
	}
}


/*
 * View
 */

$form = new Form($db);

llxHeader(array(),$langs->trans('ContractTitle'));

if ($object->id)
{
	$object->fetch_thirdparty();

	$head=emcontract_prepare_head($object, $user);

	dol_fiche_head($head,'documents',$langs->trans("ContractTitle"),0,'emcontract@emcontract');


	// Construit liste des fichiers
	$filearray=dol_dir_list($upload_dir,"files",0,'','\.meta$',$sortfield,(strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);
	$totalsize=0;
	foreach($filearray as $key => $file)
	{
		$totalsize+=$file['size'];
	}


    print '<table class="border" width="100%">';

    $linkback = '<a href="'.DOL_URL_ROOT.'/emcontract/index.php'.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';

  	// Ref
  	print '<tr><td width="30%">'.$langs->trans("Ref").'</td><td>';
  	print $form->showrefnav($object, 'id', $linkback, 1, 'rowid', 'ref', '');
  	print '</td></tr>';

	// Societe
	//print "<tr><td>".$langs->trans("Company")."</td><td>".$object->client->getNomUrl(1)."</td></tr>";

    print '<tr><td>'.$langs->trans("NbOfAttachedFiles").'</td><td colspan="3">'.count($filearray).'</td></tr>';
    print '<tr><td>'.$langs->trans("TotalSizeOfAttachedFiles").'</td><td colspan="3">'.$totalsize.' '.$langs->trans("bytes").'</td></tr>';
    print '</table>';

    print '</div>';

    /*
     * Confirmation suppression fichier
     */
    if ($action == 'delete')
    {
    	$ret=$form->form_confirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&urlfile='.urlencode($_GET["urlfile"]), $langs->trans('DeleteFile'), $langs->trans('ConfirmDeleteFile'), 'confirm_deletefile', '', 0, 1);
    	if ($ret == 'html') print '<br>';
    }

    // Affiche formulaire upload
   	$formfile=new FormFile($db);
	  $formfile->form_attach_new_file(DOL_URL_ROOT.'/emcontract/document.php?id='.$object->id,'',0,0,$user->rights->emcontract->add,50,$object);


	// List of document
	$param='&id='.$object->id;
	$formfile->list_of_documents($filearray,$object,'emcontract',$param);

}
else
{
	print $langs->trans("UnkownError");
}

llxFooter();

$db->close();
?>
