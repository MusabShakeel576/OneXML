<?php
/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://musab.kedruga.com/
 * @since             1.0
 * @package           One-XML
 *
 * Plugin Name:     One-XML
 * Plugin URI:      https://musab.kedruga.com/
 * Description:     Clean XML code and make HTTP POST request at one-dc
 * Version:         0.1
 * Author:          MusabShakeel
 * Author URI:      https://musab.kedruga.com/
 * Text Domain:     one-xml
 * Domain Path:     /languages
 */
// One-XML - Clean XML code and make HTTP POST request at one-dc
// Copyright (C) 2020  Musab

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <https://www.gnu.org/licenses/>.

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

//Admin Dashboard
add_action("admin_menu", "addMenu");

function addMenu() {
  add_menu_page("One-XML", "One-XML", 4, "one-xML", "onexmlFunction" );
}

function onexmlFunction() {
	echo '
	<div class="container" style="padding: 30px;">
		<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post" id="xml-form" enctype="multipart/form-data">
			<label for="xml-text" style="line-height: 40px; font-size: 20px">XML Code:</label>
			<br>
			<textarea id="xml-text" name="xml-text" rows="30" cols="150" style="font-size: .8rem; letter-spacing: 1px; padding: 10px; line-height: 1.5; border-radius: 5px; border: 1px solid #ccc; box-shadow: 1px 1px 1px #999;"></textarea>
			<br>
			<input id="xml-sumbit" name="xml-sumbit" type="submit" value="Submit" style="margin-top: 20px; background-color: white; border-radius: 13px; padding: 8px; font-size: 18px; border: 3px solid #53c7b7;">
		</form>
	</div>
	';
	?>
	<?php
	if (isset($_POST['xml-sumbit']) && !empty($_POST["xml-text"])) {
		$xml = simplexml_load_string($_POST["xml-text"]) or die("Error: Cannot create object");
		// Values
		$apikey = '8r304981231wrt89c020c9rr7wrt89tr';
		$password = '3HGh@P9qAnc$';
		$output = 'advanced';
		if(!empty($xml->Order[1]->city)){
		  $array = array();
		  $array2 = array();
		  $i = 0;
		  $k = 0;
		  $completexml = '';
		  for($i = 0; $i <= 50; $i++) {
			if(empty($xml->Order[$i]->Order_Id)){
			break;
			}
			$orderid = $xml->Order[$i]->Order_Id;
			$email = $xml->Order[$i]->email;
			$phone = $xml->Order[$i]->phone;
			$name = $xml->Order[$i]->name;
			$country = 1;
			$city = $xml->Order[$i]->city;
			$postcode = $xml->Order[$i]->postalcode;
			$address = $xml->Order[$i]->address;
			$phone = $xml->Order[$i]->phone;
			$completexml .= <<<XML
			<Order>
				<email>$email</email>
				<apikey>$apikey</apikey>
				<output>$output</output>
				<name>$name</name>
				<address>$address</address>
				<postalcode>$postcode</postalcode>
				<city>$city</city>
				<country>$country</country>
				<Order_Id>$orderid</Order_Id>
				<phone>$phone</phone>
				<Products>
			XML;
			  for($k = 0; $k <= 50; $k++) {
			  if(empty($xml->Order[$i]->Products->Product[$k]->Product_Id)){
				break;
			  }
			  $productid = $xml->Order[$i]->Products->Product[$k]->Product_Id;
			  $artnr = $xml->Order[$i]->Products->Product[$k]->artnr;
			  $title = $xml->Order[$i]->Products->Product[$k]->title;
			  $price = $xml->Order[$i]->Products->Product[$k]->Price;
			  $completexml .= <<<XML
				<Product>
				<Product_Id>$productid</Product_Id>
				<artnr>$artnr</artnr>
				<title>$title</title>
				<Price>$price</Price>
				</Product>
			XML;
			}
			$completexml .= <<<XML
				</Products>
			  </Order>
			XML;
			// CURL
			$password = '3HGh@P9qAnc$';
			$apiurl = 'https://www.one-dc.com/ao/';
			if(!function_exists('curl_init')){
			die('You do not have the cURL functions installed! Ask your host for more info.');
			} else {
		  
			// Send the XML request
			$postfields = 'data='.$completexml;
			$ch = curl_init($apiurl);
			curl_setopt($ch,CURLOPT_HEADER,0);
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postfields);
			$result = curl_exec($ch);
			curl_close($ch);
		  
			if($ch === false || $result === false){
			  die('There was a problem with the connection to EDC');
			} else {
			  $json = json_decode($result,true);
		  
			  // Success
			  if($json['result'] == 'OK'){
		  
			  echo '
			  <div class="success" style="width: 500px; background-color: green; padding: 30px; margin-left: 30px; margin-bottom: 10px; color: white; font-size: 17px; border-radius: 20px;">
				Success! XML has been sent through HTTP POST
			  </div>';
			  
			  // Failure
			  } else {
			  echo '<pre>';
			  echo 'There was a problem with the order request. The following output was received from EDC:';
			  print_r($json);
			  echo '</pre>';
			  }
			}
			}
			$completexml = '';
		  }
		}else{
		  $array = array();
		  $array2 = array();
		  $i = 0;
		  $k = 0;
		  $completexml = '';
		  $orderid = $xml->Order->Order_Id;
		  $email = $xml->Order->email;
		  $phone = $xml->Order->phone;
		  $name = $xml->Order->name;
		  $country = 1;
		  $city = $xml->Order->city;
		  $postcode = $xml->Order->postalcode;
		  $address = $xml->Order->address;
		  $phone = $xml->Order->phone;
		  $completexml .= <<<XML
			<Order>
			  <email>$email</email>
			  <apikey>$apikey</apikey>
			  <output>$output</output>
			  <name>$name</name>
			  <address>$address</address>
			  <postalcode>$postcode</postalcode>
			  <city>$city</city>
			  <country>$country</country>
			  <Order_Id>$orderid</Order_Id>
			  <phone>$phone</phone>
			  <Products>
			XML;
			for($k = 0; $k <= 50; $k++) {
			  if(empty($xml->Order->Products->Product[$k]->Product_Id)){
			  break;
			  }
			  $productid = $xml->Order->Products->Product[$k]->Product_Id;
			  $artnr = $xml->Order->Products->Product[$k]->artnr;
			  $title = $xml->Order->Products->Product[$k]->title;
			  $price = $xml->Order->Products->Product[$k]->Price;
			  $completexml .= <<<XML
			  <Product>
			  <Product_Id>$productid</Product_Id>
			  <artnr>$artnr</artnr>
			  <title>$title</title>
			  <Price>$price</Price>
			  </Product>
			XML;
			}
			$completexml .= <<<XML
				</Products>
			  </Order>
			XML;
			// CURL
			$password = '3HGh@P9qAnc$';
			$apiurl = 'https://www.one-dc.com/ao/';
			if(!function_exists('curl_init')){
			  die('You do not have the cURL functions installed! Ask your host for more info.');
			} else {
			  // Send the XML request
			  $postfields = 'data='.$completexml;
			  $ch = curl_init($apiurl);
			  curl_setopt($ch,CURLOPT_HEADER,0);
			  curl_setopt($ch,CURLOPT_POST,1);
			  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			  curl_setopt($ch,CURLOPT_POSTFIELDS,$postfields);
			  $result = curl_exec($ch);
			  curl_close($ch);
		  
			  if($ch === false || $result === false){
			  die('There was a problem with the connection to EDC');
			  } else {
			  $json = json_decode($result,true);
		  
			  // Success
			  if($json['result'] == 'OK'){
		  
				echo '
				<div class="success" style="width: 500px; background-color: green; padding: 30px; margin-left: 30px; margin-bottom: 10px; color: white; font-size: 17px; border-radius: 20px;">
				Success! XML has been sent through HTTP POST
				</div>';
				
			  // Failure
			  } else {
				echo '<pre>';
				echo 'There was a problem with the order request. The following output was received from EDC:';
				print_r($json);
				echo '</pre>';
			  }
			}
		  }
		}
		global $wpdb;
		$wpdb->update($wpdb->posts, array("post_status" => "trash"), array("post_status" => "wc-processing"));
	}
}

