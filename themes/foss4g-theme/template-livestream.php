<?php 
/*
Template Name: Live Streams
*/
?>
<?php get_header(); ?>
<style type="text/css" media="screen">
#contact-container {
  display: none;
}
.btn-player {
  white-space: normal; 
}
</style>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/video/jwplayer.js"></script>
<section class="page">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php the_title('<h1 class="title">','</h1>',true); ?>
        <?php the_content(); ?>
      </div>
    </div>
    <div class="row">
     <div class="col-md-9" >
        <div class="col-md-9" id="player-col">
          <div id="playlist1">
            Loading video...
          </div>
        </div>
         <div class="col-md-2">
           <button data-playlist="0" id='button-track-0' type="button" class="active btn-player btn-block btn btn-default">Keynotes and Invited Talks</button>
           <button data-playlist="1" id='button-track-1' type="button" class="btn-player btn-block btn btn-default">Track 1</button>
           <button data-playlist="2" id='button-track-2' type="button" class="btn-player btn-block btn btn-default">Track 2</button>
           <button data-playlist="3" id='button-track-3' type="button" class="btn-player btn-block btn btn-default">Track 3</button>
           <button data-playlist="4" id='button-track-4' type="button" class="btn-player btn-block btn btn-default">Track 4</button>
           <button data-playlist="5" id='button-track-5' type="button" class="btn-player btn-block btn btn-default">Track 5</button>
           <button data-playlist="6" id='button-track-6' type="button" class="btn-player btn-block btn btn-default">Track 6</button>
           <button data-playlist="7" id='button-track-7' type="button" class="btn-player btn-block btn btn-default">Track 7</button>
           <button data-playlist="8" id='button-track-8' type="button" class="btn-player btn-block btn btn-default">Track 8</button>
         </div>
      </div>
      <div class="col-md-3 sidebar">
        <?php
        get_template_part( 'includes/partials/sidebar-sponsors', 'sidebar-sponsors' );
        ?>
      </div>
    </div> <!-- row -->
  </div> <!-- container -->
</section>

<script type="text/javascript">

jwplayer('playlist1').setup({
 'flashplayer': '<?php bloginfo('template_url'); ?>/video/player.swf',
 'id': 'playlist1',
 'playlist.position': 'none',
 'width': '100%',
 'repeat': 'none',
 'autostart': 'false',
 'controlbar': 'bottom',
 'dock': 'false',
 'stretching': 'fill',
 'modes': [{
   type: 'flash',
   src: '<?php bloginfo('template_url'); ?>/video/player.swf',
    config: {
      'provider': 'rtmp',
      'playlist': [{
        'file': 'FOSS4G2014KEY',
        'title': 'Keynotes',
        'description': 'and Invited Talks',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360k.jpg',
        'streamer': 'rtmp://54.185.85.214:1935/redirect/OceanLive'
      }, {
        'file': 'FOSS4G201401',
        'title': 'Track 1',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg',
        'streamer': 'rtmp://54.185.85.214:1935/redirect/OceanLive'
      }, {
        'file': 'FOSS4G201402',
        'title': 'Track 2',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg',
        'streamer': 'rtmp://54.185.85.214:1935/redirect/OceanLive'
      }, {
        'file': 'FOSS4G201403',
        'title': 'Track 3',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg',
        'streamer': 'rtmp://54.185.85.214:1935/redirect/OceanLive'
      }, {
        'file': 'FOSS4G201404',
        'title': 'Track 4',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg',
        'streamer': 'rtmp://54.185.85.214:1935/redirect/OceanLive'
      }, {
        'file': 'FOSS4G201405',
        'title': 'Track 5',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg',
        'streamer': 'rtmp://54.185.85.214:1935/redirect/OceanLive'
      }, {
        'file': 'FOSS4G201406',
        'title': 'Track 6',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg',
        'streamer': 'rtmp://54.185.85.214:1935/redirect/OceanLive'
      }, {
        'file': 'FOSS4G201407',
        'title': 'Track 7',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg',
        'streamer': 'rtmp://54.185.85.214:1935/redirect/OceanLive'
      }, {
        'file': 'FOSS4G201408',
        'title': 'Track 8',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg',
        'streamer': 'rtmp://54.185.85.214:1935/redirect/OceanLive'
      }]
    }
  }, {
    type: 'html5',
    config: {
      'provider': 'video',
      'playlist': [{
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G2014KEY/playlist.m3u8',
        'title': 'Keynotes',
        'description': 'and Invited Talks',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360k.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201401/playlist.m3u8',
        'title': 'Track 1',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201402/playlist.m3u8',
        'title': 'Track 2',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201403/playlist.m3u8',
        'title': 'Track 3',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201404/playlist.m3u8',
        'title': 'Track 4',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201405/playlist.m3u8',
        'title': 'Track 5',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201406/playlist.m3u8',
        'title': 'Track 6',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201407/playlist.m3u8',
        'title': 'Track 7',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201408/playlist.m3u8',
        'title': 'Track 8',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'
      }]
    }
  }, {
    type: 'download',
    config: {
      'provider': 'video',
      'playlist': [{
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G2014KEY/playlist.m3u8',
        'title': 'Keynotes',
        'description': 'And Invited Talks',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360k.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201401/playlist.m3u8',
        'title': 'Track 1',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201402/playlist.m3u8',
        'title': 'Track 2',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201403/playlist.m3u8',
        'title': 'Track 3',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201404/playlist.m3u8',
        'title': 'Track 4',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201405/playlist.m3u8',
        'title': 'Track 5',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201406/playlist.m3u8',
        'title': 'Track 6',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201407/playlist.m3u8',
        'title': 'Track 7',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'
      }, {
        'file': 'http://54.185.85.214:1935/OceanLive/FOSS4G201408/playlist.m3u8',
        'title': 'Track 8',
        'image': 'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'
      }]
    }
  }]
});
function prepButtons(){
  jQuery('.btn-player btn-block').each(function(i){
   jQuery(this).click(function(){
     jQuery('.active.btn-player btn-block').removeClass('active')
     var btn = jQuery(this);
     btn.addClass('active');
     jwplayer().playlistItem(this.dataset.playlist)
   });
  });
};
prepButtons();

</script>

<?php get_footer(); ?>

