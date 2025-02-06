<?php


require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');

while (have_posts()) : the_post();

    $video_url = get_post_meta(get_the_ID(), 'recording_path', true);
    error_log('This is the video url :' . $video_url);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <style>
            .video-player {
                border-radius: 10px;
                width: 100vh;
                height: 100vh;
            }
        </style>
    </head>

    <body style="padding: 0; margin: 0;">
        <div class="card">
            <video class="video-player" controls style="width: 100%;">
                <source src="<?php echo $video_url ?>" type="video/mp4">Your browser does not support the video tag.
            </video>
            <!-- <h2><?php the_title(); ?></h2>
            <p><?php the_content(); ?></p>
            <p>Recording Date : <?php echo get_post_meta(get_the_ID(), 'recording_date', true); ?></p>
            <p>Recording Size : <?php echo get_post_meta(get_the_ID(), 'recording_size', true); ?></p>
            <p>Recording Duration : <?php echo get_post_meta(get_the_ID(), 'duration', true); ?></p>
        </div> -->
    </body>

    </html>
<?php endwhile; ?>