/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

document.addEventListener('DOMContentLoaded', function() {
    var deleteIcons = document.querySelectorAll('.delete');
    //console.log(deleteIcons);
    if (deleteIcons) {
        //console.log(deleteIcons);
        deleteIcons.forEach(function(deleteIcon) {
            //console.log(deleteIcon);
            deleteIcon.addEventListener('click', function() {
                var trickId = deleteIcon.getAttribute('data-id');
                //console.log(trickId);
                var xhr = new XMLHttpRequest();
                
                xhr.open('POST', '/snowtricks/delete/' + trickId, true);
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            location.reload();
                            console.log('La suppression a réussi !');
                        } else {
                            console.error('Erreur lors de la suppression : ' + xhr.status);
                        }
                    }
                };
                
                xhr.send();
            });
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var editIcons = document.querySelectorAll('.edit');
    //console.log(editIcons);
    if (editIcons) {
        editIcons.forEach(function(editIcon) {
            editIcon.addEventListener('click', function() {
                var trickId = editIcon.getAttribute('data-id');
                var xhr = new XMLHttpRequest();
                
                xhr.open('POST', '/snowtricks/edit/' + trickId, true);
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            console.log("L'édition a réussi !");
                        } else {
                            console.error("Erreur lors de l'édition : " + xhr.status);
                        }
                    }
                };
                
                xhr.send();
            })
        })

    }

})