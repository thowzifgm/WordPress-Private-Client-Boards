jQuery(document).ready(function($) {

    var url = window.location.href;
    // if(url.indexOf('pcb_pp_file_id') != -1){
    //    jQuery('.upmp-private-page-files-tab').trigger('click');            
    // }

});

function pcb_front_formatRepo (repo) {
    if (repo.loading) return repo.text;

    var markup = '<div class="clearfix">' +
    '<div class="col-sm-1">' +
    '' +
    '</div>' +
    '<div clas="col-sm-10">' +
    '<div class="clearfix">' +
    '<div class="col-sm-6">' + repo.name + '</div>' +
    '</div>';


    markup += '</div></div>';

    return markup;
}

function pcb_front_formatRepoSelection (repo) {
    return repo.name || repo.text;
}