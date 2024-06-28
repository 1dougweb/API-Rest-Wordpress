jQuery(document).ready(function($) {
    function loadPosts() {
        $('#keep-nomad-posts').html('<p>Loading posts...</p>'); // Add a loading message
        
        $.ajax({
            url: keepNomad.ajax_url,
            method: 'POST',
            data: {
                action: 'keep_nomad_get_posts'
            },
            success: function(response) {
                if (response.success) {
                    var posts = JSON.parse(response.data);
                    var output = '<ul class="keep-nomad-post-list">';
                    $.each(posts, function(index, post) {
                        output += '<li class="keep-nomad-post-item" data-id="' + post.id + '">' + post.title.rendered + '</li>';
                    });
                    output += '</ul>';
                    $('#keep-nomad-posts').html(output);
                }
            }
        });
    }

    $(document).on('click', '.keep-nomad-post-item', function() {
        var postId = $(this).data('id');
        $('#keep-nomad-posts').html('<p>Loading post...</p>'); // Add a loading message
        
        $.ajax({
            url: keepNomad.ajax_url,
            method: 'POST',
            data: {
                action: 'keep_nomad_fetch_post',
                post_id: postId
            },
            success: function(response) {
                if (response.success) {
                    var post = JSON.parse(response.data);
                    var output = '<div class="keep-nomad-post-content">';
                    output += '<h2>' + post.title.rendered + '</h2>';
                    output += '<div>' + post.content.rendered + '</div>';
                    output += '<button id="keep-nomad-back">' + keepNomad.i18n.back + '</button>';
                    output += '</div>';
                    $('#keep-nomad-posts').html(output);
                }
            }
        });
    });

    $(document).on('click', '#keep-nomad-back', function() {
        loadPosts();
    });

    loadPosts();
});