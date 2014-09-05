<?php 
/*
Template Name: Live Streams
*/
?>
<?php get_header(); ?>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/video/jwplayer.js"></script>

<section class="page">
  <div class="container">
    <?php the_title('<h1 class="title">','</h1>',true); ?>
    <?php the_content(); ?>
    <div id="playlist1">
      Loading video...
    </div>
  </div>
</section>

<script type="text/javascript">
  jwplayer('playlist1').setup  ({'flashplayer':'<?php bloginfo('template_url'); ?>/video/player.swf','id':'playlist1','width':'860','height':'360',
        'playlist.position':'right','playlist.size':'240','repeat':'none','autostart':'false',
        'controlbar':'bottom','dock':'false','stretching':'fill',
        'modes':[{type:'flash',src:'<?php bloginfo('template_url'); ?>/video/player.swf',
            config:{'provider':'rtmp','playlist':[
               {'file':'FOSS4G2014KEY','title':'Keynotes','description':'and Invited Talks',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360k.jpg',
               'streamer':'rtmp://54.185.85.214:1935/redirect/OceanLive'},
               {'file':'FOSS4G201401','title':'Track 1',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg',
               'streamer':'rtmp://54.185.85.214:1935/redirect/OceanLive'},
               {'file':'FOSS4G201402','title':'Track 2',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg',
               'streamer':'rtmp://54.185.85.214:1935/redirect/OceanLive'},
               {'file':'FOSS4G201403','title':'Track 3',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg',
               'streamer':'rtmp://54.185.85.214:1935/redirect/OceanLive'},
               {'file':'FOSS4G201404','title':'Track 4',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg',
               'streamer':'rtmp://54.185.85.214:1935/redirect/OceanLive'},
               {'file':'FOSS4G201405','title':'Track 5',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg',
               'streamer':'rtmp://54.185.85.214:1935/redirect/OceanLive'},
               {'file':'FOSS4G201406','title':'Track 6',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg',
               'streamer':'rtmp://54.185.85.214:1935/redirect/OceanLive'},
               {'file':'FOSS4G201407','title':'Track 7',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg',
               'streamer':'rtmp://54.185.85.214:1935/redirect/OceanLive'},
               {'file':'FOSS4G201408','title':'Track 8',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg',
               'streamer':'rtmp://54.185.85.214:1935/redirect/OceanLive'}
            ]}
         },
         {type:'html5',
            config:{'provider':'video','playlist':[
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G2014KEY/playlist.m3u8','title':'Keynotes','description':'and Invited Talks',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360k.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201401/playlist.m3u8','title':'Track 1',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201402/playlist.m3u8','title':'Track 2',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201403/playlist.m3u8','title':'Track 3',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201404/playlist.m3u8','title':'Track 4',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201405/playlist.m3u8','title':'Track 5',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201406/playlist.m3u8','title':'Track 6',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201407/playlist.m3u8','title':'Track 7',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201408/playlist.m3u8','title':'Track 8',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'}
            ]}
         },
         {type:'download',
            config:{'provider':'video','playlist':[
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G2014KEY/playlist.m3u8','title':'Keynotes','description':'And Invited Talks',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360k.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201401/playlist.m3u8','title':'Track 1',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201402/playlist.m3u8','title':'Track 2',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201403/playlist.m3u8','title':'Track 3',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201404/playlist.m3u8','title':'Track 4',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201405/playlist.m3u8','title':'Track 5',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201406/playlist.m3u8','title':'Track 6',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201407/playlist.m3u8','title':'Track 7',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360a.jpg'},
               {'file':'http://54.185.85.214:1935/OceanLive/FOSS4G201408/playlist.m3u8','title':'Track 8',
               'image':'http://e3webcasting.com/wp-content/uploads/2014/09/FOSS4G-640x360b.jpg'}
            ]}
  }]});
</script>
<?php get_footer(); ?>