class WooCater extends WP_Widget {
 
    function __construct() {
		$widget_ops = array( 
			'classname' => 'woocater',
			'description' => 'Filter WooCommerce products by categories',
		);
        parent::__construct('woocater', 'WooCater', $widget_ops);
 
        add_action( 'widgets_init', function() {
            register_widget( 'WooCater' );
        });
 
    }
 
    public $args = array(
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div></div>'
    );
 
    public function widget( $args, $instance ) {
 
        echo $args['before_widget'];
		
		$orderby = 'name';
		$order = 'asc';
		$hide_empty = false ;
		$cat_args = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
		);
		
		$product_categories = get_terms( 'product_cat', $cat_args );
		
		if( !empty($product_categories) ){
			echo '<span class="widget-title shop-sidebar">Filter by Category</span>';
			echo '<div class="is-divider small"></div>';
			echo '<select name="cater-select" id="cater-select">';
			foreach ($product_categories as $key => $category) {
				echo "<option value='".get_term_link($category)."'>".$category->name."</option>";
			}
			echo '</select>';
			echo '<button type="button" id="cater-filter-button" style="border-radius: 99px; background-color: #666; float: left; font-size: .85em; color: white;">FILTER</button>';
			?>
			<script>
				document.getElementById("cater-filter-button").addEventListener("click", filterProduct);
				function filterProduct(event){
					location.assign(document.getElementById("cater-select").value);
					event.preventDefault();
				}
			</script>
			<?php
		}
 
        echo $args['after_widget'];
 
    }
 
    public function form( $instance ) {
		
    }
 
    public function update( $new_instance, $old_instance ) {

    }
 
}
$woocater = new WooCater();