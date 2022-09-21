jQuery(document).ready(function($) {
    if($("#pcb_private_page_user").length){
        $("#pcb_private_page_user").pcb_select2({
          ajax: {
            url: UPMPAdmin.AdminAjax,
            dataType: 'json',
            delay: 250,
            method: "POST",
            data: function (params) {
              return {
                q: params.term, // search term
                page: params.page,
                action: 'pcb_load_private_page_users',
              };
            },
            processResults: function (data, page) {
              return {
                results: data.items
              };
            },
            cache: true
          },
          escapeMarkup: function (markup) { return markup; }, 
          minimumInputLength: 1,
          templateResult: pcb_formatRepo, 
          templateSelection: pcb_formatRepoSelection 
        });
    }
    
    $("#pcb_private_page_user_load_form").submit(function(e){
        
        $("#upmp-message").removeClass('upmp-message-info-error').removeClass('upmp-message-info-success').hide();
        
        if($("#pcb_private_page_user").val() == '0'){
            e.preventDefault();
            $("#upmp-message").addClass('upmp-message-info-error');
            $("#upmp-message").html(UPMPAdmin.Messages.userEmpty).show();
        }
    });

    if($("#pcb_private_page_id").length){
        $("#pcb_private_page_id").pcb_select2({
          ajax: {
            url: UPMPAdmin.AdminAjax,
            dataType: 'json',
            delay: 250,
            method: "POST",
            data: function (params) {
              return {
                q: params.term, // search term
                action: 'pcb_load_published_pages',
              };
            },
            processResults: function (data, page) {
              return {
                results: data.items
              };
            },
            cache: true
          },
          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
          minimumInputLength: 1,
          templateResult: pcb_formatRepo, // omitted for brevity, see the source of this page
          templateSelection: pcb_formatRepoSelection // omitted for brevity, see the source of this page
        });
    }    

    if($("#pcb_membership_page_id").length){
        $("#pcb_membership_page_id").pcb_select2({
          ajax: {
            url: UPMPAdmin.AdminAjax,
            dataType: 'json',
            delay: 250,
            method: "POST",
            data: function (params) {
              return {
                q: params.term, // search term
                action: 'pcb_load_published_pages',
              };
            },
            processResults: function (data, page) {
              return {
                results: data.items
              };
            },
            cache: true
          },
          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
          minimumInputLength: 1,
          templateResult: pcb_formatRepo, // omitted for brevity, see the source of this page
          templateSelection: pcb_formatRepoSelection // omitted for brevity, see the source of this page
        });
    }



    $("#pcb_private_tab_visibility").change(function(e){        
        if($(this).val() == 'role'){
            $("#pcb_private_tab_role_panel").show();
        }else{
            $("#pcb_private_tab_role_panel").hide();
        }
    });

    $("#pcb_private_tab_type").change(function(e){        
        if($(this).val() == 'predefined'){
            $("#pcb_private_tab_predefined_type_panel").show();
            $("#pcb_private_tab_upload_type_panel").hide();

        }else if($(this).val() == 'files'){
            $("#pcb_private_tab_upload_type_panel").show();
            $("#pcb_private_tab_predefined_type_panel").hide();
        }else{
            $("#pcb_private_tab_predefined_type_panel").hide();
            $("#pcb_private_tab_upload_type_panel").hide();
        }
    });

    $("#pcb_private_tab_upload_permission_type").change(function(e){        
        if($(this).val() == 'role'){
            $("#pcb_private_tab_upload_role_panel").show();
        }else{
            $("#pcb_private_tab_upload_role_panel").hide();
        }
    });

    


    
    
});

function pcb_formatRepo (repo) {
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

function pcb_formatRepoSelection (repo) {
    return repo.name || repo.text;
}

String.prototype.pcb_format = function() {
  var args = arguments;
  return this.replace(/{(\d+)}/g, function(match, number) { 
    return typeof args[number] != 'undefined'
      ? args[number]
      : match
    ;
  });
};