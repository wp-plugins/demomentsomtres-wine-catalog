=== DeMomentSomTres Wine and Cheese Catalog ===
Contributors: marcqueralt
Tags: product portfolio, wines, cheese
Requires at least: 3.2.1
Tested up to: 3.9.1
Stable tag: trunk
License: GPLv2 or later

DeMomentSomTres Wine and Cheese Catalog shows your product portfolio in the website.

== Description ==

DeMomentSomTres Wine and Cheese Catalog helps to easily incorporate your catalog of wines and champagnes your winery or wine shop on the web.

You can get more information at [DeMomentSomTres Digital Marketing Agency](http://demomentsomtres.com/english/wordpress-plugins/demomentsomtres-wine-catalog/).

= Features =

* Required plugins management
* Specific post type product allowing to use all WordPress resources to present your product.
* Classification based on mark, region and type.
* Specific URL field to link to an external eCommerce site.
* Shortcode demomentsomtres-marks to show all marks in a post or page. Just insert `[demomentsomtres-marks cols=n]` where you want to show your marks and logos.
* Widgets: Many widgets have been added in order to show product related information. If showed in a single product they get information from the product. If not on a product, nothing is shown.
* Mark Description Widget: shows the description inserted on the mark taxonomy.
* Mark Logo Widget: shows the image linked to the mark taxonomy. 
* Product Name Widget: shows the current product name in a H1 tag.
* Product Sales URL: shows the external product URL if it is informed on the product.
* Products In Category: shows other products in the same mark. Can be shown as a list of texts or as an image.
* Translation Ready. Included Catalan language.
* Taxonomy Images 

We have used [Featured Image Widget](http://wordpress.org/plugins/featured-image-widget/) in some installs to show the featured product image in some installations.

= History & Raison d’être =
We were asked to build the [Castell d'Or winery's](http://www.castelldor.com/) website but it was a catalog and to achieve notoriety as the public and search engines.

So we construct a solution of discard eCommerce pure Prestashop WooCommerce on because, well, we propose the design was very much in line of a printed catalog.

For this reason we decided to build our own wine catalog component advantage of using external components like s8sideways to present the brand logo.

So we decided to build a profile of wine or champagne on a custom post type wordpress creating classifications (taxonomies) specific to group brands.

Widgets there were added according to the specific needs of the construction site to present brands in their own navigation brand, navigation to other brands ...

Then we had to incorporate some products link to the website where you can buy this wine and this was the origin of Product Sales URL widget.

== Installation ==

This plugin can be installed on any WordPress. It detects if any required plugin is not installed and manages the situation.

== Screenshots ==

1. The widgets marked on a product page from Castell d'Or winery.
2. Shortcode result in Castell d'Or winery's homepage.

== Frequent Asked Questions ==
=== What's the right syntax of the shortcode? ===
The right syntax is `[demomentsomtres-marks cols="n"]`.

=== I was using Sideways Simple Taxonomy Images what happens to my installation ===
If you where using this plugin, nothing happens. Old marks are compatible. However, new ones are only compatible with Categories Images plugin.

== Changelog ==
= 2.0 =
* Classe based development
* Additional shortcodes
* Categories Images integration
* Related posts and events
* Cheese specific requirements

= 1.4.2 =
* Improved documentation

= 1.4.1 =
* 3.9.1 compatibility

=1.4=
* New field to inform sales URL
* Widget to show sales URL

=1.3=
*Products in the mark widget adds text only version

=1.2.1=
*Bug: plugin name badly informed

=1.2=
*Products in the mark widget
*Mark Description widget
*Mark Logo widget
*Product Name Widget
