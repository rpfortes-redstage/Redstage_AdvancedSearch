require(['jquery', 'jquery/ui', 'mage/template', 'mage/url'], function($, _, template, urlbuilder){
    
    var url =  urlbuilder.build('advancedsearch/search/index');
    var server_url = urlbuilder.build('');

    $('#search').keyup(function(event) {
        var query_string =  $('#search').val();
        if(query_string.length > 3) {
            $('#redstage-advancedsearch-loader').show();
            $('#search').prop('disabled', true);
            $.ajax({
                url: url,
                data: {"name": query_string},
                type: "GET",
                dataType: 'json'
            }).done(function (response) {
                var html = "";
                $.each(response.data, function () {
                    var product_url = server_url+ this['url_key']+'.html';
                    html += template('#search-template', {data: {name: this['name'], url: product_url}});
                });
                $('#search_autocomplete ul').html(html);
                $('#search_autocomplete').show();
                $('#redstage-advancedsearch-loader').hide();
                $('#search').prop('disabled', false);
            })
        }
    });
});