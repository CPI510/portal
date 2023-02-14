<?php
/* 
Template Name: users 
Template Post Type: post, page, product 
*/
authAll();

get_header();

?>
<div class="section-header">
    <ol class="breadcrumb">
        <?php if(is_page('users')): ?> 
             <?php if( $_GET['z'] == 'folders' )
                echo "<li class='active'> ".MY_FOLDERS." </li>" ;
                else echo  "<li><a href='/users/?z=folders' class='text-primary'>".MY_FOLDERS."</a></li>";
        ?>
             <?php if( $_GET['z'] == 'add_file' ) { 
                $_SESSION['add_file']  = $_SERVER['QUERY_STRING']; 
                echo "<li class='active'>".FILES."</li>";
                } else {
                    echo "<li><a href='/users/?".$_SESSION['add_file']."' class='text-primary'>".COMMENTS."</a></li>";
                    } ?>
             <?php if ( $_GET['z'] == 'comment' ) 
             echo "<li class='active'>".COMMENTS."</li>";
             else echo "<li>".COMMENTS."</li>";
             ?>
        <?php endif;?>
    </ol>
</div>

<?php
pageCreate('/modules/users/');

get_Footer();
?>
