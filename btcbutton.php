<?php
/**
 * @package Bitcoin_Button
 * @version 0.91
 */
/*
Plugin Name: Bitcoin Donate Button

Description: Adds a Bitcoin Donate Button (with QR-code in a popover) to your blogposts.   
Author: Alex Dijkstra
Version: 0.91
Author URI: http://lxer.eu/
*/

defined( 'ABSPATH' ) or die();


function add_btc_button($content) {
    if (!is_front_page()){      
        
      $excludepages =  explode(",", esc_attr( get_option('btcb_exclude'))) ;

      global $id; 
      if (! in_array($id,$excludepages)){ 
        $btcadr   = esc_attr( get_option('btcb_address') ) ;
        $btcsize  = esc_attr( get_option('btcb_size') );
        $btcbtext = esc_attr( get_option('btcb_btext') );
        $btcalign = esc_attr( get_option('btcb_align') );
        $btctitle = esc_attr( get_option('btcb_title') );
        $btcpoptitle = esc_attr( get_option('btcb_poptitle') );
        //$title       = 'Use this address to show your support ';
        $htmlcontent = '<div class=\"msg\">'.$btcadr.'<a href=\"bitcoin:'.$btcadr.'\"><img src=\"https://chart.googleapis.com/chart?chs='.$btcsize.'x'.$btcsize.'&cht=qr&chl='. $btcadr .'\"></a><br /><a href=\"https://bitcoin.org/en/getting-started\" target=\"_blank\">Start using Bitcoin.</a></div>';
        $content    .= '
        <script type="text/javascript">
         jQuery(document).ready(function() {
         $("#btcpopoverId").popover({
            html: true,
            title: "'.$btcpoptitle.'",
            content: "'.$htmlcontent .'",
         });
            $("#btcpopoverId").click(function (e) {
               e.stopPropagation();
            });
            $(document).click(function (e) {
      if (($(".popover").has( e.target).length == 0) || $(e.target).is(".close")) {
         $("#btcpopoverId").popover("hide");
         }
         });
         });
         </script><div class="btcb_button '.$btcalign.'"><h5><i>'.	$btctitle .' </i></h5>
<button id="btcpopoverId" class="popoverThis btn btn-xs btn-success '.$btcalign.'" >
<i class="icon-bitcoin"></i> '.$btcbtext.'</button>  </div>      
        
           ';
          }
    }
    return $content;
}


add_action( 'the_content', 'add_btc_button' );
// admin

add_action( 'admin_menu', 'my_plugin_menu' );

function my_plugin_menu() {
    add_options_page( 'BitcoinButton Options', 'BitcoinButton', 'manage_options', 'btcb-15-01', 'my_plugin_options' );
    }

