(function( $ ) {
  'use strict';

  $(function() {
    var initFroontPanel = function() {
      var froont_meta_box = $('#froont_meta_box');

      if (!froont_meta_box.length) {
        return;
      }

      var froont = froont_meta_box.find('#froont');
      var froont_import = froont_meta_box.find('#froont_import');
      var froont_project = froont_meta_box.find('#froont_project');
      var project_delete = froont_meta_box.find('.delete');
      var project = froont_meta_box.find('.project');
      var error = froont_meta_box.find('.error-message');
      var loader = froont_meta_box.find('.spinner');

      froont_import.on('click', function(e){
        e.preventDefault();
        importProject();
      });

      var importProject = function() {
        var froont_project_url = froont_project.val();
        if (!froont_project_url) {
          return;
        }
        froont_import.attr('disabled', true);
        error.text('');
        loader.addClass('is-active');

        $.ajax(ajaxurl, {
          'method': 'POST',
          'dataType': 'json',
          'data': {
            'action': 'froont_import',
            'url': froont_project_url
          },
          'success': function(response) {
            var title = froont_project.find('option:selected').text();
            var data = {
              'date': response.date,
              'url': froont_project_url,
              'title': title
            };
            froont.val(JSON.stringify(data));
            project.find('span').text(title + ' / ' + response.date_format);
            project.removeClass('hidden');
          },
          'error': function(response) {
            error.text(response.responseJSON.error);
          },
          'complete': function() {
            loader.removeClass('is-active');
            froont_import.attr('disabled', false);
          }
        });

      };

      project_delete.on('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to remove Froont project from this post/page?')) {
          froont.val('');
          project.addClass('hidden');
        }
      });

    };
    initFroontPanel();
  });

})( jQuery );
