jQuery(document).ready(function($){

	if($("#pcb_private_tab_predefined_type_contact_form").length){
        $("#pcb_private_tab_predefined_type_contact_form").pcb_select2({
          ajax: {
            url: UPMPCF7.AdminAjax,
            dataType: 'json',
            delay: 250,
            method: "POST",
            data: function (params) {
              return {
                q: params.term, // search term
                action: 'pcb_load_published_cf7_forms',
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

    $('#pcb_private_tab_predefined_type').change(function(){
    	var predefined_type = $(this).val();

    	$('.pcb_predefined_post_meta_row').hide();
    	switch(predefined_type){
    		case 'contact_form_7':
    			$('#pcb_private_tab_predefined_type_cf7_panel').show();
    			break;
    	}
    });
});