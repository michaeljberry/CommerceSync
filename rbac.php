<?php
include_once 'core/init.php';
//$general->logged_out_protect();
/*
$role_descriptions = array(
	'President',
	'Marketing Manager',
	'Web Developer'
	);
$rbac->Roles->addPath('/president/marketing_manager/web_developer', $role_descriptions);
$role_descriptions = array(
	'President',
	'Marketing Manager',
	'E-commerce Specialist'
	);
$rbac->Roles->addPath('/president/marketing_manager/e_commerce_specialist', $role_descriptions);
$role_descriptions = array(
	'President',
	'Marketing Manager',
	'Graphic Designer'
	);
$rbac->Roles->addPath('/president/marketing_manager/graphic_designer',$role_descriptions);
$role_descriptions = array(
	'President',
	'Marketing Manager',
	'Marketing Assistant'
	);
$rbac->Roles->addPath('/president/marketing_manager/marketing_assistant',$role_descriptions);
$role_descriptions = array(
	'President',
	'Operations Manager',
	'Musikey Clerk'
	);
$rbac->Roles->addPath('/president/operations_manager/musikey_clerk',$role_descriptions);
//*/
//$rbac->Roles->remove(36);
//$rbac->Roles->remove(37);

/*
$perm_descriptions = array(
	'Management',
	'E-Commerce'
	);
$rbac->Permissions->addPath('/management/e_commerce', $perm_descriptions);
$perm_descriptions = array(
	'E-Commerce'
	);
$rbac->Permissions->addPath('/e_commerce', $perm_descriptions);
$perm_descriptions = array(
	'MusiKey'
	);
$rbac->Permissions->addPath('/musikey', $perm_descriptions);
//*/
//$rbac->Permissions->remove(2);
//$rbac->Permissions->remove(3);

//$rbac->Permissions->assign('president', 'management'); // President - Management
//$rbac->Permissions->assign('president', 'e_commerce'); // President - E-Commerce
//$rbac->Permissions->assign('president', 'musikey'); // President - MusiKey

//$rbac->Permissions->assign('marketing_manager', 'management'); // Marketing Manager - Management
//$rbac->Permissions->assign('marketing_manager', 'e_commerce'); // Marketing Manager - E-Commerce
//$rbac->Permissions->assign('marketing_manager', 'musikey'); // Marketing Manager - MusiKey

//$rbac->Permissions->assign('operations_manager', 'management'); // Operations Manager - Management
//$rbac->Permissions->assign('operations_manager', 'musikey'); // Operations Manager - MusiKey

//$rbac->Permissions->assign('musikey_clerk', 'musikey'); // Musikey Clerk - MusiKey

//$rbac->Permissions->assign('web_developer', 'e_commerce'); //Web Developer - E-Commerce'
//$rbac->Permissions->assign('e_commerce_specialist', 'e_commerce'); //E-commerce Specialist - E-Commerce

//$oldrole = '';
$newrole = 'marketing_manager';
$user_id = '842';
//$rbac->Users->unassign($oldrole, $user_id); //Me Dummy
echo $newrole . ' ' . $user_id;
$rbac->Users->assign($newrole, $user_id); //Me Dummy
if ($rbac->check('management', $user_id)) {
    echo 'true';
} else {
    echo 'false';
}
?>