function my_plugin_options() {
   if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
   }
   if(isset($_POST['btcb_address'])){
      //sanitize stuff and write t db 
      update_option('btcb_address', preg_replace("/[^A-Za-z0-9]/", "",$_POST['btcb_address']));
      update_option('btcb_size'   , preg_replace("/[^0-9]/", "",$_POST['btcb_size']));
      update_option('btcb_title'  , preg_replace("/[^A-Za-z0-9\ \! \. \?]/", "",$_POST['btcb_title']));
      update_option('btcb_btext'  , preg_replace("/[^A-Za-z0-9\ \! \.]/", "",$_POST['btcb_btext']));
      update_option('btcb_exclude', preg_replace("/[^0-9,]/", "",$_POST['btcb_exclude']));
      update_option('btcb_poptitle'  , preg_replace("/[^A-Za-z0-9\ \! \.]/", "",$_POST['btcb_poptitle']));
      update_option('btcb_align'  , $_POST['btcb_align']);      
   }?>
   <div class="wrap">
   <img width="110px" src="http://lxer.eu/bitcoin_dark_306x64.png"/><h3>Bitcoin Button </h3>
   <p>This plugin adds a small Button for Bitcoin donations below every post on your blog.</p>
   <p>The QR-code is generated by google.com, as this is considered the safest option.</p>
   <p>The popover needs Bootstrap3 CSS and jQuery to work correctly.</p>

   <p>Donations are made directly to your wallet, so no 3rd-party or Exchange is involved.</p>
   </div>
   <hr>
   <form method="post" action="">
    <?php settings_fields( 'btcb-settings-group' );
          do_settings_sections( 'btcb-settings-group' ); ?>
        <table class="form-table">  
           <tbody>
             <tr>
               <th scope="row"><label for="btcb_address">Your Bitcoin Address: </label></th>
                  <td>
                     <input type="text" class="regular-text" name="btcb_address" value="<?php echo esc_attr( get_option('btcb_address') ); ?>" />
                  </td>
             </tr>
             <tr>  
               <th scope="row"><label for="btcb_size">QR size (250): </label></th>
               <td> 
                  <input type="text" class="small-text" name="btcb_size" value="<?php echo esc_attr( get_option('btcb_size') ); ?>" /> px
               </td>
             </tr>   
             
             <tr>  
               <th scope="row"><label for="btcb_title">Title text : </label></th>
               <td> 
                  <input type="text" class="regular-test" name="btcb_title" value="<?php echo esc_attr( get_option('btcb_title') ); ?>" /> 
               </td>
             </tr>   

             <tr>  
               <th scope="row"><label for="btcb_poptitle">Popover Title text : </label></th>
               <td> 
                  <input type="text" class="regular-text" name="btcb_poptitle" value="<?php echo esc_attr( get_option('btcb_poptitle') ); ?>" /> 
               </td>
             </tr>  
             
             <tr>  
               <th scope="row"><label for="btcb_size">Button text : </label></th>
               <td> 
                  <input type="text" class="regular-text" name="btcb_btext" value="<?php echo esc_attr( get_option('btcb_btext') ); ?>" /> 
               </td>
             </tr>     

             <tr>  
               <th scope="row"><label for="btcb_size">Hide button on these pages: </label></th>
               <td> 
                  <input type="text" class="medium-text" name="btcb_exclude" value="<?php echo esc_attr( get_option('btcb_exclude') ); ?>" />  <i>example: 23,34,9</i>
               </td>
             </tr>               
             
             <tr>  
               <th scope="row"><label for="btcb_align">Align: </label></th>
                <td> 
                  <select name='btcb_align' id='btcb_align class='my_drop-down-class'>
                      <option value='pull-left'>left</option>
                      <option value='pull-right'>right</option>
                  </select>   
               </td>
             </tr> 
                                  
             
            </tbody>
        </table>     
   <?php submit_button(); ?>   
    </form>

<hr>
<p></p>
<h4>New to bitcoin?</h4>
 <p>If you have not used Bitcoin before, you might want learn a little about it first.<br />Here is a nice 3 minute video to get started: <a href="https://vimeo.com/110874487">What is Bitcoin</a>.
</p><p>You can get you own Bitcoin wallet from <a href="https://electrum.org/">Electrum.org</a> (desktop), <br/>
or a wallet for your mobile device, like <a href="https://play.google.com/store/apps/details?id=de.schildbach.wallet"> Bitcoin android </a>
or <a href="https://itunes.apple.com/app/breadwallet/id885251393">Breadwallet for iOS</a>.
</p>
<br /> 
<hr>

<?php
}
function register_mysettings() {
        register_setting( 'btcb-settings-group', 'btcb_address' );
        register_setting( 'btcb-settings-group', 'btcb_size' );
        register_setting( 'btcb-settings-group', 'btcb_btext' );
        register_setting( 'btcb-settings-group', 'btcb_exclude' );
        register_setting( 'btcb-settings-group', 'btcb_align' );
        register_setting( 'btcb-settings-group', 'btcb_title' );
        register_setting( 'btcb-settings-group', 'btcb_poptitle' );
}

//first run, set some defaults
register_activation_hook( __FILE__, 'plugin_activated' );

function plugin_activated(){
      // set some Default values when the the plugin is activated for the first time.
      update_option('btcb_size'   , "250");
      update_option('btcb_poptitle',"Use this address to show your support");
      update_option('btcb_address', "1GZ48BkjhRLFRzdHvyBwukRa6cggec1SrY");
      update_option('btcb_title'  , "Was this post helpfull?");
      update_option('btcb_btext'  , "Make a donation!");
      update_option('btcb_exclude', "");
      update_option('btcb_align'  , "pull-left");         
    }




?>
