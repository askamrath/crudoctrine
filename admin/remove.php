<?php
/*
 * Cru Doctrine
 * Admin - Remove Handler
 * Campus Crusade for Christ
 */

require_once("../config.inc.php"); 
require_once("../Database.singleton.php");

function deleteModule($id){

    global $db;

    //delete attached sections
    $sql = "SELECT * FROM section WHERE ModuleId = ".$id;
    //execute query
    $sections = $db->fetch_array($sql);
    foreach($sections as $section){
        deleteSection($section['ID']);
    }
    
    //delete module
    $sql = "DELETE FROM module WHERE ID= ".$id;
    //execute query
    $db->query($sql);    
}

function deleteSection($id){

    global $db;
    
    //delete attached pages
    $sql = "SELECT * FROM page WHERE SectionId = ".$id;
    $pages = $db->fetch_array($sql);
    foreach($pages as $page){
        deletePage($page['ID']);
    }
    
    //delete section
    $sql = "DELETE FROM section WHERE ID= ".$id;
    //execute query
    $db->query($sql);
}

function deletePage($id){

    global $db;
    
    //delete attached elements
    $sql = "SELECT * FROM element WHERE PageId = ".$id;
    $elements = $db->fetch_array($sql);

    foreach($elements as $element) {
        if ($element['Type'] == 'input'){
             $sql = "DELETE FROM response WHERE InputId = ".$element['ElementId'];
             //execute query
             $db->query($sql);
         }

        $sql = "DELETE FROM ".$element['Type']." WHERE ID = ".$element['ElementId'];
        //execute query
        $db->query($sql);

        $sql = "DELETE FROM element WHERE ElementId = ".$element['ElementId'];
        //execute query
        $db->query($sql);
    }

    //delete progress
    $sql = "DELETE FROM progress WHERE PageId = ".$id;
    $db->query($sql);

    //delete page
    $sql = "DELETE FROM page WHERE ID = ".$id;
    $db->query($sql);
}

try {

    //get values
    $ajax       = isset($_POST['ajax'])     ? true : false;

    $type       = isset($_POST['type'])     ? $_POST['type'] : '';
    $id         = isset($_POST['id'])       ? $_POST['id'] : '';

    $errors     = isset($_POST['errors'])   ? $_POST['errors'] : '';

    //initialize the database object
    $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE); 
    $db->connect();

    switch($type){

        case 'module':
            deleteModule($id);
            break;

        case 'section':
            deleteSection($id);
            break;

        case 'page':
            deletePage($id);
            break;

    }

    $db->close();

    //if ajax, return success
    if ($ajax) {

        echo 1;
    }

} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}
?>