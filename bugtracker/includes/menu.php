<?php
function showMenu(){
    echo('<div class="menu" id="left">
        <table class="side_menu">
            <tr>
                <td><a href="new_bug.php">Een nieuwe bug indienen</a></td>
            </tr>
            <tr>
                <td><a href="overview_bugs.php">Overzicht ingediende bugs</a></td>
            </tr> 
            <tr>
                <td><a href="future_features.php">Toekomstige site-opties</a></td>  
            </tr> 
            <tr>
                <td><a href="edit_profile.php">Profiel bewerken</a></td>  
            </tr>    
            <tr>
                <td><a href="logout.php">Uitloggen</a></td>
            </tr>
        </table>
    </div>');
}
?>